<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Player;
use App\Repositories\PlayerRepository;
use DateTime;

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
        $this->errors = [];

        $id = (int) ($input['id'] ?? 0);
        $firstName = trim((string) ($input['first_name'] ?? ''));
        $lastName = trim((string) ($input['last_name'] ?? ''));
        $birthDate = trim((string) ($input['birth_date'] ?? ''));
        $picture = trim((string) ($input['picture'] ?? ''));

        $this->validateName('first_name', 'Le prénom', $firstName);
        $this->validateName('last_name', 'Le nom', $lastName);
        $date = $this->validateBirthDate($birthDate);

        if (mb_strlen($picture) > 255) {
            $this->errors['picture'] = 'Le nom de la photo ne doit pas dépasser 255 caractères.';
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

        if ($player === null) {
            $player = new Player(0, $firstName, $lastName, $date, $picture);
            $player->setId($this->repository->create($player));

            return $player;
        }

        $player
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setBirthDate($date)
            ->setPicture($picture);
        $this->repository->update($player);

        return $player;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateName(string $field, string $label, string $value): void
    {
        if ($value === '') {
            $this->errors[$field] = $label . ' est obligatoire.';
        } elseif (mb_strlen($value) > 100) {
            $this->errors[$field] = $label . ' ne doit pas dépasser 100 caractères.';
        }
    }

    private function validateBirthDate(string $value): ?DateTime
    {
        if ($value === '') {
            $this->errors['birth_date'] = 'La date de naissance est obligatoire.';
            return null;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            $this->errors['birth_date'] = 'La date de naissance n\'est pas valide.';
            return null;
        }

        if ($date > new DateTime()) {
            $this->errors['birth_date'] = 'La date de naissance ne peut pas être dans le futur.';
            return null;
        }

        return $date;
    }
}
