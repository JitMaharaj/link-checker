# Link checker

![tests](https://github.com/tassoneroberto/link-checker/actions/workflows/php.yml/badge.svg)

A PHP module to check if hosted files/folders links are valid, online or offline.

Supported services:

- [MEGA](https://mega.io/)
- [Google Drive](https://www.google.com/drive/)

## Requirements

It requires `php-curl`.

For Ubuntu/Debian users:

```bash
sudo apt-get install php-curl
```

For Windows users uncomment the following line in `php.ini`:

```ini
extension=php_curl.dll
```

## Usage

```php
$link = 'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz';

var_dump(Mega::isValid($link));
var_dump(Mega::isOnline($link));

$link = 'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

var_dump(GoogleDrive::isValid($link));
var_dump(GoogleDrive::isOnline($link));

$links = [
    // MEGA
    'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', // file with key
    'https://mega.nz/file/xxxxxxxx', // file without key
    'https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzz', // folder with key
    'https://mega.nz/folder/xxxxxxxx#', // folder without key
    'https://mega.nz/folder/xxxxxx', // invalid link - too short
    'https://mega.nz/folder/xxxxxxxxxxxxx', // invalid link - too long
    // Google Drive
    'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // file
    'https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // folder
    'https://drive.google.com/file/dxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // invalid link
    'https://drive.google.com/drive/folder/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // invalid link
];

$result = LinkChecker::checkLinks($links);

print_r($result);
```

output:

```console
bool(true)
bool(false)
bool(true)
bool(false)
Array
(
    [https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz] => offline
    [https://mega.nz/file/xxxxxxxx] => offline
    [https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzz] => offline
    [https://mega.nz/folder/xxxxxxxx#] => offline
    [https://mega.nz/folder/xxxxxx] => invalid
    [https://mega.nz/folder/xxxxxxxxxxxxx] => invalid
    [https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx] => offline
    [https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx] => offline
    [https://drive.google.com/file/dxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx] => invalid
    [https://drive.google.com/drive/folder/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx] => invalid
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
