<?php

declare(strict_types = 1);

enum StaffRole: string
{
    case COACH = 'Coach';
    case ASSISTANT_COACH = 'assistant_coach';
    case KINESITHERAPEUTE = 'kinesitherapeute';

  

    public function label(): string
    {
        switch ($this) {
           case self::COACH:
              return 'Coach';

           case self::ASSISTANT_COACH:
              return 'assistant_coach';

           case self::KINESITHERAPEUTE: 
              return 'kinesitherapeutes';
        }
    }

}
