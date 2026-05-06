<?php
use Game\Inventory\Items\Item;
require_once 'components.php';
$quest_npc = 'question-sage';
$data = ['targetIds' => 1];
$endpoint = "talk/$quest_npc";
$state = ai_serv_post('state');
$state_obj = json_decode($state);
$dialogue = null;

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
<div class="d-flex text-center align-content-center justify-content-center">
	<div class="card shadow-sm w-75 border-light p-3">
		<div class="row">
			<div class="col-md-4">
				<img class="img-fluid" src="/img/avatars/npc/question-sage.jpg" />
			</div>

			<div class="col-md-8">
				<div class="card-body bg-dark bg-opacity-25 p-4">
					<div class="card-headery mb-3">
						QUESTion Giver
					</div>

					<div class="lead mb-3 bg-gradient bg-dark">
						<?php
						echo $message->text;
						?>
					</div>

					<div class="d-gap gap-2 mb-3">
						<ol class="list-group list-group-numbered mb-3">

							<?php
							$i = 0;
							foreach ($message->choices as $choice) {
								echo '<a href="/game?page=intro&sub=quests&choiceIndex=' . $i++ . '&nodeId=' . $choice->nextNodeId . '">';
								echo '    <li class="list-group-item d-flex justify-content-between align-items-start">';
								echo '        <div class="ms-2 me-auto">';
								echo '            <div class="fw-bold">';
								echo $choice->text;
								echo '		      </div>';
								echo '	      </div>';
								echo '        <span class="badge text-bg-primary rounded-pill"><span class="bi bi-' . $choice->type->icon . ' text-' . $choice->type->color . '"></span>';
								echo '    </li>';
								echo '</a>';
							}
							// type stat amount
							if (isset($message->effects)) {
								foreach ($message->effects as $effect) {
									if ($effect->type == 'modifyStat') {
										$stat = $effect->stat;
										$amount = $effect->amount;
									} else if ($effect->type == 'addItem') {
										$log->warning("ADD ITEM: " . print_r($effect, 1));
										$item_id = $effect->itemId;
										$item_type = $effect->itemType;
										$item = new Item($item_type, $item_id);

/*										$badge = "<div class=\"status-pill bg-success-subtle text-success\">" .
												 "    <img src=\"/{$item->get_imgThumb()}\" class=\"avatar\">" .
												 "     <div class=\"label mb-3\">{$item->get_name()}</div>" .
												 "     <div class=\"description\">{$item->get_description()}</div>" .
												 "     <div class=\"border border-success mb-3\">" .
												 "     	   <button id=\"use-item\" name=\"use-item\" loa-data=\"itemId={$item->get_itemId()};itemType={$item->get_itemType()}\" class=\"btn btn-success\">Use</button>" .
												 "     </div>" .
												 "     <button class=\"btn-c\"></button>" .
												 "</div>";



										echo $badge;
 */
										echo render_item_card($item);
									}

								}
							}



							?>
												</ol>
										</div>
								</div>
						</div>
				</div>
		</div>
						

						?>
						</ol>
					</div>
				</div>
			</div>
		</div>
	</div>
