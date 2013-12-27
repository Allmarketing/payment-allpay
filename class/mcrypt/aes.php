<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of aes
 *
 * @author Administrator
 */
class Mcrypt_Aes {
    /**
     * pkcs7补码
     *
     * @param string $string  明文
     * @param int $blocksize Blocksize , 以 byte 为单位
     *
     * @return String
     */ 
    static function addPkcs7Padding($string, $blocksize = 32) {
        $len = strlen($string); //取得字符串长度
        $pad = $blocksize - ($len % $blocksize); //取得补码的长度
        $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
        return $string;
    }

    /**
     * 加密然后base64转码
     * 
     * @param String 明文
     * @param 加密的初始向量（IV的长度必须和Blocksize一样， 且加密和解密一定要用相同的IV）
     * @param $key 密钥
     */
    static function aes256cbcEncrypt($str, $iv, $key ) {	
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, self::addPkcs7Padding($str) , MCRYPT_MODE_CBC, $iv));
    }
    /**
     * 加密然后base64转码
     * 
     * @param String 明文
     * @param 加密的初始向量（IV的长度必须和Blocksize一样， 且加密和解密一定要用相同的IV）
     * @param $key 密钥
     */
    static function aes128cbcEncrypt($str, $iv, $key ) {	
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, self::addPkcs7Padding($str,16) , MCRYPT_MODE_CBC, $iv));
    }

    /**
     * 除去pkcs7 padding
     * 
     * @param String 解密后的结果
     * 
     * @return String
     */
    static function stripPkcs7Padding($string){
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }


    /**
     * 解密
     * 
     * @param String $encryptedText 二进制的密文 
     * @param String $iv 加密时候的IV
     * @param String $key 密钥
     * 
     * @return String
     */
    static function aes256cbcDecrypt($encryptedText, $iv, $key) {
            $encryptedText =base64_decode($encryptedText);
            return self::stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encryptedText, MCRYPT_MODE_CBC, $iv));
    }

    /**
     * 解密
     * 
     * @param String $encryptedText 二进制的密文 
     * @param String $iv 加密时候的IV
     * @param String $key 密钥
     * 
     * @return String
     */
    static function aes128cbcDecrypt($encryptedText, $iv, $key) {
            $encryptedText =base64_decode($encryptedText);
            return self::stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encryptedText, MCRYPT_MODE_CBC, $iv));
    }
}
