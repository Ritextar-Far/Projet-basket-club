<?php

declare(strict_types=1);

namespace App\Form;

use App\Enumerations\PlayerRole;
use App\Model\PlayerHasTeam;
use App\Model\Team;
use App\Repositories\PlayerHasTeamRepository;
use App\Repositories\PlayerRepository;
use Respect\Validation\Validator as v;

class PlayerHasTeamFormHandler
{
    private array $errors = [];

    public function __construct(
        private PlayerHasTeamRepository $repository,
        private PlayerRepository $playerRepository
    ) {
    }

    public function handle(Team $team, array $input): ?PlayerHasTeam
    {
        $this->errors = [];

        $player = $this->playerRepository->findById((int) ($input['player_id'] ?? 0));
        if ($player === null) {
            $this->errors['player_id'] = 'Veuillez choisir un joueur.';
        }

        $roles = array_map(fn (PlayerRole $role): string => $role->value, PlayerRole::cases());
        $roleValue = (string) ($input['role'] ?? '');
        if (!v::in($roles)->validate($roleValue)) {
            $this->errors['role'] = 'Veuillez choisir un rôle.';
        }

        if ($this->errors !== []) {
            return null;
        }

        $role = PlayerRole::from($roleValue);
        $membership = $this->repository->findByPlayerAndTeam($player->getId(), $team->getId());

        if ($membership === null) {
            $membership = new PlayerHasTeam($player, $team, $role);
            $this->repository->create($membership);

            return $membership;
        }

        $membership->setRole($role);
        $this->repository->update($membership);

        return $membership;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
