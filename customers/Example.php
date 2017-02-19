<?php

namespace SSO_PHP7\customers;

use SSO_PHP7\model\{ CheckUser };

/**
 * Example of SSO connection using OpenSSL PHP extension running as static service
 *
 * @package SSO
 * @require OpenSSL
 * @see     http://php.net/manual/fr/openssl.requirements.php
 * @author Thomas Dupont
 */
class Example implements SsoInterface
{
    private static $_instance;

    protected $key_size = 32;
    protected $key = 'aXOmepEbFW8Fx7CzfaisDQjLccNrT3fFuPUgsUx4hWw=';

    protected $iv_size;
    protected $iv;

    /**
    * @param array $get parametter of url (here is openssl encrypt string)
    * MUST be impemented
    * @return Boolean
    *
    */
    public function execute(array $get)
    : bool
    {
        if(!isset($get['dt'], $get['iv'])) {
            return false;
        }
        return $this->_decrypt($get['dt'], $get['iv']);
    }

    /**
    * @param$decode the array decode from decrypt
    * MUST be impemented
    * Create an Array as you can us for your DataBase manager
    *
    */
    public function convertParameter(array $decode)
    : array
    {
        $cleanArray = [];
        $cleanArray['firstname'] = $data['login'];
        $cleanArray['lastname'] = $data['lastname']
        $cleanArray['email'] = $data['mail'];
        $cleanArray['phone'] = $data['phone'];
        return $cleanArray;
    }

    public static function getInstance()
    : self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if (!extension_loaded('openssl'))
            throw new \Exception('OpenSSL is required for Carmila data transfer');
        $strong = true;
        // $this->key_size = 32; // 256 bits
        // $this->key = openssl_random_pseudo_bytes($this->key_size, $strong); // $strong will be true if the key is crypto safe

        $this->iv_size = 16; // 128 bits
        $this->iv = openssl_random_pseudo_bytes($this->iv_size, $strong); // $strong will be true if the key is crypto safe
    }


    private function _decrypt(string $data, string $iv)
    : bool
    {
        $arrayDecode = json_decode($this->_pkcs7_unpad(
            openssl_decrypt(
                base64_decode($data),
                'AES-256-CBC',
                base64_decode($this->key),
                0,
                base64_decode($iv)
            )
        ), true);
        $userData = $this->convertParameter($arrayDecode);
        return (isset($userData['email']) && filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) ?
          CheckUser::getInstance()->checkUser($userData, strtoupper(get_class($this))) : false;
    }

    private function _pkcs7_unpad(string $data)
    : string
    {
        return substr($data, 0, -ord($data[strlen($data) - 1]));
    }

    /**
    * @param string clean string to crypt
    * To encrypt user data
    * @return array $crypt parametter to be use in URL
    *
    */
    public function encrypt(string $plaintext)
    : array
    {
        $data = openssl_encrypt(
            $this->pkcs7_pad($plaintext, 16), // padded data
            'AES-256-CBC',                    // cipher and mode
            base64_decode($this->key),        // secret key
            0,                                // options (not used)
            $this->iv                         // initialisation vector
        );

        $timestamp = openssl_encrypt(
            $this->pkcs7_pad(time(), 16),     // padded data
            'AES-256-CBC',                    // cipher and mode
            base64_decode($this->key),        // secret key
            0,                                // options (not used)
            $this->iv                         // initialisation vector
        );

        return array(
            'dt' => urlencode(base64_encode($data)),
            'iv' => urlencode(base64_encode($this->iv)),
            'ts' => urlencode(base64_encode($timestamp))
        );
    }

    private function pkcs7_pad(string $data, string $size)
    : string
    {
        $length = $size - strlen($data) % $size;
        return $data . str_repeat(chr($length), $length);
    }
}
