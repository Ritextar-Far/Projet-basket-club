<?php

class BasketballMatch
{
    private int $id;
    private int $teamScore;
    private int $opponentScore;
    private DateTime $date;
    private string $city;
    private Team $team;
    private OpposingClub $opposingClub;

    public function __construct(
        int $id,
        int $teamScore,
        int $opponentScore,
        DateTime $date,
        string $city,
        team $team,
        OpposingClub $opposingClub
    ) {
        $this->id = $id;
        $this->teamScore = $teamScore;
        $this->opponentScore = $opponentScore;
        $this->date = $date;
        $this->city = $city;
        $this->team = $team;
        $this->opposingClub = $opposingClub;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }

    public function getTeamScore(): int { return $this->teamScore; }
    public function setTeamScore(int $teamScore): self { $this->teamScore = $teamScore; return $this; }

    public function getOpponentScore(): int { return $this->opponentScore; }
    public function setOpponentScore(int $opponentScore): self { $this->opponentScore = $opponentScore; return $this; }

    public function getDate(): DateTime { return $this->date; }
    public function setDate(DateTime $date): self { $this->date = $date; return $this; }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }

    public function getTeam(): team { return $this->team; }
    public function setTeam(team $team): self { $this->team = $team; return $this; }

    public function getOpposingClub(): OpposingClub { return $this->opposingClub; }
    public function setOpposingClub(OpposingClub $opposingClub): self { $this->opposingClub = $opposingClub; return $this; }
}