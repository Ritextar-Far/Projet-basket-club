<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Team;
use App\Repositories\TeamRepository;
use App\Service\ImageUploader;
use Respect\Validation\Validator as v;

class TeamFormHandler
{
    private array $errors = [];

    public function __construct(
        private TeamRepository $repository,
        private ImageUploader $uploader
    ) {
    }

    /**
     * Valide les données saisies (et le logo envoyé) puis crée ou met à jour l'équipe.
     * Retourne l'équipe enregistrée, ou null si les données sont invalides.
     */
    public function handle(array $input, array $files): ?Team
    {
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'name' => trim((string) ($input['name'] ?? '')),
        ];

        // Pour chaque champ : la règle à respecter et le message si elle ne l'est pas
        $rules = [
            'name' => [v::notEmpty()->length(1, 150), 'Le nom de l\'équipe est obligatoire (150 caractères maximum).'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
        }

        // Logo : facultatif, mais si un fichier est choisi il doit être une image valide
        $logo = $files['logo'] ?? null;
        if ($this->uploader->hasFile($logo)) {
            $error = $this->uploader->validate($logo);
            if ($error !== null) {
                $this->errors['logo'] = $error;
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

        // Nom du logo : le nouveau si un fichier a été envoyé, sinon on garde l'actuel
        $logoName = '';
        if ($team !== null) {
            $logoName = $team->getLogo();
        }
        if ($this->uploader->hasFile($logo)) {
            $logoName = $this->uploader->save($logo);
        }

        if ($team === null) {
            $team = new Team(0, $data['name'], $logoName);
            $team->setId($this->repository->create($team));

            return $team;
        }

        $team
            ->setName($data['name'])
            ->setLogo($logoName);
        $this->repository->update($team);

        return $team;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
