<?php

namespace SSO_PHP7\model;

/**
* SSO authentification
* @author Thomas D
* MIT License
*/
abstract class Sso {

    /**
    * @param $data Decrypt SSO data
    * @param $company company of SSO user
    *
    */
    abstract protected function checkUser (array $userData, string $company = "")
    : bool;

    /**
    * @param $data Decrypt SSO data
    * @param $company company of SSO user
    *
    */
    abstract protected function createUser (array $userData, string $company = "")
    : bool;

    /**
    * @param $email email of the user
    * @param $token connection token uniq per user to secure the connection
    *
    */
    abstract protected function connectUser (string $login, string $token = "")
    : bool;
}
