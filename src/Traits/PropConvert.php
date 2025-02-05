<?php
namespace Game\Traits;

trait PropConvert {
    private function clsprop_to_tblcol($property) {
        global $log;
        $splits = [];
        $splits = preg_split('/(?=[A-Z]{1,2})/', $property);

        if (count($splits) === 1) {
            return $property;
        }

        if ($splits[1] == 'I' && $splits[2] == 'D') {
            $table_column = strtolower("$splits[0]_id");
        } else {
            $table_column = $splits[0] . '_' . strtolower($splits[1]);
        }

        if (isset($splits[2]) && $splits[1] != 'I' && $splits[2] != 'D') {
            $table_column .= '_' . strtolower($splits[2]);
        }

        return $table_column;
    }

    private function tblcol_to_clsprop($column) {
        global $log;

        $splits = preg_split('/_/', $column);

        if (count($splits) === 1) {
            return $column;
        }

        if ($splits[1] === 'id') {
            $class_property = $splits[0] . strtoupper($splits[1]);
        } else {
            $class_property = $splits[0] . ucfirst($splits[1]);
        }

        if (isset($splits[2])) {
            $class_property .= ucfirst($splits[2]);
        }

        return $class_property;
    }
}