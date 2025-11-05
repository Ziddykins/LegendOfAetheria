<?php

namespace Game\System\Tabulator;
use Game\System\Tabulator\Enums\DataType;
use Game\System\Tabulator\Enums\LayoutType;
use ReflectionClass;

/**
 * Converts PHP objects to Tabulator.js table configurations.
 * Uses reflection to generate column headers and row data for JavaScript table library.
 * Work in progress - TABLE_DATA type not yet implemented.
 */
class TableFromObject {
    /** @var object Source object to convert to table */
    private $object;
    
    /** @var mixed Column definitions for table headers */
    private $columnData;
    
    /** @var mixed Row data for table body */
    private $rowData;
    
    /** @var LayoutType Table layout mode (fit columns, fit data, etc.) */
    private LayoutType $layout;
    
    /** @var mixed Accumulated output string during generation */
    private $output;

    /**
     * Creates a table generator from an object.
     * 
     * @param object $object Source object to convert
     */
    public function __construct($object) {
        $this->object = $object;
    }
    
    /**
     * Converts object to Tabulator JSON configuration.
     * HEADER_DATA: generates column definitions with editable cells.
     * TABLE_DATA: not yet implemented.
     * 
     * @param object $object Object to convert
     * @param DataType $type Type of data to generate (HEADER_DATA or TABLE_DATA)
     * @return string JSON configuration string
     */
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

    /**
     * Sanitizes output for Tabulator.js format.
     * Removes quotes from property names and handles null values.
     * 
     * @param string $input Raw JSON-like string
     * @return string Sanitized output for Tabulator
     */
    private function tabulatorSanitize(string $input): string {
        $output = preg_replace('/"(.*?) ":("?.*?"?),/', '$1:$2, ', $input);
        $output = preg_replace('/, "(.*?)":/', ', $1:', $output);
        $output = str_replace("null", '"null"', $output);

        return $output;
    }
}