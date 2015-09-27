<?php

error_reporting(E_ALL | E_STRICT);

$dir = dirname(__FILE__) . '/../src';

foreach (glob($dir . '/*.php') as $php_file) {
    require_once $php_file;
}
