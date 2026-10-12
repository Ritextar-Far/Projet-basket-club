<?php 

declare(strict_types=1);

require_once __DIR__ . '/Enumérations/StaffRole.php';
require_once __DIR__ . '/Enumérations/PlayerRole.php';

require_once __DIR__ . '/Classes/team.php';
require_once __DIR__ . '/Classes/player.php';
require_once __DIR__ . '/Classes/staff.php';
require_once __DIR__ . '/Classes/OpposingClub.php';
require_once __DIR__ . '/Classes/player_has_team.php';
require_once __DIR__ . '/Classes/BasketballMatch.php';

require_once __DIR__ . '/Repositories/PlayerRepository.php';
require_once __DIR__ . '/Repositories/StaffRepository.php';
require_once __DIR__ . '/Repositories/TeamRepository.php';
require_once __DIR__ . '/Repositories/BasketballMatchRepository.php';
require_once __DIR__ . '/Repositories/OpposingClubRepository.php';
require_once __DIR__ . '/Repositories/PlayerHasTeamRepository.php';