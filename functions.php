<?php
    function generate_toast($type, $title, $message) {
        $toast = "<div role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\" class=\"toast\" data-bs-delay=\"10000\">
    <div class=\"toast-header\">
        <img src=\"...\" class=\"rounded me-2\" alt=\"$type-square\">
        <strong class=\"me-auto\">$title</strong>
        <small>Just now</small>
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
    </div>
    <div class=\"toast-body\">
        $message
    </div>
</div>";
        return $toast;
    }
?>