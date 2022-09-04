<?php


require "./vendor/autoload.php";

use LinkChecker\LinkChecker;
use LinkChecker\GoogleDrive;
use LinkChecker\Mega;

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
