<?php

namespace Data;

use App\Model\BasketballMatch;
use App\Model\OpposingClub;
use App\Model\Player;
use App\Model\Team;
use DateTime;

require_once __DIR__ . '/../src/Chargement.php';

$clubs = [
    'Los Angeles Lakers' => 'Los Angeles',
    'Golden State Warriors' => 'San Francisco',
    'Brooklyn Nets' => 'New York',
    'Milwaukee Bucks' => 'Milwaukee',
    'Los Angeles Clippers' => 'Los Angeles',
    'Houston Rockets' => 'Houston',
    'Dallas Mavericks' => 'Dallas',
    'Philadelphia 76ers' => 'Philadelphie',
    'Denver Nuggets' => 'Denver',
    'Miami Heat' => 'Miami',
];

$joueurs = [
    'James' => 'LeBron',
    'Curry' => 'Stephen',
    'Durant' => 'Kevin',
    'Antetokounmpo' => 'Giannis',
    'Leonard' => 'Kawhi',
    'Harden' => 'James',
    'Davis' => 'Anthony',
    'Doncic' => 'Luka',
    'Embiid' => 'Joel',
    'Jokic' => 'Nikola',
];

$teamIndex = 1;
foreach ($clubs as $nomEquipe => $ville) {
    $team = new Team($teamIndex, $nomEquipe, 'logo-' . strtolower(str_replace(' ', '-', $nomEquipe)) . '.png');
    $opposingClub = new OpposingClub($teamIndex, $nomEquipe, 'Rue du basket ' . $teamIndex, $ville);
    $nomJoueur = array_rand($joueurs);
    $prenomJoueur = $joueurs[$nomJoueur];
    $joueur = new Player(
        $teamIndex,
        $prenomJoueur,
        $nomJoueur,
        new DateTime('1995-01-01'),
        'photo-' . strtolower($prenomJoueur) . '.jpg'
    );

    $match = new BasketballMatch(
        $teamIndex,
        rand(80, 120),
        rand(80, 120),
        new DateTime('2023-01-' . rand(1, 31) . ' 20:00:00'),
        $ville,
        $team,
        $opposingClub
    );

    echo "Match $teamIndex : " . $match->getTeam()->getName() . " vs " . $match->getOpposingClub()->getName() . "<br>";
    echo "Score : " . $match->getTeamScore() . " - " . $match->getOpponentScore() . "<br>";
    echo "Date : " . $match->getDate()->format('Y-m-d') . "<br>";
    echo "City : " . $match->getCity() . "<br>";
    echo "Joueur : " . $joueur->getFirstName() . ' ' . $joueur->getLastName() . "<br><br>";

    $teamIndex++;
    if ($teamIndex > 10) {
        break;
    }
}
