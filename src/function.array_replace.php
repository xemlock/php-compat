<?php

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
function phpcompat_array_replace(array $array1 = null, array $array2 = null)
{
    $args = func_get_args();
    $num_args = func_num_args();

    if ($num_args < 1) {
        trigger_error(
            sprintf('array_replace() expects at least 1 parameter, %d given', $num_args),
            E_USER_WARNING
        );
        return null;
    }

    $res = array();
    for ($i = 0; $i < $num_args; ++$i) {
        if (is_array($args[$i])) {
            foreach ($args[$i] as $key => $val) {
                $res[$key] = $val;
            }
        } else {
            trigger_error(
                sprintf('array_replace(): Argument #%d is not an array', $i + 1),
                E_USER_WARNING
            );
            return null;
        }
    }

    return $res;
}
