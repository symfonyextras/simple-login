<?php


namespace Symfonyextars\SimpleLogin\Utility;

use Ramsey\Uuid\Uuid;


class Hash
{
    /**
     * @param bool $with_time
     * @return string
     */
    public static function get(bool $with_time = false): string
    {
        try {
            $v4 = Uuid::uuid4();
            $h = $v4->toString();
        } catch (\Exception $e) {
            try {
                $h = bin2hex(random_bytes(5));
            } catch (\Exception $e) {
                $h = hash('sha256', 'radio ' . date('y-m-d H:i:s Y:s N'));
            }
        }

        return $h . ($with_time ? date('U') : '');
    }


    /**
     * @param string $textToEncrypt
     * @param string $password
     * @return string
     */
    public static function encrypt(string $textToEncrypt, string $password): string
    {
        $key = substr(hash('md5', $password, false), 0, 32);
        $cipher = 'aes-256-gcm';
        $iv_len = openssl_cipher_iv_length($cipher);
        $tag_length = 16;
        $iv = openssl_random_pseudo_bytes($iv_len);
        $tag = ""; // will be filled by openssl_encrypt

        $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", $tag_length);
        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * @param string $textToDecrypt
     * @param string $password
     * @return bool
     */
    public static function decrypt(string $textToDecrypt, string $password): bool
    {
        $encrypted = base64_decode($textToDecrypt);
        $key = substr(hash('md5', $password, false), 0, 32);
        $cipher = 'aes-256-gcm';
        $iv_len = openssl_cipher_iv_length($cipher);
        $tag_length = 16;
        $iv = substr($encrypted, 0, $iv_len);
        $tag = substr($encrypted, $iv_len, $tag_length);
        $ciphertext = substr($encrypted, $iv_len + $tag_length);

        return openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}