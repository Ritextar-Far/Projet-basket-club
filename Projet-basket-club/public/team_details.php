<?php

namespace Public;

use App\Enumerations\PlayerRole;
use App\Form\PlayerHasTeamFormHandler;
use App\Repositories\PlayerHasTeamRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Security\CsrfToken;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new PlayerHasTeamRepository($pdo);
$playerRepository = new PlayerRepository($pdo);
$handler = new PlayerHasTeamFormHandler($repository, $playerRepository);
$errors = [];
$values = [];

// L'équipe concernée, récupérée via le repository qui renvoie un objet Team
$team = (new TeamRepository($pdo))->findById((int) ($_GET['id'] ?? 0));
if ($team === null) {
    http_response_code(404);
    exit('Équipe introuvable.');
}

$pageUrl = 'team_details.php?id=' . $team->getId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Refuse l'envoi si le jeton du formulaire ne correspond pas à celui de la session
    if (!CsrfToken::isValid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Formulaire invalide ou expiré, veuillez recharger la page.');
    }

    // Bouton « Retirer » : on supprime le joueur de cette équipe
    if (($_POST['action'] ?? '') === 'remove') {
        $repository->delete((int) ($_POST['player_id'] ?? 0), $team->getId());
        header('Location: ' . $pageUrl . '&removed=1');
        exit;
    }

    // Formulaire « Ajouter un joueur à l'équipe »
    if ($handler->handle($team, $_POST) !== null) {
        header('Location: ' . $pageUrl . '&saved=1');
        exit;
    }

    $errors = $handler->getErrors();
    $values = $_POST;
}

$memberships = $repository->findByTeam($team->getId());
$players = $playerRepository->findAll();

// Jeton CSRF récupéré avant tout affichage (la session doit démarrer avant le HTML)
$csrfToken = CsrfToken::get();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Équipe <?= e($team->getName()) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1>
        <?php if ($team->getLogo() !== ''): ?>
            <img src="images/<?= e($team->getLogo()) ?>" alt="Logo <?= e($team->getName()) ?>" width="60">
        <?php endif; ?>
        <?= e($team->getName()) ?>
    </h1>

    <?php if (isset($_GET['saved'])): ?>
        <p class="success">Le joueur a bien été ajouté à l'équipe.</p>
    <?php elseif (isset($_GET['removed'])): ?>
        <p class="success">Le joueur a bien été retiré de l'équipe.</p>
    <?php endif; ?>

    <section>
        <h2>Composition de l'équipe</h2>

        <?php if ($memberships === []): ?>
            <p>Aucun joueur dans cette équipe pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($memberships as $membership): ?>
                    <?php $player = $membership->getPlayer(); ?>
                    <li>
                        <?php if ($player->getPicture() !== ''): ?>
                            <img src="images/<?= e($player->getPicture()) ?>" alt="Photo de <?= e($player->getFirstName() . ' ' . $player->getLastName()) ?>">
                        <?php endif; ?>
                        <span class="info">
                            <strong><?= e($player->getLastName()) ?> <?= e($player->getFirstName()) ?></strong>
                            — <?= e($membership->getRole()->label()) ?>
                        </span>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="player_id" value="<?= $player->getId() ?>">
                            <button class="button" type="submit">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section>
        <h2>Ajouter un joueur à l'équipe</h2>
        <p>Si le joueur fait déjà partie de l'équipe, son rôle sera remplacé.</p>

        <?php if ($players === []): ?>
            <p class="error">Il faut d'abord ajouter au moins un joueur.</p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

            <p>
                <label for="player_id">Joueur</label><br>
                <select id="player_id" name="player_id" required>
                    <option value="">-- Choisir un joueur --</option>
                    <?php foreach ($players as $player): ?>
                        <option value="<?= $player->getId() ?>" <?= (string) $player->getId() === ($values['player_id'] ?? '') ? 'selected' : '' ?>>
                            <?= e($player->getLastName() . ' ' . $player->getFirstName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['player_id'])): ?>
                    <br><span class="error"><?= e($errors['player_id']) ?></span>
                <?php endif; ?>
            </p>

            <p>
                <label for="role">Rôle dans cette équipe</label><br>
                <select id="role" name="role" required>
                    <option value="">-- Choisir un rôle --</option>
                    <?php foreach (PlayerRole::cases() as $role): ?>
                        <option value="<?= e($role->value) ?>" <?= $role->value === ($values['role'] ?? '') ? 'selected' : '' ?>>
                            <?= e($role->label()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <br><span class="error"><?= e($errors['role']) ?></span>
                <?php endif; ?>
            </p>

            <button class="button" type="submit">Ajouter un joueur à l'équipe</button>
        </form>
    </section>
</body>
</html>
