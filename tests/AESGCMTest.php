<?php

declare(strict_types=1);

namespace AESGCM\Test;

use PHPUnit\Framework\TestCase;
use AESGCM\AESGCM;

/**
 * @covers \AESGCM\AESGCM
 */
final class AESGCMTest extends TestCase
{
    public function testCanEncryptAndDecrypt(): void
    {
        $password = 'password';
        for ($i = 0; $i < 100000; $i = 1 + $i * 2) {
            $data =  str_repeat('abcd1234</>"$é€ä?', $i);
            $enc = AESGCM::encrypt($data, $password);
            $dec = AESGCM::decrypt($enc, $password);
            $this->assertGreaterThan(strlen($data), strlen($enc));
            $this->assertEquals($data, $dec);
            $this->assertNotEquals($dec, $enc);
        }
    }

    public function testWrongPassword(): void
    {
        $enc = AESGCM::encrypt('abcd1234', 'xyzxyz');
        $this->assertFalse(AESGCM::decrypt($enc, 'ayzxyz'));
    }

    public function testEmptyPassword(): void
    {
        $enc = AESGCM::encrypt('abcd1234', '');
        $this->assertEquals('abcd1234', AESGCM::decrypt($enc, ''));
    }

    public function testNullPassword(): void
    {
        $this->expectException(\TypeError::class);
        AESGCM::encrypt('abcd1234', null);
    }

    public function testDecryptInvalidData(): void
    {
        $this->assertFalse(AESGCM::decrypt('abc123zzz', 'password'));
        $this->assertFalse(AESGCM::decrypt('abc123zzz', 'password', true));
    }

    public function testCanEncryptBinaryData(): void
    {
        $password = 'password';
        $data = '';
        for ($i = 0; $i < 1000; $i += 1) {
            $data .= chr($i);
        }
        $enc = AESGCM::encrypt($data, $password, true);
        $dec = AESGCM::decrypt($enc, $password, true);
        $this->assertEquals($data, $dec);
    }

    public function testCanEncryptWithBinaryPassword(): void
    {
        $data = 'data';
        $password = '';
        for ($i = 0; $i < 1000; $i += 1) {
            $password .= chr($i);
        }
        $enc = AESGCM::encrypt($data, $password);
        $dec = AESGCM::decrypt($enc, $password);
        $this->assertEquals($data, $dec);
    }

    public function testAdditionalAuthenticatedData(): void
    {
        $data = 'abcd1234';
        $password = 'xyzxyz';
        $enc = AESGCM::encrypt($data, $password);
        $this->assertFalse(AESGCM::decrypt($enc, $password, aad: 'aad'));
        $enc = AESGCM::encrypt($data, $password, aad: 'aad');
        $this->assertFalse(AESGCM::decrypt($enc, $password));
        $this->assertEquals($data, AESGCM::decrypt($enc, $password, aad: 'aad'));
    }
}
