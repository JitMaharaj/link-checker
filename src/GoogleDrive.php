<?php

namespace LinkChecker;

class GoogleDrive implements CheckerInterface
{
    const API_URL_FILE = 'https://drive.google.com/uc?export=download&id=';
    const API_URL_FOLDER = 'https://drive.google.com/drive/folders/';

    const REGEX = [
        'valid' => '/https:\/\/drive\.google\.com\/(file\/d|drive\/folders)\/[a-zA-Z0-9\-]{33}((\/(view|edit)?)?(\?[a-zA-Z0-1=]*)?)?/'
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
                return $result[2] === 200;
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
                return isset($result[1]['location']);
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
     * @param string $link              Google Drive link
     * @return string                   resource id
     * @throws InvalidArgumentException if the link is not valid
     */
    public static function getId(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        if (self::getType($link) === Constants::TYPE_FILE) {
            return substr($link, 32, 33);
        } else {
            return substr($link, 39, 33);
        }
    }
}
