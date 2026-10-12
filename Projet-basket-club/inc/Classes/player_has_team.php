<?php


class PlayerHasTeam
{
    private player $player ;
    private team $team ;
    private PlayerRole $role;

    public function __construct(player $player, team $team, PlayerRole $role)
    {
        $this->player = $player;
        $this->team = $team;
        $this->role = $role;
    }

    public function getPlayer(): player { return $this->player; }
    public function setPlayer(player $player): self { $this->player = $player; return $this; }

    public function getTeam(): team { return $this->team; }
    public function setTeam(team $team): self { $this->team = $team; return $this; }

    public function getRole(): PlayerRole { return $this->role; }
    public function setRole(PlayerRole $role): self { $this->role = $role; return $this; }
}