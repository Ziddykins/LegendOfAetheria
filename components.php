<?php

    require_once 'constants.php';

    function generate(Components $which, $data_object) {
        $produced_html = null;

        switch($which) {
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
}
