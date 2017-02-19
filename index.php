<?php

declare(strict_types = 1);
require_once "Autoloader.php";

SSO\Autoloader::register();

try {
    var_dump(SSO\controller\SsoFactory::load());
} catch(\EngineException $e) {
    echo "ENGINE: ".$e->getMessage();
}
