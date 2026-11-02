<?php

namespace Public;

use App\Repositories\BasketballMatchRepository;
use App\Repositories\OpposingClubRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Security\CsrfToken;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

$teams = (new TeamRepository($pdo))->findAll();
$players = (new PlayerRepository($pdo))->findAll();
$opposingClubs = (new OpposingClubRepository($pdo))->findAll();
$playedMatches = (new BasketballMatchRepository($pdo))->findPlayed();

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
    <title>Basket Club</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Projet Basket Club</h1>

    <?php if (isset($_GET['saved'])): ?>
        <p class="success">Les données ont bien été enregistrées.</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p class="success">L'élément a bien été supprimé.</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="error">Impossible de supprimer cet élément : il est utilisé dans un ou plusieurs matchs. Supprimez d'abord ces matchs.</p>
    <?php endif; ?>

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
                        <a class="button" href="team_details.php?id=<?= $team->getId() ?>">Détails</a>
                        <form method="post" action="delete.php">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="type" value="team">
                            <input type="hidden" name="id" value="<?= $team->getId() ?>">
                            <button class="button delete" type="submit">Supprimer</button>
                        </form>
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
                        <form method="post" action="delete.php">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="type" value="player">
                            <input type="hidden" name="id" value="<?= $player->getId() ?>">
                            <button class="button delete" type="submit">Supprimer</button>
                        </form>
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
                        <form method="post" action="delete.php">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="type" value="opposing_club">
                            <input type="hidden" name="id" value="<?= $club->getId() ?>">
                            <button class="button delete" type="submit">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="button add" href="opposing_club_form.php">Ajouter un club adverse</a>
    </section>

    <section>
        <h2>Matchs joués</h2>

        <?php if ($playedMatches === []): ?>
            <p>Aucun match joué pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($playedMatches as $match): ?>
                    <li>
                        <span class="info">
                            <strong>
                                <?= e($match->getTeam()->getName()) ?>
                                <?= $match->getTeamScore() ?> - <?= $match->getOpponentScore() ?>
                                <?= e($match->getOpposingClub()->getName()) ?>
                            </strong><br>
                            Le <?= $match->getDate()->format('d/m/Y à H\hi') ?>, à <?= e($match->getCity()) ?>
                        </span>
                        <a class="button" href="match_form.php?id=<?= $match->getId() ?>">Modifier</a>
                        <form method="post" action="delete.php">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="type" value="match">
                            <input type="hidden" name="id" value="<?= $match->getId() ?>">
                            <button class="button delete" type="submit">Supprimer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="button add" href="match_form.php">Ajouter un match</a>
    </section>
</body>
</html>
