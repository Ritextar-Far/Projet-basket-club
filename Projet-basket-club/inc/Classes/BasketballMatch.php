<?php

class BasketballMatch
{
    private ?int $id = null;
    private int $teamScore = 0;
    private int $opponentScore = 0;
    private ?DateTime $date = null;
    private string $city = '';
    private ?Team $team = null;
    private ?OpposingClub $opposingClub = null;

    public function __construct(
        ?int $id = null,
        int $teamScore = 0,
        int $opponentScore = 0,
        ?DateTime $date = null,
        string $city = '',
        ?Team $team = null,
        ?OpposingClub $opposingClub = null
    ) {
        $this->id = $id;
        $this->teamScore = $teamScore;
        $this->opponentScore = $opponentScore;
        $this->date = $date;
        $this->city = $city;
        $this->team = $team;
        $this->opposingClub = $opposingClub;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTeamScore(): int { return $this->teamScore; }
    public function setTeamScore(int $teamScore): self { $this->teamScore = $teamScore; return $this; }

    public function getOpponentScore(): int { return $this->opponentScore; }
    public function setOpponentScore(int $opponentScore): self { $this->opponentScore = $opponentScore; return $this; }

    public function getDate(): ?DateTime { return $this->date; }
    public function setDate(?DateTime $date): self { $this->date = $date; return $this; }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }

    public function getTeam(): ?Team { return $this->team; }
    public function setTeam(?Team $team): self { $this->team = $team; return $this; }

    public function getOpposingClub(): ?OpposingClub { return $this->opposingClub; }
    public function setOpposingClub(?OpposingClub $opposingClub): self { $this->opposingClub = $opposingClub; return $this; }
}