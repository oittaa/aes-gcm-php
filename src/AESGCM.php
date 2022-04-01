<?php

declare(strict_types=1);

namespace AESGCM;

final class AESGCM
{
    private static function keyAndIV(string $password, string $salt): array
    {
        $key = hash_pbkdf2('sha512', $password, $salt, 100_000, 44, true);
        $iv = substr($key, 32, 12);
        $key = substr($key, 0, 32);
        return [$key, $iv];
    }

    public static function decrypt(string $data, string $password, bool $raw = false): string|false
    {
        if (!$raw) {
            $data = base64_decode($data, true);
        }
        if ($data === false || strlen($data) < 32) {
            return false;
        }
        $salt = substr($data, 0, 16);
        $tag = substr($data, 16, 16);
        $data = substr($data, 32);
        [$key, $iv] = self::keyAndIV($password, $salt);
        return openssl_decrypt(
            $data,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            'aes-256-gcm-php'
        );
    }

    public static function encrypt(string $data, string $password, bool $raw = false): string
    {
        $salt = random_bytes(16);
        [$key, $iv] = self::keyAndIV($password, $salt);
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            'aes-256-gcm-php',
            16
        );
        $result = $salt . $tag . $encrypted;
        if (!$raw) {
            $result = base64_encode($result);
        }
        return $result;
    }
}
