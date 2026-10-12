<?php

declare(strict_types=1);

class PlayerHasTeamRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query($this->selectWithRelations() . ' ORDER BY pt.player_id, pt.team_id');

        return array_map(
            fn (array $row): PlayerHasTeam => $this->hydrate($row),
            $statement->fetchAll()
        );
    }

    public function findByPlayerAndTeam(int $playerId, int $teamId): ?PlayerHasTeam
    {
        $statement = $this->pdo->prepare(
            $this->selectWithRelations() . ' WHERE pt.player_id = :player_id AND pt.team_id = :team_id'
        );
        $statement->execute(['player_id' => $playerId, 'team_id' => $teamId]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(PlayerHasTeam $membership): bool
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO player_has_team (player_id, team_id, `role`)
             VALUES (:player_id, :team_id, :role)'
        );

        return $statement->execute($this->parameters($membership));
    }

    public function update(PlayerHasTeam $membership): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE player_has_team
             SET `role` = :role
             WHERE player_id = :player_id AND team_id = :team_id'
        );

        return $statement->execute($this->parameters($membership));
    }

    public function delete(int $playerId, int $teamId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM player_has_team WHERE player_id = :player_id AND team_id = :team_id'
        );

        return $statement->execute(['player_id' => $playerId, 'team_id' => $teamId]);
    }

    private function selectWithRelations(): string
    {
        return 'SELECT
                    pt.`role`, p.id AS player_id, p.first_name, p.last_name,
                    p.birth_date, p.picture, t.id AS team_id,
                    t.name AS team_name, t.logo AS team_logo
                FROM player_has_team pt
                INNER JOIN player p ON p.id = pt.player_id
                INNER JOIN team t ON t.id = pt.team_id';
    }

    private function hydrate(array $row): PlayerHasTeam
    {
        return new PlayerHasTeam(
            new Player(
                (int) $row['player_id'],
                $row['first_name'],
                $row['last_name'],
                new DateTime($row['birth_date']),
                $row['picture']
            ),
            new Team((int) $row['team_id'], $row['team_name'], $row['team_logo']),
            PlayerRole::from($row['role'])
        );
    }

    private function parameters(PlayerHasTeam $membership): array
    {
        return [
            'player_id' => $membership->getPlayer()->getId(),
            'team_id' => $membership->getTeam()->getId(),
            'role' => $membership->getRole()->value,
        ];
    }
}
