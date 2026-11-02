<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\OpposingClub;
use App\Repositories\OpposingClubRepository;
use Respect\Validation\Validator as v;

class OpposingClubFormHandler
{
    private array $errors = [];

    public function __construct(private OpposingClubRepository $repository)
    {
    }

    public function handle(array $input): ?OpposingClub
    {
        $id = (int) ($input['id'] ?? 0);
        $data = [
            'name' => trim((string) ($input['name'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'city' => trim((string) ($input['city'] ?? '')),
        ];

        $rules = [
            'name' => [v::notEmpty()->length(1, 150), 'Le nom du club est obligatoire (150 caractères maximum).'],
            'address' => [v::notEmpty()->length(1, 255), 'L\'adresse est obligatoire (255 caractères maximum).'],
            'city' => [v::notEmpty()->length(1, 150), 'La ville est obligatoire (150 caractères maximum).'],
        ];

        $this->errors = [];
        foreach ($rules as $field => [$rule, $message]) {
            if (!$rule->validate($data[$field])) {
                $this->errors[$field] = $message;
            }
        }

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
            $club = new OpposingClub(0, $data['name'], $data['address'], $data['city']);
            $club->setId($this->repository->create($club));

            return $club;
        }

        $club
            ->setName($data['name'])
            ->setAddress($data['address'])
            ->setCity($data['city']);
        $this->repository->update($club);

        return $club;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
