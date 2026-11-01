<?php

namespace Public;

use App\Form\PlayerFormHandler;
use App\Repositories\PlayerRepository;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new PlayerRepository($pdo);
$handler = new PlayerFormHandler($repository);
$errors = [];

// Édition : on récupère le joueur via le repository, qui renvoie un objet Player
$player = null;
if (isset($_GET['id'])) {
    $player = $repository->findById((int) $_GET['id']);
    if ($player === null) {
        http_response_code(404);
        exit('Joueur introuvable.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saved = $handler->handle($_POST);

    if ($saved !== null) {
        header('Location: player_form.php?id=' . $saved->getId() . '&saved=1');
        exit;
    }

    $errors = $handler->getErrors();
    $values = $_POST;
} else {
    $values = [
        'first_name' => $player?->getFirstName() ?? '',
        'last_name' => $player?->getLastName() ?? '',
        'birth_date' => $player?->getBirthDate()->format('Y-m-d') ?? '',
        'picture' => $player?->getPicture() ?? '',
    ];
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $player ? 'Modifier un joueur' : 'Ajouter un joueur' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1><?= $player ? 'Modifier un joueur' : 'Ajouter un joueur' ?></h1>

    <?php if (isset($_GET['saved'])): ?>
        <p class="success">Le joueur a bien été enregistré.</p>
    <?php endif; ?>

    <?php if (isset($errors['id'])): ?>
        <p class="error"><?= e($errors['id']) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $player?->getId() ?? 0 ?>">

        <p>
            <label for="first_name">Prénom</label><br>
            <input type="text" id="first_name" name="first_name" maxlength="100" required
                   value="<?= e($values['first_name'] ?? '') ?>">
            <?php if (isset($errors['first_name'])): ?>
                <br><span class="error"><?= e($errors['first_name']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="last_name">Nom</label><br>
            <input type="text" id="last_name" name="last_name" maxlength="100" required
                   value="<?= e($values['last_name'] ?? '') ?>">
            <?php if (isset($errors['last_name'])): ?>
                <br><span class="error"><?= e($errors['last_name']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="birth_date">Date de naissance</label><br>
            <input type="date" id="birth_date" name="birth_date" required
                   value="<?= e($values['birth_date'] ?? '') ?>">
            <?php if (isset($errors['birth_date'])): ?>
                <br><span class="error"><?= e($errors['birth_date']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="picture">Photo (nom du fichier)</label><br>
            <input type="text" id="picture" name="picture" maxlength="255"
                   value="<?= e($values['picture'] ?? '') ?>">
            <?php if (isset($errors['picture'])): ?>
                <br><span class="error"><?= e($errors['picture']) ?></span>
            <?php endif; ?>
        </p>

        <button class="button" type="submit"><?= $player ? 'Enregistrer les modifications' : 'Ajouter le joueur' ?></button>
    </form>
</body>
</html>
