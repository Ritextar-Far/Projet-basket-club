<?php

namespace Public;

use App\Repositories\BasketballMatchRepository;
use App\Repositories\OpposingClubRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Security\CsrfToken;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfToken::isValid($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Suppression refusée, veuillez recharger la page.');
}

$repositories = [
    'team' => new TeamRepository($pdo),
    'player' => new PlayerRepository($pdo),
    'opposing_club' => new OpposingClubRepository($pdo),
    'match' => new BasketballMatchRepository($pdo),
];

$type = $_POST['type'] ?? '';
if (!isset($repositories[$type])) {
    http_response_code(400);
    exit('Type d\'élément inconnu.');
}

try {
    $repositories[$type]->delete((int) ($_POST['id'] ?? 0));
} catch (PDOException $exception) {
    header('Location: index.php?error=linked');
    exit;
}

header('Location: index.php?deleted=1');
exit;
