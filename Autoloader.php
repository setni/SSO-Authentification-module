<?php

namespace SSO_PHP7;

final class Autoloader {

    public static function register()
    {
        define("DS"   , DIRECTORY_SEPARATOR);
        define("ROOT" , dirname(__FILE__).DS."..".DS);
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    private static function autoload(string $class)
    {
        $parts = preg_split("#\\\#", $class);
        $className = array_pop($parts);
        require_once ROOT.strtolower(implode(DS, $parts)).DS.$className.'.php';

    }
}
