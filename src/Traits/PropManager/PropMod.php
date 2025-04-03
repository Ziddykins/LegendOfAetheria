<?php
namespace Game\Traits\PropManager;
use Exception;

trait PropMod {
    private function propMod($method, $params): void {
        if ($method == 'propMod') {
            return;
        }

        $offset = strpos($method, "_");
        $action = substr($method, 0, $offset);

        if ($offset === false) {
            $action = $method;
        } else {
            $prop = lcfirst(substr($method, $offset + 1));
        }

        if (preg_match('/sub|add|mul|div|exp|mod/', $action)) {
            $cur_value    = null;
            $max_value    = null;
            $str_max_prop = null;

            if ($this->$prop) {
                $cur_value = $this->$prop ?? 0;
                if (in_array($prop, ['hp', 'mp', 'ep'])) {
                    $str_max_prop = "max" . strtoupper($prop);
                    $max_value = $this->$str_max_prop ?? PHP_INT_MAX; 
                }
            
                if (is_numeric($cur_value)) {
                    $new_value = $cur_value;

                    switch ($action) {
                        case 'add':
                            $new_value += $params[0];
                            break;
                        case 'sub':
                            $new_value -= $params[0];
                            break;
                        case 'mul':
                            $new_value *= $params[0];
                            break;
                        case 'div':
                            if ($params[0] == 0) {
                                throw new Exception('Division by zero in PropMod');
                            }
                            $new_value /= $params[0];
                            break;
                        case 'exp':
                            $new_value **= $params[0];
                            break;
                        case 'mod':
                            if ($params[0] == 0) {
                                throw new Exception('Modulo by zero in PropMod');
                            }
                            $new_value %= $params[0];
                            break;
                        default:
                            throw new Exception('Unknown PropMod action');
                    }

                    $new_value = min($new_value, $max_value);
                    $new_value = max($new_value, 0);
                        
                    $prop_str = "set_$prop";
                    $this->$prop_str($new_value);
                } else {
                    print_r($this);
                    throw new Exception("Current value '$cur_value' is not numeric!");
                }
            } else {
                $this->$prop = $params[0];
                return;
            }
        }
    }
}