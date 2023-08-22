<?php
    function get_mysql_datetime() {
        return date("Y-m-d H:i:s", strtotime("now"));
    }
?>