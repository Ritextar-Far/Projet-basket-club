<?php

namespace Public;

use App\Form\TeamFormHandler;
use App\Repositories\TeamRepository;
use App\Security\CsrfToken;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$repository = new TeamRepository($pdo);
$handler = new TeamFormHandler($repository);
$errors = [];

// Édition : on récupère l'équipe via le repository, qui renvoie un objet Team
$team = null;
if (isset($_GET['id'])) {
    $team = $repository->findById((int) $_GET['id']);
    if ($team === null) {
        http_response_code(404);
        exit('Équipe introuvable.');
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
} elseif ($team !== null) {
    // Édition : on pré-remplit le formulaire avec les valeurs de l'équipe
    $values = [
        'name' => $team->getName(),
        'logo' => $team->getLogo(),
    ];
} else {
    // Ajout : formulaire vide
    $values = [];
}

// Identifiant envoyé dans le champ caché : 0 pour un ajout, l'id de l'équipe pour une édition
$id = 0;
if ($team !== null) {
    $id = $team->getId();
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
    <title><?= $team ? 'Modifier une équipe' : 'Ajouter une équipe' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <a class="button back" href="index.php">&larr; Retour à l'accueil</a>

    <h1><?= $team ? 'Modifier une équipe' : 'Ajouter une équipe' ?></h1>

    <?php if (isset($errors['id'])): ?>
        <p class="error"><?= e($errors['id']) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <p>
            <label for="name">Nom de l'équipe</label><br>
            <input type="text" id="name" name="name" maxlength="150" required
                   value="<?= e($values['name'] ?? '') ?>">
            <?php if (isset($errors['name'])): ?>
                <br><span class="error"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="logo">Logo (nom du fichier)</label><br>
            <input type="text" id="logo" name="logo" maxlength="255"
                   value="<?= e($values['logo'] ?? '') ?>">
            <?php if (isset($errors['logo'])): ?>
                <br><span class="error"><?= e($errors['logo']) ?></span>
            <?php endif; ?>
        </p>

        <button class="button" type="submit"><?= $team ? 'Enregistrer les modifications' : 'Ajouter l\'équipe' ?></button>
    </form>
</body>
</html>
