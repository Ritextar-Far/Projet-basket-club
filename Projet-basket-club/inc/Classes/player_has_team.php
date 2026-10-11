<?php

enum PlayerRole: string
{
    case MENEUR = 'meneur';
    case ARRIERE = 'arriere';
    case AILIER = 'ailier';
    case AILIER_FORT = 'ailier_fort';
    case PIVOT = 'pivot';

    public function label(): string
    {
        return match ($this) {
            self::MENEUR => 'Meneur',
            self::ARRIERE => 'Arriere',
            self::AILIER => 'Ailier',
            self::AILIER_FORT => 'Ailier fort',
            self::PIVOT => 'Pivot',
        };
    }
}

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