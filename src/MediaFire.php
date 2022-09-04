<?php

namespace LinkChecker;

class MediaFire implements CheckerInterface
{
    const REGEX = [
        'valid_file' => '/http(s)?:\/\/(www\.)?mediafire\.com\/file\/[a-zA-Z0-9\-\_]+(\/[a-zA-Z0-9\-\_\.\+]*(\/[\w]*)?)?/',
        'valid_folder' => '/http(s)?:\/\/(www\.)?mediafire\.com\/folder\/[a-zA-Z0-9\-\_]+\/?[a-zA-Z0-9\-\_\.]*(\/[\w]*)?/',
    ];

    public static function isOnline(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): bool {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        $type = self::getType($link);

        switch ($type) {
            case Constants::TYPE_FOLDER:
                $result = HTTPClient::request(
                    $link,
                    HTTPClient::TYPE_GET,
                    null,
                    $verifyCertificate,
                    $verbose
                );
                if (!$result) {
                    throw new \Exception('Connection failed');
                }
                return $result['statusCode'] !== 404;
            case Constants::TYPE_FILE:
                $result = HTTPClient::request(
                    $link,
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
                    default:
                        return str_contains($result['result'], 'window.location.href');
                }

            default:
                throw new \InvalidArgumentException("Invalid type $type");
        }
    }

    public static function isValid(string $link): bool
    {
        return preg_replace(self::REGEX['valid_file'], '', $link) === '' ||
            preg_replace(self::REGEX['valid_folder'], '', $link) === '';
    }

    public static function getType(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        return explode('/', $link)[3] === 'file' ?
            Constants::TYPE_FILE : Constants::TYPE_FOLDER;
    }
}
