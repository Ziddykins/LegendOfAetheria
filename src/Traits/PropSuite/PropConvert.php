<?php
namespace Game\Traits\PropSuite;

/**
 * Provides bidirectional conversion between camelCase class properties and snake_case database columns.
 * 
 * Handles special cases:
 * - Two-letter uppercase acronyms (ID, HP, MP, EP, AP) are preserved as lowercase with underscores
 * - Example: maxHP → max_hp, characterID → character_id, accountID → account_id
 * - Reverse: max_hp → maxHP, character_id → characterID
 * 
 * Used by PropSync to translate property names for database operations.
 */
trait PropConvert {
    /**
     * Converts camelCase class property name to snake_case database column name.
     * 
     * Examples:
     * - "characterID" → "character_id"
     * - "maxHP" → "max_hp"
     * - "userName" → "user_name"
     * 
     * @param string $property Class property name (camelCase)
     * @return string Database column name (snake_case)
     */
    private function clsprop_to_tblcol($property) {
        $property = preg_replace('/[^a-zA-Z_1-3]/', '', $property);
        $out = null;
        $check_double = null;

        for ($i=0; $i<strlen($property); $i++) {
            if ($i == 0 && ctype_upper($property[$i])) {
                $out .= strtolower($property[$i]);
                continue;
            }

            if (isset($property[$i+1])) {
                $check_double = $property[$i] . $property[$i + 1];
            } else {
                $check_double = $property[$i];
            }

            if (ctype_upper($check_double)) {
                $acceptable_doubles = ['ID', 'HP', 'MP', 'EP', 'AP'];
                if (array_search($check_double, $acceptable_doubles) !== false) {
                    $out .= '_' . strtolower($check_double);
                    $i++;
                    continue;
                }
            }

            if (ctype_upper($property[$i])) {
                $let = strtolower($property[$i]);
                $out .= "_$let";
                continue;
            }
            $out .= $property[$i];
        }
        return $out;
    }

    /**
     * Converts snake_case database column name to camelCase class property name.
     * 
     * Examples:
     * - "character_id" → "characterID"
     * - "max_hp" → "maxHP"
     * - "user_name" → "userName"
     * 
     * @param string $column Database column name (snake_case)
     * @return string Class property name (camelCase)
     */
    private function tblcol_to_clsprop($column) {
        $column = preg_replace('/[^a-zA-Z1-3_]/', '', $column);
        $splits = preg_split('/_/', $column);
        $column = strtolower($column);
        $acceptable_doubles = ['ID', 'HP', 'MP', 'EP', 'AP'];
        
        if (count($splits) == 1) {
            return $column;
        }

        for ($i=1; $i<count($splits); $i++) {
            if (strlen($splits[$i]) == 2 && array_search(strtoupper($splits[$i]), $acceptable_doubles) !== false) {
                $splits[$i] = strtoupper($splits[$i]);
            } else {
                $splits[$i] = ucfirst($splits[$i]);
            }
        }
        
        $class_property = join('', $splits);

        return $class_property;
    }
}