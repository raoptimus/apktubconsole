<?php

namespace app\models;

use Yii;

class Crypto {
    private $key = '';
    private $_bin2hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');

    public function __construct() {
        $this->key = Yii::$app->params['cryptKey'];
    }

    public function crypt($str, $bin2hex = true) {
        $res = "";
        $_aKey = array();

        for ($i = 0; $i < 256; $i++)
        {
            $_aKey[$i] = $i;
        }

        $jk = 0;

        for ($ik = 0; $ik < 256; $ik++)
        {
            $jk = ($jk + $_aKey[$ik] + ord($this->key[$ik % strlen($this->key)])) % 256;

            $xk = $_aKey[$ik];
            $_aKey[$ik] =$_aKey[$jk];
            $_aKey[$jk] = $xk;
        }

        $key = $_aKey;
        $i = 0;
        $j = 0;

        for ($y = 0; $y < strlen($str); $y++)
        {
            $i = ($i + 1) % 256;
            $j = ($j + $key[$i]) % 256;
            $x = $key[$i];
            $key[$i] = $key[$j];
            $key[$j] = $x;
            $ch = $str[$y] ^ chr($key[($key[$i] + $key[$j]) % 256]);

            if (!$bin2hex) {
                $res .= $ch;
                continue;
            }
            $chOrd = ord($ch);
            $res .= $this->_bin2hex[($chOrd & 0xf0) >> 4];
            $res .= $this->_bin2hex[($chOrd & 0x0f)];
        }
        return $res;
    }

    public function decrypt($str, $bin2hex = true) {
        if ($bin2hex) {
            $str = pack("H*", $str);
        }
        return $this->crypt($str, false);
    }

    /**
     * @param $pure_string
     * @return string
     * @deprecated
     */
    function encrypt($pure_string ) {
        //good solution, but needs mcrypt
        /*
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $this->key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
        return $encrypted_string;
        */
    }

    /**
     * @param $encrypted_string
     * @return string
     */
/*    function decrypt($encrypted_string) {
        //good solution, but needs mcrypt
        /*
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;

    }*/
}
