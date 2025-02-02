<?php
/**
 * FOSSBilling
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license   Apache-2.0
 *
 * This file may contain code previously used in the BoxBilling project.
 * Copyright BoxBilling, Inc 2011-2021
 *
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */


class Box_Crypt implements \Box\InjectionAwareInterface
{
    protected $di = NULL;

    const METHOD = 'aes-256-cbc';

    public function __construct()
    {
        if (!extension_loaded('openssl')) {
            throw new Box_Exception('The PHP openssl extension must be enabled on your server');
        }
    }

    public function setDi($di)
    {
        $this->di = $di;
    }

    public function getDi()
    {
        return $this->di;
    }

    public function encrypt($text, $pass = null)
    {
        $key = $this->_getSalt($pass);

        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt(
            $text,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $ciphertext);
    }

    public function decrypt($text, $pass = null)
    {
        if (is_null($text)){
            return false;
        }
        $key = $this->_getSalt($pass);

        $text = base64_decode($text);

        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = mb_substr($text, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($text, $ivsize, null, '8bit');

        $result = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $result = trim($result);

        return $result;
    }

    private function _getSalt($pass = null)
    {
        if (null == $pass) {
            $pass = $this->di['config']['salt'];
        }
        return pack('H*', hash('md5', $pass));
    }
}