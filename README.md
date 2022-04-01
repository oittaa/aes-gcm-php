# aes-gcm-php
AES GCM (Galois Counter Mode) made simple.

## Installation

```bash
composer require oittaa/aes-gcm
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use AESGCM\AESGCM;

// Base64 encoded data returned
$encrypted = AESGCM::encrypt('my data', 'my secret password');
var_dump($encrypted);
$decrypted = AESGCM::decrypt($encrypted, 'my secret password');
var_dump($decrypted);

// False returned
$decrypted = AESGCM::decrypt($encrypted, 'WRONG password');
var_dump($decrypted);

// Raw binary data returned
$encrypted = AESGCM::encrypt('my data', 'my secret password', true);
var_dump($encrypted);
$decrypted = AESGCM::decrypt($encrypted, 'my secret password', true);
var_dump($decrypted);

// Additional authenticated data (AAD)
$encrypted = AESGCM::encrypt('my data', 'my secret password', aad: 'additional data');
var_dump($encrypted);
$decrypted = AESGCM::decrypt($encrypted, 'my secret password', aad: 'additional data');
var_dump($decrypted);
```
