<?php 

declare(strict_types=1);

class StaffRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, first_name, last_name, picture, `role` FROM staff ORDER BY id'
        );

        return array_map(fn (array $row): Staff => $this->hydrate($row), $statement->fetchAll());
    }

    public function findById(int $id): ?Staff
    {
        $statement = $this->pdo->prepare(
            'SELECT id, first_name, last_name, picture, `role` FROM staff WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(Staff $staff): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO staff (first_name, last_name, picture, `role`)
             VALUES (:first_name, :last_name, :picture, :role)'
        );
        $statement->execute($this->parameters($staff));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Staff $staff): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE staff
             SET first_name = :first_name, last_name = :last_name,
                 picture = :picture, `role` = :role
             WHERE id = :id'
        );
        $parameters = $this->parameters($staff);
        $parameters['id'] = $staff->getId();

        return $statement->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM staff WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    private function hydrate(array $row): Staff
    {
        return new Staff(
            (int) $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['picture'],
            StaffRole::from($row['role'])
        );
    }

    private function parameters(Staff $staff): array
    {
        return [
            'first_name' => $staff->getFirstName(),
            'last_name' => $staff->getLastName(),
            'picture' => $staff->getPicture(),
            'role' => $staff->getRole()->value,
        ];
    }
}