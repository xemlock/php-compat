<?php

    /**
     * @internal
     */
    function __phpcompat_array_replace_recursive($array, $array1)
    {
        foreach ($array1 as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }
 
            // overwrite the value in the base array
            if (is_array($value)) {
                $value = __phpcompat_array_replace_recursive($array[$key], $value);
            }
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Replaces elements from passed arrays into the first array recursively
     *
     * @since PHP 5.3.0
     * @param array $array
     * @param array $array1...
     * @return array
     * @uses __phpcompat_array_replace_recursive()
     */
    function phpcompat_array_replace_recursive($array, $array1)
    {
        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }
        for ($i = 1; $i < count($args); ++$i) {
            if (is_array($args[$i])) {
                $array = __phpcompat_array_replace_recursive($array, $args[$i]);
            }
        }
        return $array;
    }
