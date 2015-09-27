<?php

    /**
     * Return the values from a single column in the input array
     *
     * @since PHP 5.5.0
     * @param array $input
     * @param int|string|null $column_key
     * @param int|string $index_key OPTIONAL
     * @return array
     */
    function phpcompat_array_column($input, $column_key, $index_key = null)
    {
        $num_args = func_num_args();

        if ($num_args > 3) {
            trigger_error(
                sprintf('array_column() expects at most 3 parameters, %d given', $num_args),
                E_USER_WARNING
            );
            return null;
        }

        if ($num_args < 2) {
            trigger_error(
                sprintf('array_column() expects at least 2 parameters, %d given', $num_args),
                E_USER_WARNING
            );
            return null;
        }

        if (!is_array($input)) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
            return null;
        }

        if (null !== $column_key
            && !is_int($column_key)
            && !is_float($column_key)
            && !is_string($column_key)
            && !(is_object($column_key) && method_exists($column_key, '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (null !== $index_key
            && !is_int($index_key)
            && !is_float($index_key)
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
