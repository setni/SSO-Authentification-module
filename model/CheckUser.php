<?php

namespace SSO\model;

use SSO_PHP7\model\{ Mysql };

/**
* SSO authentification
* @author Thomas D
* MIT License
*/
class CheckUser extends Sso {

    /**
    * @var instance of CheckUser
    */
    private static $instance;

    /**
    * @var instance of Mysql
    */
    private static $mysql;

    public static function getInstance()
    : self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct ()
    {
        self::$mysql = new Mysql();
    }

    /**
    * @param $data Decrypt SSO data
    * @param $company company of SSO user
    *
    */
    public function checkUser (array $userData, string $company = "")
    : bool
    {
        return $this->connectUser($userData['email']) ? true : $this->createUser($userData, $company);
    }

    /**
    * @param $data Decrypt SSO data
    * @param $company company of SSO user
    *
    */
    public function createUser (array $userData, string $company = "")
    : bool
    {
        return self::mysql->setUser($userData, $company);
    }

    /**
    * @param $login login of the user
    * @param $token secure token to prevent hack
    *
    */
    public function connectUser (string $login, string $token = "")
    : bool
    {
        // @TODO Connection token
        return self::mysql->connectUser($login);
    }


}
