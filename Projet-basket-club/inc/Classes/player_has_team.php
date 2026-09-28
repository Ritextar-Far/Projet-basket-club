<?php

class PlayerHasTeam
{
    private ?Player $player = null;
    private ?Team $team = null;
    private string $role = '';

    public function __construct(?Player $player = null, ?Team $team = null, string $role = '')
    {
        $this->player = $player;
        $this->team = $team;
        $this->role = $role;
    }

    public function getPlayer(): ?Player { return $this->player; }
    public function setPlayer(?Player $player): self { $this->player = $player; return $this; }

    public function getTeam(): ?Team { return $this->team; }
    public function setTeam(?Team $team): self { $this->team = $team; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
}