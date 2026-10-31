<?php
require_once __DIR__ . '/../inc/Chargement.php';

$team = new Team(1, 'Los Angeles Lakers', 'logo-lakers.png');
$opposingClub = new OpposingClub(1, 'Golden State Warriors', '123 Main Street', 'San Francisco');
$coach = new Staff(1, 'Frank', 'Vogel', 'coach.jpg', StaffRole::COACH);
$player = new Player(1, 'LeBron', 'James', new DateTime('1984-12-30'), 'lebron.jpg');
$match = new BasketballMatch(
    1,
    110,
    104,
    new DateTime('2024-01-15 20:00:00'),
    'Los Angeles',
    $team,
    $opposingClub
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Basket Club</title>
</head>
<body>
    <h1>Projet Basket Club</h1>

    <h2>Équipe</h2>
    <p>Nom : <?= $team->getName() ?></p>
    <p>Logo : <?= $team->getLogo() ?></p>

    <h2>Joueur</h2>
    <p><?= $player->getFirstName() . ' ' . $player->getLastName() ?></p>

    <h2>Staff</h2>
    <p><?= $coach->getFirstName() . ' ' . $coach->getLastName() . ' - ' . $coach->getRole()->label() ?></p>

    <h2>Match</h2>
    <p><?= $team->getName() . ' ' . $match->getTeamScore() . ' - ' . $match->getOpponentScore() . ' ' . $opposingClub->getName() ?></p>
</body>
</html>
