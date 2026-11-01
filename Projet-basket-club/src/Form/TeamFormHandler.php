<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Team;
use App\Repositories\TeamRepository;
use Respect\Validation\Validator as v;

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
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'name' => trim((string) ($input['name'] ?? '')),
            'logo' => trim((string) ($input['logo'] ?? '')),
        ];

        // Pour chaque champ : la règle à respecter et le message si elle ne l'est pas
        $rules = [
            'name' => [v::notEmpty()->length(1, 150), 'Le nom de l\'équipe est obligatoire (150 caractères maximum).'],
            'logo' => [v::length(null, 255), 'Le nom du logo ne doit pas dépasser 255 caractères.'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
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
            $team = new Team(0, $data['name'], $data['logo']);
            $team->setId($this->repository->create($team));

            return $team;
        }

        $team
            ->setName($data['name'])
            ->setLogo($data['logo']);
        $this->repository->update($team);

        return $team;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
