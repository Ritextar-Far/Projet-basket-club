<?php

namespace Public;

use App\Form\OpposingClubFormHandler;
use App\Repositories\OpposingClubRepository;
use App\Security\CsrfToken;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new OpposingClubRepository($pdo);
$handler = new OpposingClubFormHandler($repository);
$errors = [];

// Édition : on récupère le club via le repository, qui renvoie un objet OpposingClub
$club = null;
if (isset($_GET['id'])) {
    $club = $repository->findById((int) $_GET['id']);
    if ($club === null) {
        http_response_code(404);
        exit('Club adverse introuvable.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Refuse l'envoi si le jeton du formulaire ne correspond pas à celui de la session
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
} elseif ($club !== null) {
    // Édition : on pré-remplit le formulaire avec les valeurs du club
    $values = [
        'name' => $club->getName(),
        'address' => $club->getAddress(),
        'city' => $club->getCity(),
    ];
} else {
    // Ajout : formulaire vide
    $values = [];
}

// Identifiant envoyé dans le champ caché : 0 pour un ajout, l'id du club pour une édition
$id = 0;
if ($club !== null) {
    $id = $club->getId();
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
    <title><?= $club ? 'Modifier un club adverse' : 'Ajouter un club adverse' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1><?= $club ? 'Modifier un club adverse' : 'Ajouter un club adverse' ?></h1>

    <?php if (isset($errors['id'])): ?>
        <p class="error"><?= e($errors['id']) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <p>
            <label for="name">Nom du club</label><br>
            <input type="text" id="name" name="name" maxlength="150" required
                   value="<?= e($values['name'] ?? '') ?>">
            <?php if (isset($errors['name'])): ?>
                <br><span class="error"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="address">Adresse</label><br>
            <input type="text" id="address" name="address" maxlength="255" required
                   value="<?= e($values['address'] ?? '') ?>">
            <?php if (isset($errors['address'])): ?>
                <br><span class="error"><?= e($errors['address']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="city">Ville</label><br>
            <input type="text" id="city" name="city" maxlength="150" required
                   value="<?= e($values['city'] ?? '') ?>">
            <?php if (isset($errors['city'])): ?>
                <br><span class="error"><?= e($errors['city']) ?></span>
            <?php endif; ?>
        </p>

        <button class="button" type="submit"><?= $club ? 'Enregistrer les modifications' : 'Ajouter le club' ?></button>
    </form>
</body>
</html>
