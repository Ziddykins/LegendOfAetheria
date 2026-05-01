<?php
    use Game\Components\Modals\Modals;
    use Game\Components\Enums\ComponentType;
	use Game\System\Enums\LOAError;
	use Game\Inventory\Items\Item;
	use Game\Inventory\Enums\ObjectRarity;
	use Game\Inventory\Enums\ObjectType;

    function generate(ComponentType $which, $data_object) {
        $produced_html = null;

        switch($which) {
            case ComponentType::OFFCANVAS_CHAT:
                $assistant_online = 1; //check_rep_availability eventually
                $icon_thumbs_up   = '<span class="material-symbols-outlined">recommend</span>';
                $icon_glyph       = '●';
                $icon_color       = 'text-';
                $online_status    = ($assistant_online ? 'Online'  : 'Offline');
                $icon_color      .= ($assistant_online ? 'success' : 'danger');
                
                $oc_title = $data_object->title;
                $oc_id    = $data_object->id;
                $oc_label = $data_object->label;
                
                $online_status_header = '<span class="' . $icon_color . '">' . $icon_glyph . '</span> ' . $online_status;

                $produced_html =  '<div class="offcanvas offcanvas-start show" tabindex="-1" id="' . $data_object->id . '" aria-labelledby="' . $oc_id . 'Label">';
                $produced_html .= '      <div class="offcanvas-header">';
                $produced_html .= '        <h5 class="offcanvas-title" id="' . $oc_label . '">Support Chat - ' . $online_status_header . '</h5>';
                $produced_html .= '        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                $produced_html .= '        <div>';
                $produced_html .= '            <div class="offcanvas-body">';
                $produced_html .= '                <div class="mb-3">';
                $produced_html .= '                    <label for="chat-nickname" class="form-label"></label>';
                $produced_html .= '                    <input type="email" class="form-control" id="chat-nickname" placeholder="name@example.com">';
                $produced_html .= '                </div>';
                $produced_html .= '                <div class="mb-3>';
                $produced_html .= '                    <label for="chat-textarea" class="form-label">Example textarea</label>';
                $produced_html .= '                    <textarea class="form-control" id="chat-textarea" rows="3"></textarea>';
                $produced_html .= '                </div>';
                $produced_html .= '            </div>';
                $produced_html .= '        </div>';
                $produced_html .= '    </div>';
                $produced_html .= '</div>';
                break;
                case ComponentType::FLOATING_LABEL_TEXTBOX:
                    $produced_html = '<div class="input-group">
                                        <span class="input-group-text" id="name-icon" name="name-icon">
                                            <span class="' . $data_object['icon'] . '">cog    nition</span>
                                        </span>
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="' . $data_object['id'] . '" name="' . $data_object['id'] . '">
                                            <label for="' . $data_object['id'] . '">' . $data_object['label'] . '</label>';
                    if ($data_object['small']) {    
                        $produced_html .= '<small>' . $data_object['small'] . '</small>';
                    }
                    
                    $produced_html .= '   </div>';
                    $produced_html .= '</div>';
                    
					break;
				case ComponentType::ITEMCARD:
		/*			$produced_html = null;
					$rarity        = strtolower($data_object->rarity);
					$item_type     = $data_object->type;
					$item_subtype  = $data_object->subtype;
					$item_name     = $data_object->name;
					$item_desc     = $data_object->description;
					$item_stats    = $data_object->modifiers;
					$item_thumb    = $data_object->imgThumb;
					$item_weight   = $data_object->weight;
					$item_sockets  = $data_object->maxSockets;
					$item_modifiers = $data_object->modifiers;



					$produced_html  = '<div class="card bg-dark text-light border-0 rounded p-2 rpg-glow rarity-' . $item_rarity . '">';
                    $produced_html .= '    <div class="d-flex gap-2 align-items-start">';
                    $produced_html .= '        <img src="' . $item_thumb . '" class="rounded" />';
                    $produced_html .= '        <div class="flex-grow-1">';
				    $produced_html .= '            <div class="d-flex justify-content-between">';
					$produced_html .= '                <div class="rpg-title">' . $item_name . '</div>';
					$produced_html .= '                <span class="badge bg-secondary rarity-badge">';
					$produced_html .= '                    <i class="bi bi-circle"></i> Common';
					$produced_html .= '                </span>';
				    $produced_html .= '            </div>';
				    $produced_html .= '            <div class="small text-secondary">Reliable. Barely.</div>';
				    $produced_html .= '            <div class="small text-success">+5 ATK</div>';
				    $produced_html .= '        </div>';
			        $produced_html .= '    </div>';
			        $produced_html .= '</div>';
		 */


break;

                default:
                    return LOAError::FUNCT_GENCOMP_UNKNOWN;
        }

        return $produced_html;
    }

	function render_item_card(Item $item): string {
		$rarity = $item->get_rarity()->data();
		$type = ObjectType::name_to_enum(strtoupper($item->get_type()));
	    return '
    <div class="card bg-dark text-light border-0 rounded p-2 rpg-glow rarity-'.$rarity['key'].'">
        <div class="d-flex gap-2 align-items-start">

            <img src="'.$item->get_imgThumb() .'" class="rounded" width="64" height="64" />

            <div class="flex-grow-1">

                <div class="d-flex justify-content-between align-items-center">
                    <div class="rpg-title">' . $item->get_name() . '</div>

                    <span class="badge bg-'.$rarity['class'].' rarity-badge">
                        '.$type->icon().' '.$rarity['label'].'
                    </span>
                </div>

                <div class="small text-secondary">' . $item->get_description() . '</div>

                <div class="small">
                    ' . format_stats($item->get_modifiers()) . '

                </div>

            </div>
        </div>
    </div>';
	}

	function format_stats(array $stats): string {
		$out = '';

		foreach ($stats as $stat => $val) {
			$class = $val >= 0 ? 'text-success' : 'text-danger';
			$sign  = $val >= 0 ? '+' : '';
			$out  .= "<span class=\"$class\">{$sign}{$val} {$stat}</span> ";
		}

		return $out;
	}
?>
