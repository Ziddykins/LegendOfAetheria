<?php
    define('MAX_STARTING_INVWEIGHT', 500);
    define('MAX_STARTING_INVSLOTS',   30);
    define('MAX_ASSIGNABLE_AP',       40);
    define('REGEN_PER_TICK',           3);
    
    define('ROOT_WEB_DIRECTORY', '/var/www/html/kali.local/loa/');
    
    enum ModalButtonType {
        case YesNo;
        case Close;
        case OKCancel;
    }

    enum Races {
        public static function random(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[rand(0, $count)];
        }
        
        case Angel;
        case Demon;
        case Dwarf;
        case Elf;
        case Gnome;
        case Halfling;
        case Human;
        case Orc;
        case Troll;
        case Undead;
        case Vampire;
        case Default;
    }

    enum UserPrivileges: int {
        public static function name_to_value(string $name): string {
            foreach (self::cases() as $privilege) {
                if ($name === $privilege->name){
                    return $privilege->value;
                }
            }
            throw new \ValueError("$name is not a valid backing value for enum " . self::class);
        }
        
        public static function name_to_enum(string $name) {
            foreach (self::cases() as $privilege) {
                if ($name === $privilege->name){
                    return $privilege;
                }
            }
            throw new \ValueError("$name is not a valid backing value for enum " . self::class);
        }

        case BANNED = 1;
        case MUTED = 2;
        case UNREGISTERED = 4;
        case UNVERIFIED = 8;
        case USER = 16;
        case MODERATOR = 32;
        case SUPER_MODERATOR = 64;
        case ADMINISTRATOR = 128;
        case GLOBAL_ADMINISTRATOR = 256;
        case OWNER = 512;
        case ROOTED = 1024;
    }

    enum Weather {
        public static function random(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[rand(0, $count)];
        }

        public static function name_to_value(string $name): string {
            foreach (self::cases() as $weather) {
                if ($name === $weather->name){
                    return $weather->value;
                }
            }
            throw new \ValueError("$name is not a valid backing value for enum " . self::class);
        }
  
        public function icon(): string {
            return match($this) {
                Weather::CLOUDY =>  '<span class="text-info-emphasis"><i class="bi bi-cloud-fill p-2"></i>',
                Weather::SUNNY  =>  '<span class="text-warning"><i class="bi bi-brightness-high-fill p-2"></i>',
                Weather::HAILING => '<span class="text-primary-emphasis"><i class="bi bi-cloud-hail-fill p-2"></i>',
                Weather::SNOWING => '<span class="text-white"><i class="bi bi-cloud-snow-fill p-2"></i>',
                Weather::RAINING => '<span class="text-primary"><i class="bi bi-cloud-lightning-rain-fill p-2"></i>'
            };
        }

        case SUNNY;
        case RAINING;
        case HAILING;
        case CLOUDY;
        case SNOWING;
    }

    enum FriendStatus {
        case MUTUAL;
        case REQUESTED;
        case REQUEST;
        case NONE;
        case BLOCKED;
        case BLOCKED_BY;
    }

    # Categories:
    #   - FUNCT - Relating to the usage of a function
    #   - SQLDB - Directly relating to the database, i.e. invalid
    #             prepare statements or connection issues
    #   - FRNDS - Well, friend related issues
    #   - MAIL  - Mail related issues
    #   - CRON  - Cron related issues
    #   - CHAR  - Character related issues
    enum LOAError: int {
        case FUNCT_DOSQL_INVALIDACTION = -1000;
        case FUNCT_GENCOMP_UNKNOWN     = -1001;
        case FUNCT_PROPSYNC_TYPE       = -1002;
        
        case SQLDB_NOCONNECTION        = -2000;
        case SQLDB_PREPPED_EXECUTE     = -2001;
        case SQLDB_UNKNOWN_SAVE_TYPE   = -2002;

        case FRNDS_FRIEND_STATUS_ERROR = -3000;

        case MAIL_UNKNOWN_DIRECTIVE    = -4000;
        case MAIL_ALREADY_BLOCKED      = -4001;
        
        case CRON_HTTP_DIRECT_ACCESS   = -5000;

        case CHAR_MAX_CHAR_COUNT       = -6000;
    }

    # Global:
    #   - Global monsters are available for everyone to attack and will pop up occasionally
    #     Expeience and gold, as well as items will be based on damage contribution
    #   - Zone monsters are a bit less powerful, but are restricted to zones (maps); any
    #     players in this area will be able to contribute. Leaving the area forefits contribution
    #   - Personal monsters are only visible and attackable by you
    enum MonsterScope {
        case GLOBAL;
        case ZONE;
        case PERSONAL;
    }
    
    enum MonsterClass {
        case GIANT;       /* 2-3x HP */
        case MINI;        /* 1/2 - 1/4 HP */
        case HARDENED;    /* 3-4x DEF */
        case ENRAGED;     /* 4x STR, -5% HP */
        case EXPERIENCED; /* 3x EXP, +2% HP+DEF */
        case PACKRAT;     /* Item rarity chances increased/1.5x, 3x gold */
        case DEFECTING;   /* Chance to spare life/let join your army */
        case LEADER;      /* 5-10x all stats */
    }

    enum ObjectRarity: string {
        public static function getObjectRarity($roll) {
            foreach (self::cases() as $rarity) {
                if ($roll >= $rarity->value) {
                    return $rarity;
                }
            }
        }

        case WORTHLESS = "50.0";  /* 50.00% chance */
        case TARNISHED = "30.0";  /* 20.00% chance */
        case COMMON    = "20.0";  /* 10.00% chance */
        case ENCHANTED = "12.0";  /*  8.00% chance */
        case MAGICAL   = "8.0";  /*  4.00% chance */
        case LEGENDARY = "5.0";  /*  3.00% chance */
        case EPIC      = "2.50";  /*  2.50% chance */
        case MYSTIC    = "1.50";  /*  1.00% chance */
        case HEROIC    = "0.75";  /*  0.75% chance */
        case INFAMOUS  = "0.24";  /*  0.51% chance */
        case GODLY     = "0.01";  /*  0.23% chance */
        case NONE      = "0.00";  /* Used to determine if player has egg yet */
    }

    enum Components {
        case FLOATING_LABEL_TEXTBOX;
    }

    enum AbuseTypes {
        case CHEATING;    /* General cheating/abuse of game mechanics etc. */
        case AUTOBOTTING; /* Using autoclickers to play for you */
        case MULTISIGNUP; /* Abusing the signup form/multi-characters */
        case POSTMODIFY;  /* Modifying POST requests */
    }

    enum CharacterStatus: int {
        case HEALTHY        = 1;
        case POISONED       = 2;
        case BLINDED        = 4;
        case SCARED         = 8;
        case OVERENCUMBERED = 16;
        case OVERHEATED     = 32;
        case STUNNED        = 64;
        case FROZEN         = 128;
        case BURNING        = 256;
        case CONFUSED       = 512;
        case CHARMED        = 1024;
        case SLEEPING       = 2048;
        case DEAD           = 4096;
        case BLEEDING       = 8192;
    }

    enum PropSyncType {
        case ACCOUNT;
        case CHARACTER;
        case FAMILIAR;
        case INVENTORY;
        case STATS;
    }

    enum GEMSTONE {
        /* Red-ish,    focus: health, str               */
        case BLOODSTONE;
        case CARNELIAN;
        case RUBY;

        /* Blue-ish,   focus: defense, mp               */
        case SAPPHIRE;
        case AQUAMARINE;
        case TANZANITE;

        /* Black-ish,  focus: defense, shields, summons */
        case ONYX;
        case OBSIDIAN;

        /* Green-ish,  focus: currency, luck, exp       */
        case JADE;
        case MALACHITE;
        case EMERALD;
        case PERIDOT;

        /* Yellow-ish, focus: currency, int, ep         */
        case TOPAZ;
        case CITRINE;

        /* Purple-ish, focus: mp, mhp, int              */
        case IOLITE;
        case AMETHYST;

        /* White-ish, focus: purity, clarity            */
        case DIAMOND;
        case PEARL;
        case MOONSTONE;
        case ZIRCON;

        /* Brown-ish, focus: grounding, stability       */
        case AGATE;
        case JASPER;
        case SUNSTONE;

        /* Pink-ish, focus: love, compassion            */
        case MORGANITE;
        case TOURMALINE;

        /* Multi-colored, focus: versatility, adaptability */
        case OPAL;
        case ALEXANDRITE;

        /* Other gemstones */
        case CHRYSOLITE;
        case TURQUOISE;

        public static function getRandom(): GEMSTONE {
            return self::cases()[random_int(0, count(self::cases()) - 1)];
        }
    }