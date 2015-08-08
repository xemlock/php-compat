<?php

    /**
     * Get the boolean value of a variable
     *
     * @since PHP 5.5.0
     * @param mixed $var
     * @return bool Returns the boolean value of $var
     */
    function phpcompat_boolval($var)
    {
        return (bool) $var;
    }
