<?php

declare(strict_types=1);

namespace AESGCM;

final class AESGCM
{
    public static function decrypt(string $data, string $password, bool $raw = false): string|false
    {
        if (!$raw) {
            $data = base64_decode($data, true);
        }
        $salt = substr($data, 0, 32);
        $iv = substr($data, 32, 12);
        $tag = substr($data, 44, 16);
        $data = substr($data, 60);
        $secret = hash_pbkdf2('sha256', hash('sha256', $password, true), $salt, 100000, 32, true);
        return openssl_decrypt(
            $data,
            'aes-256-gcm',
            $secret,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            'aes-256-gcm-php'
        );
    }

    public static function encrypt(string $data, string $password, bool $raw = false): string
    {
        $salt = random_bytes(32);
        $iv = random_bytes(12);
        $secret = hash_pbkdf2('sha256', hash('sha256', $password, true), $salt, 100000, 32, true);
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $secret,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            'aes-256-gcm-php',
            16
        );
        $result = $salt . $iv . $tag . $encrypted;
        if (!$raw) {
            $result = base64_encode($result);
        }
        return $result;
    }
}
