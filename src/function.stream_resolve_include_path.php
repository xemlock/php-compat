<?php

    /**
     * Resolve filename against the include path.
     *
     * @since PHP 5.3.2
     * @param string $filename
     * @return string|false
     */
    function phpcompat_stream_resolve_include_path($filename)
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
