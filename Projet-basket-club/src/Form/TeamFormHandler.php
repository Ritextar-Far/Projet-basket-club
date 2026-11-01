<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Team;
use App\Repositories\TeamRepository;

class TeamFormHandler
{
    private array $errors = [];

    public function __construct(private TeamRepository $repository)
    {
    }

    /**
     * Valide les données saisies puis crée ou met à jour l'équipe.
     * Retourne l'équipe enregistrée, ou null si les données sont invalides.
     */
    public function handle(array $input): ?Team
    {
        $this->errors = [];

        $id = (int) ($input['id'] ?? 0);
        $name = trim((string) ($input['name'] ?? ''));
        $logo = trim((string) ($input['logo'] ?? ''));

        if ($name === '') {
            $this->errors['name'] = 'Le nom de l\'équipe est obligatoire.';
        } elseif (mb_strlen($name) > 150) {
            $this->errors['name'] = 'Le nom de l\'équipe ne doit pas dépasser 150 caractères.';
        }

        if (mb_strlen($logo) > 255) {
            $this->errors['logo'] = 'Le nom du logo ne doit pas dépasser 255 caractères.';
        }

        $team = null;
        if ($id > 0) {
            $team = $this->repository->findById($id);
            if ($team === null) {
                $this->errors['id'] = 'Cette équipe n\'existe pas.';
            }
        }

        if ($this->errors !== []) {
            return null;
        }

        if ($team === null) {
            $team = new Team(0, $name, $logo);
            $team->setId($this->repository->create($team));

            return $team;
        }

        $team
            ->setName($name)
            ->setLogo($logo);
        $this->repository->update($team);

        return $team;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
