<?php

declare(strict_types=1);

class PlayerRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, first_name, last_name, birth_date, picture FROM player ORDER BY id'
        );

        return array_map(fn (array $row): player => $this->hydrate($row), $statement->fetchAll());
    }

    public function findById(int $id): ?player
    {
        $statement = $this->pdo->prepare(
            'SELECT id, first_name, last_name, birth_date, picture FROM player WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(player $player): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO player (first_name, last_name, birth_date, picture)
             VALUES (:first_name, :last_name, :birth_date, :picture)'
        );
        $statement->execute($this->parameters($player));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(player $player): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE player
             SET first_name = :first_name, last_name = :last_name,
                 birth_date = :birth_date, picture = :picture
             WHERE id = :id'
        );
        $parameters = $this->parameters($player);
        $parameters['id'] = $player->getId();

        return $statement->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM player WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    private function hydrate(array $row): player
    {
        return new player(
            (int) $row['id'],
            $row['first_name'],
            $row['last_name'],
            new DateTime($row['birth_date']),
            $row['picture']
        );
    }

    private function parameters(player $player): array
    {
        return [
            'first_name' => $player->getFirstName(),
            'last_name' => $player->getLastName(),
            'birth_date' => $player->getBirthDate()->format('Y-m-d'),
            'picture' => $player->getPicture(),
        ];
    }
}