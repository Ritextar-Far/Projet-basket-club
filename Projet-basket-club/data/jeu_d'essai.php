<?php
require_once __DIR__ . '/../inc/Chargement.php';

$clubs = [
    'Los Angeles Lakers'     => 'Los Angeles',
    'Golden State Warriors'  => 'San Francisco',
    'Brooklyn Nets'          => 'New York',
    'Milwaukee Bucks'        => 'Milwaukee',
    'Los Angeles Clippers'   => 'Los Angeles',
    'Houston Rockets'        => 'Houston',
    'Dallas Mavericks'       => 'Dallas',
    'Philadelphia 76ers'     => 'Philadelphie',
    'Denver Nuggets'         => 'Denver',
    'Miami Heat'             => 'Miami'
];

$joueurs = [
    'LeBron James', 'Stephen Curry', 'Kevin Durant', 'Giannis Antetokounmpo','Kawhi Leonard', 'James Harden', 'Anthony Davis', 'Luka Doncic','Joel Embiid', 'Nikola Jokic'];

for ($i = 1; $i <= 10; $i++) {
    [$nomEquipe1, $nomEquipe2] = array_rand($clubs, 2);

    $match = new BasketballMatch();
    $match->setTeamScore(rand(80, 120));
    $match->setOpponentScore(rand(80, 120));
    $match->setDate(new DateTime('2023-01-' . rand(1, 31)));
    $match->setCity($clubs[$nomEquipe1]);
    $match->setTeam(new Team(null, $nomEquipe1));
    $match->setOpposingClub(new OpposingClub(null, $nomEquipe2));

    $nomCompletJoueur = $joueurs[array_rand($joueurs)];
    [$prenomJoueur, $nomJoueur] = array_pad(explode(' ', $nomCompletJoueur, 2), 2, '');
    $joueur = new Player(null, $prenomJoueur, $nomJoueur);

    echo "Match $i : " . $match->getTeam()->getName() . " vs " . $match->getOpposingClub()->getName() . "<br>";
    echo "Score : " . $match->getTeamScore() . " - " . $match->getOpponentScore() . "<br>";
    echo "Date : " . $match->getDate()->format('Y-m-d') . "<br>";
    echo "City : " . $match->getCity() . "<br>";
    echo "Joueur : " . $joueur->getFirstName() . ' ' . $joueur->getLastName() . "<br><br>";
}