<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Player;
use App\Repositories\PlayerRepository;
use App\Service\ImageUploader;
use DateTime;
use Respect\Validation\Validator as v;

class PlayerFormHandler
{
    private array $errors = [];

    public function __construct(
        private PlayerRepository $repository,
        private ImageUploader $uploader
    ) {
    }

    public function handle(array $input, array $files): ?Player
    {
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'first_name' => trim((string) ($input['first_name'] ?? '')),
            'last_name' => trim((string) ($input['last_name'] ?? '')),
            'birth_date' => trim((string) ($input['birth_date'] ?? '')),
        ];

        $rules = [
            'first_name' => [v::notEmpty()->length(1, 100), 'Le prénom est obligatoire (100 caractères maximum).'],
            'last_name' => [v::notEmpty()->length(1, 100), 'Le nom est obligatoire (100 caractères maximum).'],
            'birth_date' => [v::date('Y-m-d')->max(date('Y-m-d')), 'La date de naissance doit être une date valide et passée.'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
        }

        $picture = $files['picture'] ?? null;
        if ($this->uploader->hasFile($picture)) {
            $error = $this->uploader->validate($picture);
            if ($error !== null) {
                $this->errors['picture'] = $error;
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

        $pictureName = '';
        if ($player !== null) {
            $pictureName = $player->getPicture();
        }
        if ($this->uploader->hasFile($picture)) {
            $pictureName = $this->uploader->save($picture);
        }

        $birthDate = new DateTime($data['birth_date']);

        if ($player === null) {
            $player = new Player(0, $data['first_name'], $data['last_name'], $birthDate, $pictureName);
            $player->setId($this->repository->create($player));

            return $player;
        }

        $player
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setBirthDate($birthDate)
            ->setPicture($pictureName);
        $this->repository->update($player);

        return $player;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
