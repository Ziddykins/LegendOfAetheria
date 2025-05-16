<?php
namespace Game\Traits\PropManager;
use Game\Traits\PropManager\Enums\PropType;
use Game\Traits\PropManager\PropConvert;
use Game\Traits\PropManager\PropMod;
use Game\Traits\PropManager\PropSync;
use Game\Traits\PropManager\PropDump;

trait PropManager {
    use PropConvert;
    use PropMod;
    use PropSync;
    use PropDump;

    public static function checkIfExists($search_column, $data, $table): int {
        global $db, $log, $t;
        $sqlQuery = "SELECT `id` FROM $table WHERE `$search_column` = ?";
        $result = $db->execute_query($sqlQuery, [$data])->fetch_assoc();

        if ($result && $result['id'] > 0) {
            return $result['id'];
        }

        return -1;
    }
}
?>