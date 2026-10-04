<?php 

declare(strict_types=1);

$fichiers = ['BasketballMatch', 'OpposingClub', 'player', 'player_has_team', 'staff', 'team'];

foreach ($fichiers as $nom) {
    $chemin = __DIR__ . '/Classes/' . $nom . '.php';

    if (is_file($chemin)) {
        require_once $chemin;
    }
}