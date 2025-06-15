<?php
namespace Game\Character\Enums;
use Game\Character\Stats;
use Game\Traits\EnumExtender\EnumExtender;

enum Races: string {
    use EnumExtender;   
    case Angel     = "Magical tanks. High charm and magic defense. Glass bones, paper skin.";
    case Demon     = "Crit fiends with high offense, middling elsewhere, soul debt not included.";
    case Dwarf     = "Bearded bricks. Physical defense gods. Can’t dodge to save their ale.";
    case Elf       = "Dexterity freaks. Everything you’d expect from someone who judges your handwriting.";
    case Gnome     = "Brainy gremlins. They hit your firewall before they hit your face.";
    case Halfling  = "Chaos in a teacup. Lucky and slippery.";
    case Human     = "Bread sandwich. No surprises here.";
    case Orc       = "The “kick door in, ask questions never” archetype.";
    case Troll     = "Tanks with regeneration, dumb as rocks, might be literal rocks.";
    case Undead    = "Resist everything, charm of a wet sock.";
    case Vampire   = "Stylish and lethal, the bloodsucker’s choice for PvP.";
    case Default   = "None";

    /**         
     * A one-time use function used when the character is first created, which boosts the
     * stats according to the selected race. The boost is permanent and is added directly
     * to the stats of the character.
     * @param void
     * @return void
     */
    public function set_stat_adjust(Stats $character_stats): void {
        $stat_adjusters = $this->get_stat_adjust();
        
        foreach ($stat_adjusters as $key => $value) {
            $func = "set_$key";
            $character_stats->$func($value);
        }
    }
    
    private function get_stat_adjust(): array {
       return match ($this) {
            Races::Angel => [
                 'str' => 5,  'int' => 9,  'def' => 6,  'luck' => 4, 'chsm' => 10,
                'dext' => 6, 'sped' => 7, 'mdef' => 10, 'crit' => 3, 'dodg' => 5,
                'blck' => 6, 'accu' => 7, 'rsst' => 8,  'rgen' => 4
            ],

            Races::Demon => [
                 'str' => 9,  'int' => 7,  'def' => 7, 'luck' => 3, 'chsm' => 4,
                'dext' => 6, 'sped' => 7, 'mdef' => 4, 'crit' => 8, 'dodg' => 6,
                'blck' => 5, 'accu' => 8, 'rsst' => 7, 'rgen' => 4
            ],

            Races::Dwarf => [
                 'str' => 8,   'int' => 5,  'def' => 9, 'luck' => 3, 'chsm' => 4,
                'dext' => 5,  'sped' => 4, 'mdef' => 6, 'crit' => 4, 'dodg' => 3,
                'blck' => 10, 'accu' => 6, 'rsst' => 9, 'rgen' => 6
            ],

            Races::Elf => [
                 'str' => 4,   'int' => 8,  'def' => 5, 'luck' => 5, 'chsm' => 7,
                'dext' => 10, 'sped' => 9, 'mdef' => 9, 'crit' => 6, 'dodg' => 9,
                'blck' => 3,  'accu' => 9, 'rsst' => 6, 'rgen' => 4
            ],

            Races::Gnome => [
                 'str' => 3,  'int' => 10, 'def' => 4, 'luck' => 8, 'chsm' => 6,
                'dext' => 9, 'sped' => 8, 'mdef' => 7, 'crit' => 5, 'dodg' => 7,
                'blck' => 2, 'accu' => 9, 'rsst' => 6, 'rgen' => 4
            ],

            Races::Halfling => [
                 'str' => 4,  'int' => 6,  'def' => 5, 'luck' => 10, 'chsm' => 6,
                'dext' => 9, 'sped' => 9, 'mdef' => 6, 'crit' => 4,  'dodg' => 10,
                'blck' => 2, 'accu' => 8, 'rsst' => 5, 'rgen' => 3
            ],

            Races::Human => [
                 'str' => 6,  'int' => 6,  'def' => 6, 'luck' => 6, 'chsm' => 6,
                'dext' => 6, 'sped' => 6, 'mdef' => 6, 'crit' => 6, 'dodg' => 6,
                'blck' => 6, 'accu' => 6, 'rsst' => 6, 'rgen' => 6
            ],

            Races::Orc => [
                 'str' => 10, 'int' => 3,  'def' => 8, 'luck' => 2, 'chsm' => 2,
                'dext' => 5, 'sped' => 6, 'mdef' => 3, 'crit' => 6, 'dodg' => 4,
                'blck' => 9, 'accu' => 5, 'rsst' => 7, 'rgen' => 5
            ],

            Races::Troll => [
                 'str' => 9,  'int' => 2,  'def' => 9,  'luck'  => 1, 'chsm' => 1,
                'dext' => 4, 'sped' => 4, 'mdef' => 4,  'crit'  => 2, 'dodg' => 3,
                'blck' => 8, 'accu' => 4, 'rsst' => 10, 'rgen'  => 10
            ],

            Races::Undead => [
                 'str' => 6,  'int' => 5,  'def' => 7,  'luck'  => 2, 'chsm' => 1,
                'dext' => 5, 'sped' => 4, 'mdef' => 8,  'crit'  => 4, 'dodg' => 3,
                'blck' => 5, 'accu' => 6, 'rsst' => 10, 'rgen'  => 2
            ],

            Races::Vampire => [
                 'str' => 7,  'int' => 7,  'def' => 6, 'luck' => 5, 'chsm' => 9,
                'dext' => 8, 'sped' => 7, 'mdef' => 7, 'crit' => 8, 'dodg' => 6,
                'blck' => 5, 'accu' => 8, 'rsst' => 7, 'rgen' => 6
            ]
        };
    }
}