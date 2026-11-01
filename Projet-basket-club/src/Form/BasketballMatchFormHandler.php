<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\BasketballMatch;
use App\Repositories\BasketballMatchRepository;
use App\Repositories\OpposingClubRepository;
use App\Repositories\TeamRepository;
use DateTime;
use Respect\Validation\Validator as v;

class BasketballMatchFormHandler
{
    private array $errors = [];

    public function __construct(
        private BasketballMatchRepository $repository,
        private TeamRepository $teamRepository,
        private OpposingClubRepository $opposingClubRepository
    ) {
    }

    /**
     * Valide les données saisies puis crée ou met à jour le match.
     * Retourne le match enregistré, ou null si les données sont invalides.
     */
    public function handle(array $input): ?BasketballMatch
    {
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'team_score' => trim((string) ($input['team_score'] ?? '')),
            'opponent_score' => trim((string) ($input['opponent_score'] ?? '')),
            'date' => trim((string) ($input['date'] ?? '')),
            'city' => trim((string) ($input['city'] ?? '')),
        ];

        // Pour chaque champ : la règle à respecter et le message si elle ne l'est pas
        // (format de la date envoyée par un champ <input type="datetime-local"> : 2026-11-15T20:30)
        $rules = [
            'team_score' => [v::intVal()->between(0, 300), 'Le score de l\'équipe doit être un nombre entre 0 et 300.'],
            'opponent_score' => [v::intVal()->between(0, 300), 'Le score de l\'adversaire doit être un nombre entre 0 et 300.'],
            'date' => [v::dateTime('Y-m-d\TH:i')->max(date('Y-m-d\TH:i')), 'La date du match doit être une date valide et passée.'],
            'city' => [v::notEmpty()->length(1, 150), 'La ville est obligatoire (150 caractères maximum).'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
        }

        // L'équipe et le club choisis doivent exister en base
        $team = $this->teamRepository->findById((int) ($input['team_id'] ?? 0));
        if ($team === null) {
            $this->errors['team_id'] = 'Veuillez choisir une équipe du club.';
        }

        $opposingClub = $this->opposingClubRepository->findById((int) ($input['opposing_club_id'] ?? 0));
        if ($opposingClub === null) {
            $this->errors['opposing_club_id'] = 'Veuillez choisir un club adverse.';
        }

        $match = null;
        if ($id > 0) {
            $match = $this->repository->findById($id);
            if ($match === null) {
                $this->errors['id'] = 'Ce match n\'existe pas.';
            }
        }

        if ($this->errors !== []) {
            return null;
        }

        $teamScore = (int) $data['team_score'];
        $opponentScore = (int) $data['opponent_score'];
        $date = new DateTime($data['date']);

        if ($match === null) {
            $match = new BasketballMatch(0, $teamScore, $opponentScore, $date, $data['city'], $team, $opposingClub);
            $match->setId($this->repository->create($match));

            return $match;
        }

        $match
            ->setTeamScore($teamScore)
            ->setOpponentScore($opponentScore)
            ->setDate($date)
            ->setCity($data['city'])
            ->setTeam($team)
            ->setOpposingClub($opposingClub);
        $this->repository->update($match);

        return $match;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
