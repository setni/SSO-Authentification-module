<?php

namespace SSO_PHP7\customers;

/**
* SSO authentification
* @author Thomas D
* MIT License
*/
Interface SsoInterface {

    /**
    * @param array $get parametter of url (here is openssl encrypt string)
    * MUST be impemented
    *
    */
    public function execute(array $get)
    : bool;

    /**
    * @param array $decode the array decode from decrypt
    * MUST be impemented
    * Create an Array as you can us for your DataBase manager
    *
    */
    public function convertParameter(array $decode)
    : array;
}
