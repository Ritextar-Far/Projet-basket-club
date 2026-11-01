<?php

namespace Public;

use App\Repositories\OpposingClubRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$teams = (new TeamRepository($pdo))->findAll();
$players = (new PlayerRepository($pdo))->findAll();
$opposingClubs = (new OpposingClubRepository($pdo))->findAll();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Basket Club</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Projet Basket Club</h1>

    <section>
        <h2>Équipes du club</h2>

        <?php if ($teams === []): ?>
            <p>Aucune équipe pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($teams as $team): ?>
                    <li>
                        <?php if ($team->getLogo() !== ''): ?>
                            <img src="images/<?= e($team->getLogo()) ?>" alt="Logo <?= e($team->getName()) ?>">
                        <?php endif; ?>
                        <span class="info"><?= e($team->getName()) ?></span>
                        <a class="button" href="team_form.php?id=<?= $team->getId() ?>">Modifier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="button add" href="team_form.php">Ajouter une équipe</a>
    </section>

    <section>
        <h2>Joueurs du club</h2>

        <?php if ($players === []): ?>
            <p>Aucun joueur pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($players as $player): ?>
                    <li>
                        <?php if ($player->getPicture() !== ''): ?>
                            <img src="images/<?= e($player->getPicture()) ?>" alt="Photo de <?= e($player->getFirstName() . ' ' . $player->getLastName()) ?>">
                        <?php endif; ?>
                        <span class="info"><?= e($player->getLastName()) ?> <?= e($player->getFirstName()) ?></span>
                        <a class="button" href="player_form.php?id=<?= $player->getId() ?>">Modifier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="button add" href="player_form.php">Ajouter un joueur</a>
    </section>

    <section>
        <h2>Clubs adverses</h2>

        <?php if ($opposingClubs === []): ?>
            <p>Aucun club adverse pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($opposingClubs as $club): ?>
                    <li>
                        <span class="info">
                            <strong><?= e($club->getName()) ?></strong><br>
                            <?= e($club->getAddress()) ?>, <?= e($club->getCity()) ?>
                        </span>
                        <a class="button" href="opposing_club_form.php?id=<?= $club->getId() ?>">Modifier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="button add" href="opposing_club_form.php">Ajouter un club adverse</a>
    </section>
</body>
</html>
