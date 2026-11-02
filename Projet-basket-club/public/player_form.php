<?php

namespace Public;

use App\Form\PlayerFormHandler;
use App\Repositories\PlayerRepository;
use App\Security\CsrfToken;
use App\Service\ImageUploader;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new PlayerRepository($pdo);
// Les photos envoyées sont enregistrées dans le dossier public/images
$handler = new PlayerFormHandler($repository, new ImageUploader(__DIR__ . '/images'));
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
    // Refuse l'envoi si le jeton du formulaire ne correspond pas à celui de la session
    if (!CsrfToken::isValid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Formulaire invalide ou expiré, veuillez recharger la page.');
    }

    $saved = $handler->handle($_POST, $_FILES);

    if ($saved !== null) {
        header('Location: index.php?saved=1');
        exit;
    }

    $errors = $handler->getErrors();
    $values = $_POST;
} elseif ($player !== null) {
    // Édition : on pré-remplit le formulaire avec les valeurs du joueur
    $values = [
        'first_name' => $player->getFirstName(),
        'last_name' => $player->getLastName(),
        'birth_date' => $player->getBirthDate()->format('Y-m-d'),
    ];
} else {
    // Ajout : formulaire vide
    $values = [];
}

// Identifiant envoyé dans le champ caché : 0 pour un ajout, l'id du joueur pour une édition
$id = 0;
if ($player !== null) {
    $id = $player->getId();
}

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
    <title><?= $player ? 'Modifier un joueur' : 'Ajouter un joueur' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1><?= $player ? 'Modifier un joueur' : 'Ajouter un joueur' ?></h1>

    <?php if (isset($errors['id'])): ?>
        <p class="error"><?= e($errors['id']) ?></p>
    <?php endif; ?>

    <!-- enctype="multipart/form-data" est obligatoire pour pouvoir envoyer un fichier -->
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

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
            <label for="picture">Photo (JPG, PNG, GIF ou WEBP, 2 Mo maximum)</label><br>
            <?php if ($player !== null && $player->getPicture() !== ''): ?>
                <img class="preview" src="images/<?= e($player->getPicture()) ?>" alt="Photo actuelle"><br>
                <small>Laissez vide pour garder la photo actuelle.</small><br>
            <?php endif; ?>
            <input type="file" id="picture" name="picture" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (isset($errors['picture'])): ?>
                <br><span class="error"><?= e($errors['picture']) ?></span>
            <?php endif; ?>
        </p>

        <button class="button" type="submit"><?= $player ? 'Enregistrer les modifications' : 'Ajouter le joueur' ?></button>
    </form>
</body>
</html>
