<?php

require "./vendor/autoload.php";

use LinkChecker\LinkChecker;
use LinkChecker\Mega;

$link = 'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz';

var_dump(Mega::isValid($link));
var_dump(Mega::isOnline($link));

$links = [
    'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', // file with key
    'https://mega.nz/file/xxxxxxxx#', // file without key
    'https://mega.nz/file/xxxxxxxx', // file without key
    'https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzz', // folder with key
    'https://mega.nz/folder/xxxxxxxx#', // folder without key
    'https://mega.nz/folder/xxxxxxxx', // folder without key
];

$result = LinkChecker::checkLinks($links);

print_r($result);
