<?php

    /**
     * Gets the host name
     *
     * @since PHP 5.3.0
     * @return string
     */
    function phpcompat_gethostname() {
        return php_uname('n');
    }
