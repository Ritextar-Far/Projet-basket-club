<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $map = [
        'Team' => __DIR__ . '/Classes/Team.php',
        'Player' => __DIR__ . '/Classes/Player.php',
        'Staff' => __DIR__ . '/Classes/Staff.php',
        'OpposingClub' => __DIR__ . '/Classes/OpposingClub.php',
        'PlayerHasTeam' => __DIR__ . '/Classes/Player_has_team.php',
        'BasketballMatch' => __DIR__ . '/Classes/BasketballMatch.php',
        'PlayerRepository' => __DIR__ . '/Repositories/PlayerRepository.php',
        'StaffRepository' => __DIR__ . '/Repositories/StaffRepository.php',
        'TeamRepository' => __DIR__ . '/Repositories/TeamRepository.php',
        'BasketballMatchRepository' => __DIR__ . '/Repositories/BasketballMatchRepository.php',
        'OpposingClubRepository' => __DIR__ . '/Repositories/OpposingClubRepository.php',
        'PlayerHasTeamRepository' => __DIR__ . '/Repositories/PlayerHasTeamRepository.php',
        'PlayerRole' => __DIR__ . '/Enumérations/PlayerRole.php',
        'StaffRole' => __DIR__ . '/Enumérations/StaffRole.php',
    ];

    if (isset($map[$class])) {
        require_once $map[$class];
        return;
    }

    $candidates = [
        __DIR__ . '/Classes/' . $class . '.php',
        __DIR__ . '/Repositories/' . $class . '.php',
        __DIR__ . '/Enumérations/' . $class . '.php',
        __DIR__ . '/Classes/' . strtolower($class) . '.php',
        __DIR__ . '/Repositories/' . strtolower($class) . '.php',
        __DIR__ . '/Enumérations/' . strtolower($class) . '.php',
    ];

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            require_once $candidate;
            return;
        }
    }
});