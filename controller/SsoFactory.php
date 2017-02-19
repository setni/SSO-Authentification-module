<?php

namespace SSO_PHP7\controller;

use SSO_PHP7\customers\{ Example };

final class SsoFactory {
    /**
    * @param string $company (name of the mother application)
    * @param array $get parametter of url (here is openssl encrypt string)
    * @return Boolean (if the connection perform or not)
    */
    public static function load(string $company = "", array $get = [])
    : array
    {
        if($company == "" || empty($get)) {
            return ['success' => false, 'message' => "Thanks to use good parametter"];
        }
        return ['success' => $company::execute($get), 'message' => "Connection error"];
    }
}
