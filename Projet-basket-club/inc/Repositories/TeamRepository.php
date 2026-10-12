<?php

declare(strict_types=1);

class TeamRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, logo FROM team ORDER BY id'
        );

        return array_map(fn (array $row): Team => $this->hydrate($row), $statement->fetchAll());
    }

    public function findById(int $id): ?Team
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, logo FROM team WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(Team $team): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO team (name, logo)
             VALUES (:name, :logo)'
        );
        $statement->execute($this->parameters($team));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Team $team): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE team
             SET name = :name, logo = :logo
             WHERE id = :id'
        );
        $parameters = $this->parameters($team);
        $parameters['id'] = $team->getId();

        return $statement->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM team WHERE id = :id');

        return $statement->execute(['id' => $id]);

    }

    private function hydrate(array $row): Team
    {
        return new Team(
            (int) $row['id'],
            $row['name'],
            $row['logo']
        );
    }

    private function parameters(Team $team): array
    {
        return [
            'name' => $team->getName(),
            'logo' => $team->getLogo(),
        ];
    }
}