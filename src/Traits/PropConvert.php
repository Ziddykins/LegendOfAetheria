<?php
namespace Game\Traits;

trait PropConvert {
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