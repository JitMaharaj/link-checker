<?php

namespace LinkChecker;

class GoogleDrive implements CheckerInterface
{
    const API_URL_FILE = 'https://drive.google.com/uc?export=download&id=';
    const API_URL_FOLDER = 'https://drive.google.com/drive/folders/';

    const REGEX = [
        'valid' => '/http(s)?:\/\/(www\.)?drive\.google\.com\/(file\/d|drive\/folders)\/[a-zA-Z0-9\-\_]+((\/(view|edit)?)?(\?[a-zA-Z0-9=\-\_]*)?)?/',
        'clean' => '/http(s)?:\/\/(www\.)?drive\.google\.com\/(file\/d|drive\/folders)\/[a-zA-Z0-9\-\_]+/',
    ];

    public static function isOnline(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): bool {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        $id = self::getId($link);
        $type = self::getType($link);

        switch ($type) {
            case Constants::TYPE_FOLDER:
                $url = self::API_URL_FOLDER . $id;
                $result = HTTPClient::request(
                    $url,
                    HTTPClient::TYPE_GET,
                    null,
                    $verifyCertificate,
                    $verbose
                );
                if (!$result) {
                    throw new \Exception('Connection failed');
                }
                return $result['statusCode'] === 200;
            case Constants::TYPE_FILE:
                $url = self::API_URL_FILE . $id;
                $result = HTTPClient::request(
                    $url,
                    HTTPClient::TYPE_GET,
                    null,
                    $verifyCertificate,
                    $verbose
                );
                if (!$result) {
                    throw new \Exception('Connection failed');
                }

                switch ($result['statusCode']) {
                    case 404:
                        return false;
                    case 303: // redirect to download => the file is online
                        return true;
                    default:
                        return
                            // the file exists and the header contains the location
                            // of the file
                            isset($result['headers']['location']) ||
                            // the file exists but Google Drive can't scan this file
                            // for viruses
                            (isset($result['headers']['cross-origin-opener-policy']) &&
                                str_contains(
                                    $result['headers']['cross-origin-opener-policy'][0],
                                    'DriveUntrustedContentHttp'
                                )
                            ) ||
                            (isset($result['headers']['content-security-policy']) &&
                                str_contains(
                                    $result['headers']['content-security-policy'][0],
                                    'DriveUntrustedContentHttp'
                                )
                            );
                }

            default:
                throw new \InvalidArgumentException("Invalid type $type");
        }
    }

    public static function isValid(string $link): bool
    {
        return preg_replace(self::REGEX['valid'], '', $link) === '';
    }

    public static function getType(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        return explode('/', $link)[3] === 'file' ?
            Constants::TYPE_FILE : Constants::TYPE_FOLDER;
    }

    /**
     * @param string $link              link
     * @return string                   resource id
     * @throws InvalidArgumentException if the link is not valid
     */
    public static function getId(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        preg_match(self::REGEX['clean'], $link, $match);
        return explode('/', $match[0])[5];
    }
}
