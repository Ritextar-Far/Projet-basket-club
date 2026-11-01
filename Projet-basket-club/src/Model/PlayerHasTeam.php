<?php

namespace App\Model;

use App\Enumerations\PlayerRole;

class PlayerHasTeam
{
    private Player $player;
    private Team $team;
    private PlayerRole $role;

    public function __construct(Player $player, Team $team, PlayerRole $role)
    {
        $this->player = $player;
        $this->team = $team;
        $this->role = $role;
    }

    public function getPlayer(): Player { return $this->player; }
    public function setPlayer(Player $player): self { $this->player = $player; return $this; }

    public function getTeam(): Team { return $this->team; }
    public function setTeam(Team $team): self { $this->team = $team; return $this; }

    public function getRole(): PlayerRole { return $this->role; }
    public function setRole(PlayerRole $role): self { $this->role = $role; return $this; }
}