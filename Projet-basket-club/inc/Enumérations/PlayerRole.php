<?php

declare(strict_types = 1);

enum PlayerRole: string
{
    case MENEUR = 'meneur';
    case ARRIERE = 'arriere';
    case AILIER = 'ailier';
    case AILIER_FORT = 'ailier_fort';
    case PIVOT = 'pivot';

  

    public function label(): string
    {
        switch ($this) {
           case self::MENEUR:
              return 'Meneur';

           case self::ARRIERE:
              return 'Arriere';

           case self::AILIER: 
              return 'Ailier';

           case self::AILIER_FORT: 
              return 'Ailier fort';
              
           case self::PIVOT:
              return 'Pivot';
        }
    }
 

}
