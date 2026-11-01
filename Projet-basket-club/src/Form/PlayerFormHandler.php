<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Player;
use App\Repositories\PlayerRepository;
use DateTime;
use Respect\Validation\Validator as v;

class PlayerFormHandler
{
    private array $errors = [];

    public function __construct(private PlayerRepository $repository)
    {
    }

    /**
     * Valide les données saisies puis crée ou met à jour le joueur.
     * Retourne le joueur enregistré, ou null si les données sont invalides.
     */
    public function handle(array $input): ?Player
    {
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'first_name' => trim((string) ($input['first_name'] ?? '')),
            'last_name' => trim((string) ($input['last_name'] ?? '')),
            'birth_date' => trim((string) ($input['birth_date'] ?? '')),
            'picture' => trim((string) ($input['picture'] ?? '')),
        ];

        // Pour chaque champ : la règle à respecter et le message si elle ne l'est pas
        $rules = [
            'first_name' => [v::notEmpty()->length(1, 100), 'Le prénom est obligatoire (100 caractères maximum).'],
            'last_name' => [v::notEmpty()->length(1, 100), 'Le nom est obligatoire (100 caractères maximum).'],
            'birth_date' => [v::date('Y-m-d')->max(date('Y-m-d')), 'La date de naissance doit être une date valide et passée.'],
            'picture' => [v::length(null, 255), 'Le nom de la photo ne doit pas dépasser 255 caractères.'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
        }

        $player = null;
        if ($id > 0) {
            $player = $this->repository->findById($id);
            if ($player === null) {
                $this->errors['id'] = 'Ce joueur n\'existe pas.';
            }
        }

        if ($this->errors !== []) {
            return null;
        }

        $birthDate = new DateTime($data['birth_date']);

        if ($player === null) {
            $player = new Player(0, $data['first_name'], $data['last_name'], $birthDate, $data['picture']);
            $player->setId($this->repository->create($player));

            return $player;
        }

        $player
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setBirthDate($birthDate)
            ->setPicture($data['picture']);
        $this->repository->update($player);

        return $player;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
