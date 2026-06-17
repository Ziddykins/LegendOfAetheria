<?php
namespace Game\Components\Cards\CharacterSelect;
use Game\Character\Stats;
use Game\Account\Account;
/**
 * Renders a Bootstrap card component for character selection screen.
 * Displays character information (avatar, name, level, race, stats) for existing characters,
 * or an empty slot card with "New Character" button for unused slots.
 */
class CharacterSelectCard {
    /** @var int|null Character ID if slot is occupied, null if empty */
    private $characterID;
    
    /** @var int Slot number (1-3) for this character selection card */
    private $slot;
    private int $characterCount = 0;
    /*
     * Creates a character selection card.
     * 
     * @param int|null $characterID Character ID or null for empty slot
     * @param int $slot Slot number (1-3)
     */
    public function __construct($characterID, $slot) {
        $this->characterID = $characterID;
        $this->slot = $slot;
        $this->characterCount = Account::getCharacterCount($_SESSION['account-id']);
    }

    /**
     * Renders the character select card HTML.
     * For existing characters: shows avatar, stats, Load/Delete buttons.
     * For empty slots: shows placeholder avatar, zero stats, New Character button.
     * 
     * @return string Bootstrap card HTML markup
     */
    public function render(): string {
        global $db, $log, $t;
        $cardHtml = null;

        if ($this->characterID) {
            $sqlQuery = "SELECT `name`, `avatar`, `race`, `stats`, `level` FROM {$t['characters']} WHERE `id` = ?";
            $character = $db->execute_query($sqlQuery, [ $this->characterID ])->fetch_assoc();
            $stats = new Stats($this->characterID);
            $stats->propRestore($character['stats']);

            $cardHtml = '<div class="d-flex card text-center me-3" data-loa-slot="' . $this->slot . '" style="min-width:300px;">
                    <span class="small text-bg-dark bg-gradient float-right">Slot ' . $this->slot . '</span>
                    <div class="card-header">
                    <img src="img/avatars/' . $character['avatar'] . '" class="rounded-circle" width="100" height="100" />
                </div>

                <div class="card-body">
                    <p class="card-text">' . $character['name'] . '<br>the Lv. ' . $character['level'] . ' ' . $character['race']  . '</p>
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
                                <div>'. $stats->get_hp() . ' / ' . $stats->get_maxHP() . '</div>
                                <div>'. $stats->get_mp() . ' / ' . $stats->get_maxMP() . '</div>
                                <div>'. $stats->get_ep() . ' / ' . $stats->get_maxEP() . '</div>
                                <div>'. $stats->get_ap() . '</div>
                                <div>'. $stats->get_exp() . '</div>
                                <div>'. $stats->get_maxExp() . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <form id="select-char-' . $this->slot . '" action="/select" method="POST">
                        <div class="d-flex w-100">
                            <button id="select-delete-' . $this->slot . '" name="select-delete-' . $this->slot . '" class="btn btn-sm btn-outline-danger flex-fill border-black me-3" value="' . $this->slot . '">Delete</button>
                            <button id="select-load-' . $this->slot . '" name="select-load-' . $this->slot . '" class="btn btn-sm btn-primary flex-fill border-black" value="' . $this->slot . '">Load</button>
                        </form>
                    </div>
                </div>
            </div>';
        } else {
            $cardHtml =  "\n\n\t\t\t\t" . '<div class="card text-center me-3 ms-1 flex-shrink-1" data-loa-slot="' . $this->slot . '">
                    <span class="small text-bg-dark bg-gradient float-right">Slot ' . $this->slot . '</span>

                <div class="card-body">
                    <p class="card-text">Lv. 0 - Empty</p>
                    <div class="small" style="font-size: 10px;">
                        - Empty Slot -
                    </div>
                </div>

                <div class="card-footer d-grid gap-2">
                    <button id="select-new-' . $this->slot . '" name="new-char-' . $this->slot . '" type="submit" id="select-new-s' . $this->slot . '" class="flex-grow-1 btn btn-sm btn-success pe-3 als" data-bs-toggle="modal" data-bs-target="#create-character-modal" value="' . $this->slot . '"><span class="material-symbols-outlined">add_reaction</span> New Character</span></button>
                </div>
            </div>';
        }

        return $cardHtml;
    }

    public function getCharCount() {
        return $this->characterCount;
    }
        
}