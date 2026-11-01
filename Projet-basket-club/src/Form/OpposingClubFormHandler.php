<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\OpposingClub;
use App\Repositories\OpposingClubRepository;

class OpposingClubFormHandler
{
    private array $errors = [];

    public function __construct(private OpposingClubRepository $repository)
    {
    }

    /**
     * Valide les données saisies puis crée ou met à jour le club adverse.
     * Retourne le club enregistré, ou null si les données sont invalides.
     */
    public function handle(array $input): ?OpposingClub
    {
        $this->errors = [];

        $id = (int) ($input['id'] ?? 0);
        $name = trim((string) ($input['name'] ?? ''));
        $address = trim((string) ($input['address'] ?? ''));
        $city = trim((string) ($input['city'] ?? ''));

        $this->validateText('name', 'Le nom du club', $name, 150);
        $this->validateText('address', 'L\'adresse', $address, 255);
        $this->validateText('city', 'La ville', $city, 150);

        $club = null;
        if ($id > 0) {
            $club = $this->repository->findById($id);
            if ($club === null) {
                $this->errors['id'] = 'Ce club n\'existe pas.';
            }
        }

        if ($this->errors !== []) {
            return null;
        }

        if ($club === null) {
            $club = new OpposingClub(0, $name, $address, $city);
            $club->setId($this->repository->create($club));

            return $club;
        }

        $club
            ->setName($name)
            ->setAddress($address)
            ->setCity($city);
        $this->repository->update($club);

        return $club;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateText(string $field, string $label, string $value, int $maxLength): void
    {
        if ($value === '') {
            $this->errors[$field] = $label . ' est obligatoire.';
        } elseif (mb_strlen($value) > $maxLength) {
            $this->errors[$field] = $label . ' ne doit pas dépasser ' . $maxLength . ' caractères.';
        }
    }
}
