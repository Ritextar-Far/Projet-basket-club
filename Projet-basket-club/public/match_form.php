<?php

namespace Public;

use App\Form\BasketballMatchFormHandler;
use App\Repositories\BasketballMatchRepository;
use App\Repositories\OpposingClubRepository;
use App\Repositories\TeamRepository;
use App\Security\CsrfToken;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new BasketballMatchRepository($pdo);
$teamRepository = new TeamRepository($pdo);
$opposingClubRepository = new OpposingClubRepository($pdo);
$handler = new BasketballMatchFormHandler($repository, $teamRepository, $opposingClubRepository);
$errors = [];

$teams = $teamRepository->findAll();
$opposingClubs = $opposingClubRepository->findAll();

$match = null;
if (isset($_GET['id'])) {
    $match = $repository->findById((int) $_GET['id']);
    if ($match === null) {
        http_response_code(404);
        exit('Match introuvable.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfToken::isValid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Formulaire invalide ou expiré, veuillez recharger la page.');
    }

    $saved = $handler->handle($_POST);

    if ($saved !== null) {
        header('Location: index.php?saved=1');
        exit;
    }

    $errors = $handler->getErrors();
    $values = $_POST;
} elseif ($match !== null) {
    $values = [
        'team_id' => $match->getTeam()->getId(),
        'opposing_club_id' => $match->getOpposingClub()->getId(),
        'team_score' => $match->getTeamScore(),
        'opponent_score' => $match->getOpponentScore(),
        'date' => $match->getDate()->format('Y-m-d\TH:i'),
        'city' => $match->getCity(),
    ];
} else {
    $values = [];
}

$id = 0;
if ($match !== null) {
    $id = $match->getId();
}

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
    <title><?= $match ? 'Modifier un match' : 'Ajouter un match' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1><?= $match ? 'Modifier un match' : 'Ajouter un match' ?></h1>

    <?php if (isset($errors['id'])): ?>
        <p class="error"><?= e($errors['id']) ?></p>
    <?php endif; ?>

    <?php if ($teams === [] || $opposingClubs === []): ?>
        <p class="error">Il faut d'abord ajouter au moins une équipe du club et un club adverse.</p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <p>
            <label for="team_id">Équipe du club</label><br>
            <select id="team_id" name="team_id" required>
                <option value="">-- Choisir une équipe --</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= $team->getId() ?>" <?= (string) $team->getId() === (string) ($values['team_id'] ?? '') ? 'selected' : '' ?>>
                        <?= e($team->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['team_id'])): ?>
                <br><span class="error"><?= e($errors['team_id']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="opposing_club_id">Club adverse</label><br>
            <select id="opposing_club_id" name="opposing_club_id" required>
                <option value="">-- Choisir un club adverse --</option>
                <?php foreach ($opposingClubs as $club): ?>
                    <option value="<?= $club->getId() ?>" <?= (string) $club->getId() === (string) ($values['opposing_club_id'] ?? '') ? 'selected' : '' ?>>
                        <?= e($club->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['opposing_club_id'])): ?>
                <br><span class="error"><?= e($errors['opposing_club_id']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="team_score">Score de l'équipe du club</label><br>
            <input type="number" id="team_score" name="team_score" min="0" max="300" required
                   value="<?= e((string) ($values['team_score'] ?? '')) ?>">
            <?php if (isset($errors['team_score'])): ?>
                <br><span class="error"><?= e($errors['team_score']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="opponent_score">Score du club adverse</label><br>
            <input type="number" id="opponent_score" name="opponent_score" min="0" max="300" required
                   value="<?= e((string) ($values['opponent_score'] ?? '')) ?>">
            <?php if (isset($errors['opponent_score'])): ?>
                <br><span class="error"><?= e($errors['opponent_score']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="date">Date et heure du match</label><br>
            <input type="datetime-local" id="date" name="date" required
                   max="<?= date('Y-m-d\TH:i') ?>"
                   value="<?= e($values['date'] ?? '') ?>">
            <?php if (isset($errors['date'])): ?>
                <br><span class="error"><?= e($errors['date']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="city">Ville du match</label><br>
            <input type="text" id="city" name="city" maxlength="150" required
                   value="<?= e($values['city'] ?? '') ?>">
            <?php if (isset($errors['city'])): ?>
                <br><span class="error"><?= e($errors['city']) ?></span>
            <?php endif; ?>
        </p>

        <button class="button" type="submit"><?= $match ? 'Enregistrer les modifications' : 'Ajouter le match' ?></button>
    </form>
</body>
</html>
