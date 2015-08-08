<?php

define('PHPCOMPAT_VERSION', '0.1.0');

defined('JSON_NUMERIC_CHECK') || define('JSON_NUMERIC_CHECK', 32);
defined('JSON_UNESCAPED_SLASHES') || define('JSON_UNESCAPED_SLASHES', 64);
defined('JSON_PRETTY_PRINT') || define('JSON_PRETTY_PRINT', 128);
defined('JSON_UNESCAPED_UNICODE') || define('JSON_UNESCAPED_UNICODE', 256);

if (!function_exists('array_column')) {
    /**
     * Return the values from a single column in the input array
     *
     * @since PHP 5.5.0
     * @param array $input
     * @param int|string|null $column_key
     * @param int|string $index_key OPTIONAL
     * @return array
     */
    function array_column($input, $column_key, $index_key = null)
    {
        switch (func_num_args()) {
            case 0:
                trigger_error('array_column() expects at least 2 parameters, 0 given', E_USER_WARNING);
                return null;
    
            case 1:
                trigger_error('array_column() expects at least 2 parameters, 1 given', E_USER_WARNING);
                return null;
        }
    
        if (!is_array($input)) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
            return null;
        }
    
        if (null !== $index_key
            && !is_int($index_key)
            && !is_string($index_key)
            && !(is_object($index_key) && method_exists($index_key, '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
    
        $output = array();
    
        foreach ($input as $input_value) {
    
            // $input_value must be an array, otherwise it will be ignored
            $is_valid_value = is_array($input_value) && (
                null === $column_key ||
                // null value in $input_value[$column_key] is perfectly valid
                // and cannot be ignored
                isset($input_value[$column_key]) ||
                array_key_exists($column_key, $input_value)
            );
    
            if ($is_valid_value) {
    
                if (null === $column_key) {
                    $output_value = $input_value;
                } else {
                    $output_value = $input_value[$column_key];
                }
    
                // value used as a key in result array must be not null
                if (null !== $index_key && isset($input_value[$index_key])) {
                    $output[$input_value[$index_key]] = $output_value;
                } else {
                    $output[] = $output_value;
                }
    
            }
        }
    
        return $output;
    }
}

if (!function_exists('array_replace')) {
    /**
     * Replaces elements from passed arrays into the first array
     *
     * array_replace() replaces the values of array1 with values having the
     * same keys in each of the following arrays. If a key from the first array
     * exists in the second array, its value will be replaced by the value from
     * the second array. If the key exists in the second array, and not the
     * first, it will be created in the first array. If a key only exists in
     * the first array, it will be left as is. If several arrays are passed for
     * replacement, they will be processed in order, the later arrays
     * overwriting the previous values.
     *
     * @since PHP 5.3.0
     * @param array $array1     The array in which elements are replaced
     * @param array $array2...  The array from which elements will be extracted
     * @return array Returns an array, or NULL if an error occurs
     */
    function array_replace(array $array1, array $array2)
    {
        $args = func_get_args();
        $num_args = func_num_args();
    
        $res = array();
        for ($i = 0; $i < $num_args; ++$i) {
            if (is_array($args[$i])) {
                foreach ($args[$i] as $key => $val) {
                    $res[$key] = $val;
                }
            } else {
                trigger_error(__FUNCTION__ .'(): Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING);
                return null;
            }
        }
    
        return $res;
    }
}

if (!function_exists('array_replace_recursive')) {
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
    function array_replace_recursive($array, $array1)
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
}

if (!function_exists('boolval')) {
    /**
     * Get the boolean value of a variable
     *
     * @since PHP 5.5.0
     * @param mixed $var
     * @return bool Returns the boolean value of $var
     */
    function boolval($var)
    {
        return (bool) $var;
    }
}

if (!function_exists('gethostname')) {
    /**
     * Gets the host name
     *
     * @since PHP 5.3.0
     * @return string
     */
    function gethostname() {
        return php_uname('n');
    }
}

if (!function_exists('stream_resolve_include_path')) {
    /**
     * Resolve filename against the include path.
     *
     * @since PHP 5.3.2
     * @param string $filename
     * @return string|false
     */
    function stream_resolve_include_path($filename)
    {
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        array_unshift($dirs, getcwd());
    
        foreach ($dirs as $dir) {
            if (false !== ($path = realpath($filename))) {
                return $path;
            }
        }
    
        return false;
    }
}

if (!interface_exists('JsonSerializable')) {
    /**
     * JsonSerializable interface
     *
     * @since PHP 5.4.0
     */
    interface JsonSerializable
    {
        public function jsonSerialize();
    }
}

if (!interface_exists('SessionHandlerInterface')) {
    /**
     * @since PHP 5.4.0
     */
    interface SessionHandlerInterface
    {
        public function close();
    
        public function destroy($session_id);
    
        public function gc($maxlifetime);
    
        public function open($save_path, $name);
    
        public function read($session_id);
    
        public function write($session_id, $session_data);
    }
}