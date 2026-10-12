<?php

declare(strict_types=1);

class BasketballMatchRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query($this->selectWithRelations() . ' ORDER BY m.id');

        return array_map(
            fn (array $row): BasketballMatch => $this->hydrate($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?BasketballMatch
    {
        $statement = $this->pdo->prepare(
            $this->selectWithRelations() . ' WHERE m.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(BasketballMatch $match): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO basketball_match
                (team_score, opponent_score, `date`, city, team_id, opposing_club_id)
             VALUES
                (:team_score, :opponent_score, :date, :city, :team_id, :opposing_club_id)'
        );
        $statement->execute($this->parameters($match));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(BasketballMatch $match): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE basketball_match
             SET team_score = :team_score, opponent_score = :opponent_score,
                 `date` = :date, city = :city, team_id = :team_id,
                 opposing_club_id = :opposing_club_id
             WHERE id = :id'
        );
        $parameters = $this->parameters($match);
        $parameters['id'] = $match->getId();

        return $statement->execute($parameters);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM basketball_match WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    private function selectWithRelations(): string
    {
        return 'SELECT
                    m.id, m.team_score, m.opponent_score, m.`date`, m.city,
                    t.id AS team_id, t.name AS team_name, t.logo AS team_logo,
                    c.id AS club_id, c.name AS club_name,
                    c.address AS club_address, c.city AS club_city
                FROM basketball_match m
                INNER JOIN team t ON t.id = m.team_id
                INNER JOIN opposing_club c ON c.id = m.opposing_club_id';
    }

    private function hydrate(array $row): BasketballMatch
    {
        return new BasketballMatch(
            (int) $row['id'],
            (int) $row['team_score'],
            (int) $row['opponent_score'],
            new DateTime($row['date']),
            $row['city'],
            new Team((int) $row['team_id'], $row['team_name'], $row['team_logo']),
            new OpposingClub(
                (int) $row['club_id'],
                $row['club_name'],
                $row['club_address'],
                $row['club_city']
            )
        );
    }

    private function parameters(BasketballMatch $match): array
    {
        return [
            'team_score' => $match->getTeamScore(),
            'opponent_score' => $match->getOpponentScore(),
            'date' => $match->getDate()->format('Y-m-d H:i:s'),
            'city' => $match->getCity(),
            'team_id' => $match->getTeam()->getId(),
            'opposing_club_id' => $match->getOpposingClub()->getId(),
        ];
    }
}
