<?php

declare(strict_types=1);

class OpposingClubRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, address, city FROM opposing_club ORDER BY id'
        );

        return array_map(
            fn (array $row): OpposingClub => $this->hydrate($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?OpposingClub
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, address, city FROM opposing_club WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(OpposingClub $club): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO opposing_club (name, address, city)
             VALUES (:name, :address, :city)'
        );
        $statement->execute($this->parameters($club));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(OpposingClub $club): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE opposing_club
             SET name = :name, address = :address, city = :city
             WHERE id = :id'
        );
        $parameters = $this->parameters($club);
        $parameters['id'] = $club->getId();

        return $statement->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM opposing_club WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    private function hydrate(array $row): OpposingClub
    {
        return new OpposingClub(
            (int) $row['id'],
            $row['name'],
            $row['address'],
            $row['city']
        );
    }

    private function parameters(OpposingClub $club): array
    {
        return [
            'name' => $club->getName(),
            'address' => $club->getAddress(),
            'city' => $club->getCity(),
        ];
    }
}
