<?php
namespace Game\Character;
use Game\Inventory\Inventory;
use Game\Monster\Monster;
use Game\Traits\Enums\Type;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;

class Character {
    use PropConvert;
    use Propsync;
    
    private $id;
    private $accountID;
    private $name;
    private $race;
    private $avatar;
    private $level = 1;
    private $x = 0;
    private $y = 0;
    private $location = 'The Shrine';
    private $alignment = 0;
    private $gold = 1000;
    private $exp = 0;

    private $floor = 1;
    private $description = 'None Provided';

    /* class Monster */
    public $monster;

    /* class Stats */
    public $stats;

    /* class Inventory */
    public $inventory;

    public function __construct($accountID, $characterID = null) {
        $this->accountID = $accountID;
        $this->id = $characterID;
    }

    public function __call($method, $params): mixed {
        if ($method == 'propSync') {
            return -1;
        }
        
        return $this->propSync($method, $params, Type::CHARACTER);
    }

    private function getNextCharSlotID($accountID): int {
        global $db;
        $sqlQuery = "SELECT IF (`char_slot1` IS NULL, 1, IF (`char_slot2` IS NULL, 2, IF (`char_slot3` IS NULL, 3, -1))) AS `free_slot` FROM {$_ENV['SQL_ACCT_TBL']} WHERE `id` = ?";
        return $db->execute_query($sqlQuery, [ $accountID ])->fetch_assoc()['free_slot'];
    }

    public static function genSelectCard($characterID, $slot): string {
        global $db, $log;
        $cardHtml = null;
        $log->debug("Generating card", [ 'ID' => $characterID, 'Slot' => $slot]);

        if ($characterID) {
            $sqlQuery = "SELECT `name`, `avatar`, `race`, `stats` FROM {$_ENV['SQL_CHAR_TBL']} WHERE `id` = ?";
            $character = $db->execute_query($sqlQuery, [ $characterID ])->fetch_assoc();
            $stats = safe_serialize($character['stats'], true);

            $cardHtml = '<div class="card text-center me-3 ms-1" data-loa-slot="' . $slot . '">
                    <span class="small text-bg-dark bg-gradient float-right">Slot ' . $slot . '</span>
                    <div class="card-header">
                    <img src="img/avatars/' . $character['avatar'] . '" class="rounded-circle" width="100" height="100" />
                </div>

                <div class="card-body">
                    <p class="card-text">' . $character['name'] . ' the Lv. ' . $stats->get_level() . ' ' . $character['race']  . '</p>
                    <div class="small" style="font-size: 12px;">
                        <div class="row">
                            <div class="col-3 text-white">
                                <div>HP</div>
                                <div>MP</div>
                                <div>EP</div>
                                <div>AP</div>
                                <div>XP</div>
                                <div>NL</div>
                            </div>

                            <div class="col">
                                <div>'. $stats->get_hp() . ' / ' . $stats->get_maxHp() . '</div>
                                <div>'. $stats->get_mp() . ' / ' . $stats->get_maxMp() . '</div>
                                <div>'. $stats->get_ep() . ' / ' . $stats->get_maxEp() . '</div>
                                <div>'. $stats->get_ap() . '</div>
                                <div>'. $stats->get_exp() . '</div>
                                <div>'. $stats->get_maxExp() . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer d-grid gap-2">
                    <form id="select-char-' . $slot . '" action="/select" method="POST">
                        <button id="select-delete-' . $slot . '" name="select-delete-' . $slot . '" class="btn btn-sm btn-outline-danger pe-3" value="' . $slot . '">Delete</button>
                        <button id="select-load-' . $slot . '" name="select-load-' . $slot . '" class="btn btn-sm btn-primary" value="' . $slot . '">Load</button>
                    </form>
                </div>
            </div>';
        } else {
            $cardHtml =  "\n\n\t\t\t\t" . '<div class="card text-center me-3 ms-1" data-loa-slot="' . $slot . '">
                    <span class="small text-bg-dark bg-gradient float-right">Slot ' . $slot . '</span>
                    <div class="card-header">
                    <img src="img/avatars/avatar-unknown.webp" class="rounded-circle" width="100" height="100" />
                </div>

                <div class="card-body">
                    <p class="card-text">Lv. 0 - Empty</p>
                    <div class="small" style="font-size: 10px;">
                        <div class="row">
                            <div class="col">
                                <span>HP</span>: 0 / 0
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <span>MP</span>: 0 / 0
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <span>EP</span>: 0 / 0
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <span>AP</span>: 0
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col">                        
                                <div>XP: 1</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div>NL: 100</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-grid gap-2">
                    <button id="select-new-' . $slot . '" name="new-char-' . $slot . '" type="submit" id="select-new-s' . $slot . '" class="flex-grow-1 btn btn-sm btn-success pe-3" data-bs-toggle="modal" data-bs-target="#create-character-modal" value="' . $slot . '">New Character</a>
                </div>
            </div>';
        }

        return $cardHtml;
    }
}