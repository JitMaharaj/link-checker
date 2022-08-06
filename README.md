# MEGA link checker

A PHP class to check if [MEGA](https://mega.io/) links are valid, online or offline.

## Usage

```php
<?php

require_once('MegaChecker.php');

$link = 'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz';

var_dump(MegaChecker::isValid($link));
var_dump(MegaChecker::isOnline($link));

$links = [
    'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', // file with key
    'https://mega.nz/file/xxxxxxxx#', // file without key
    'https://mega.nz/file/xxxxxxxx', // file without key
    'https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzz', // folder with key
    'https://mega.nz/folder/xxxxxxxx#', // folder without key
    'https://mega.nz/folder/xxxxxxxx', // folder without key
];

$result = MegaChecker::checkLinks($links);

print_r($result);
```

output:

```console
> php .\usage.php
bool(true)
bool(false)
Array
(
    [https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz] => offline
    [https://mega.nz/file/xxxxxxxx#] => offline
    [https://mega.nz/file/xxxxxxxx] => offline
    [https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzz] => offline
    [https://mega.nz/folder/xxxxxxxx#] => offline
    [https://mega.nz/folder/xxxxxxxx] => offline
)
```

## Fix cURL connection issue

The execution of the function `isOnline` can result in a connection issue in case your local bundle of CA root certificates is not up-to-date ([source](https://stackoverflow.com/questions/21187946/curl-error-60-ssl-certificate-issue-self-signed-certificate-in-certificate-cha)).

There are two solution to this issue:

1. *(recommented)* Update your local CA root certificates (`cacert.pem` file). It can be downloaded from [here](https://curl.se/docs/caextract.html). You have to edit the `php.ini` configuration file by adding the following line:

    ```ini
        curl.cainfo = <absolute_path_to> cacert.pem
    ```

2. Set the parameter `verifyCertificate` to `false`. This will bypass the check but it will make the connection unsecure.
