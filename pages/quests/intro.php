<?php
use Game\Inventory\Items\Item;
require_once 'components.php';
$quest_npc = 'question-sage';
$data = ['targetIds' => 1];
$endpoint = "talk/$quest_npc";
$state = ai_serv_post('state');
$state_obj = json_decode($state);
$dialogue = null;
global $log;

if (isset($state_obj->store?->state?->dialogueCore?->activeNodeId)) {
    $dialogue = $state_obj->store->state->dialogueCore;
}

if (isset($_GET['choiceIndex']) && $_GET['choiceIndex'] != null) {
    $choice = $_GET['choiceIndex'];
    $node_id = $_GET['nodeId'];
    $endpoint = 'choice';

    $data = [
        'dialogueId' => 'rude',
        'choiceIndex' => preg_replace('/[^0-9]/', '', $choice),
        'nodeId' => preg_replace('/[^a-zA-Z_-]/', '', $node_id)
    ];
} else if ($dialogue !== null) {
    $endpoint = "talk/{$dialogue->npcId}/{$dialogue->activeNodeId}";
    $data = [
        'dialogueId' => $dialogue->activeDialogueId,
        'nodeId' => $dialogue->activeNodeId
    ];
}

$message = json_decode(ai_serv_post($endpoint, $data));
$log->warning(print_r($message, 1));
?>
<div class="quest-intro-page py-5">
    <div class="container">
        

        <div class="quest-card shadow-lg">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-4">
                    <div class="quest-avatar-wrapper d-flex align-items-center justify-content-center p-3">
                        <img class="img-fluid rounded-4 quest-avatar" src="/img/avatars/npc/question-sage.jpg" alt="Quest Giver" />
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-body p-5 position-relative">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
                            <div>
                                <p class="text-uppercase text-secondary small mb-1 letter-spacing">Shadowed Inquiry</p>
                                <h1 class="quest-title mb-2">Quest Giver</h1>
                                <p class="text-muted small mb-0">A mysterious sage offers guidance from the forgotten archives.</p>
                            </div>
                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle">New Encounter</span>
                        </div>

                        <div class="quest-dialogue p-4 rounded-4 mb-4">
                            <?php echo $message->text; ?>
                        </div>

                        <div class="quest-choices row g-3">
                            <?php foreach ($message->choices as $idx => $choice): ?>
                                <?php $href = '/game?page=intro&sub=quests&choiceIndex=' . preg_replace('/[^0-9]/', '', $idx) . '&nodeId=' . preg_replace('/[^a-zA-Z_-]/', '', $choice->nextNodeId); ?>
                                <div class="col-12">
                                    <a href="<?php echo $href; ?>" class="quest-choice d-flex align-items-center p-3 rounded-4 text-decoration-none">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="quest-choice-icon text-<?php echo $choice->type->color; ?>">
                                                <i class="bi bi-<?php echo $choice->type->icon; ?> fs-6 opacity-50"></i>
                                            </span>
                                            <div>
                                                <div class="fw-semibold text-white"><?php echo $choice->text; ?></div>
                                                <div class="text-muted small">Choose this response</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (isset($message->effects) && count((array) $message->effects)): ?>
                            <div class="quest-effects mt-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h2 class="fs-5 text-white mb-0">Rewards & Effects</h2>
                                </div>
                                <?php
                                foreach ($message->effects as $effect) {
                                    if ($effect->type == 'modifyStat') {
                                        $stat = $effect->stat;
                                        $amount = $effect->amount;
                                        echo '<div class="effect-pill p-3 mb-3">';
                                        echo '<div class="d-flex align-items-center justify-content-between text-white">';
                                        echo "<span>{$stat} <small class=\"text-muted\">change</small></span>";
                                        echo "<span class=\"text-warning\">" . ($amount >= 0 ? "+{$amount}" : "{$amount}") . "</span>";
                                        echo '</div>';
                                        echo '</div>';
                                    } else if ($effect->type == 'addItem') {
                                        $item_id = $effect->itemId;
                                        $item_type = $effect->itemType;
                                        $item = new Item($item_type, $item_id);
                                        echo render_item_card($item);
                                    }
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
