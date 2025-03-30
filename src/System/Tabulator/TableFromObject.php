<?php

namespace Game\System\Tabulator;
use Game\System\Tabulator\Enums\DataType;
use Game\System\Tabulator\Enums\LayoutType;
use ReflectionClass;

class TableFromObject {
    private $object;
    private $columnData;
    private $rowData;
    private LayoutType $layout;
    private $output;

    public function __construct($object) {
        $this->object = $object;
    }
    
    
    public function objectToJson(object $object, DataType $type): string {
        $refl = new ReflectionClass($object);
        $cls = get_class($object);
        $props = $refl->getProperties();

        if (is_null($this->output)) {
            $this->output = "{";
        }
        
        //{title:"verification_code", field:"verification_code", cellEdited:function(cell) {cell.getData();} }
        if ($type == DataType::HEADER_DATA) {
            foreach ($props as $prop) {
                if (class_exists(get_class(($prop->name)))) {
                    $prop->
                    $temp = new $prop->name();
                    $this->output .= $this->objectToJson($temp, $type);
                } else {
                    $this->output .= "{title: \"$prop->name\", field:\"$prop->name\", cellEdited:function(cell) {cell.getData();}";
                }
            }
            $this->output = $this->tabulatorSanitize($this->output);
            $return_data  = $this->output;
            $return_data  = preg_replace('/,$/', '', $return_data);
            $this->output = null;
        } elseif ($type == DataType::TABLE_DATA) {
            
        }
        return $return_data;
    }

    private function tabulatorSanitize(string $input): string {
        $output = preg_replace('/"(.*?) ":("?.*?"?),/', '$1:$2, ', $input);
        $output = preg_replace('/, "(.*?)":/', ', $1:', $output);
        $output = str_replace("null", '"null"', $output);

        return $output;
    }
}