<?php

    require_once 'constants.php';

    function generate(Components $which, $data_object) {
        $produced_html = null;

        switch($which) {
        case Components::OFFCANVAS_CHAT:
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
			$produced_html .= '	  <div class="offcanvas-header">';
			$produced_html .= '		<h5 class="offcanvas-title" id="' . $oc_label . '">Support Chat - ' . $online_status_header . '</h5>';
			$produced_html .= '		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
			$produced_html .= '		<div>';
			$produced_html .= '			<div class="offcanvas-body">';
			$produced_html .= '				<div class="mb-3">';
			$produced_html .= '					<label for="chat-nickname" class="form-label"></label>';
			$produced_html .= '					<input type="email" class="form-control" id="chat-nickname" placeholder="name@example.com">';
			$produced_html .= '				</div>';
			$produced_html .= '				<div class="mb-3>';
			$produced_html .= '					<label for="chat-textarea" class="form-label">Example textarea</label>';
			$produced_html .= '					<textarea class="form-control" id="chat-textarea" rows="3"></textarea>';
			$produced_html .= '				</div>';
			$produced_html .= '			</div>';
			$produced_html .= '		</div>';
			$produced_html .= '	</div>';
			$produced_html .= '</div>';
			break;
            case Components::FLOATING_LABEL_TEXTBOX:
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
            default:
                return LOAError::FUNCT_GENCOMP_UNKNOWN;
        }

        return $produced_html;
    }
?>
