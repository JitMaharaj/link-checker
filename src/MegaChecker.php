<?php

namespace MegaChecker;

class MegaChecker
{
    const API_URL = 'https://g.api.mega.co.nz';

    const REGEX_OLD_VALID = '/https:\/\/mega\.nz\/#F?![a-zA-Z0-9]{8}(![a-zA-Z0-9_-]*)?/';
    const REGEX_OLD_WITH_KEY = '/https:\/\/mega\.nz\/#F?![a-zA-Z0-9]{8}(![a-zA-Z0-9_-]+)/';
    const REGEX_OLD_NO_KEY = '/https:\/\/mega\.nz\/#F?![a-zA-Z0-9]{8}!?/';

    const REGEX_NEW_VALID = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{8}(#[a-zA-Z0-9_-]*)?/';
    const REGEX_NEW_WITH_KEY = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{8}(#[a-zA-Z0-9_-]+)/';
    const REGEX_NEW_NO_KEY = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{8}#?/';

    const TYPE_FILE = 'file';
    const TYPE_FOLDER = 'folder';

    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_UNKNOWN = 'unknown';

    /**
     * @param string[] $links           array of MEGA links. Old links format not supported
     * @param bool $continueOnError     if true the script will not be interrupted if a link in invalid
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @return string[]                 array with the structure ['mega_link' => 'online/offline/invalid']
     */
    public static function checkLinks(
        array $links,
        bool $continueOnError = false,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): array {
        $result = [];
        foreach ($links as $link) {
            $link = trim($link);
            try {
                if (!self::isValid($link)) {
                    $result[$link] = self::STATUS_INVALID;
                    continue;
                }
                $result[$link] = self::isOnline(
                    $link,
                    $verifyCertificate,
                    $verbose
                ) ? self::STATUS_ONLINE : self::STATUS_OFFLINE;
            } catch (\Exception $e) {
                if ($continueOnError) {
                    $result[$link] = self::STATUS_UNKNOWN;
                    continue;
                } else {
                    throw $e;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $link              MEGA link
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @return bool                     true if the link is valid and online, false otherwise
     * @throws InvalidArgumentException if the MEGA link is not valid
     * @throws Exception                if it was not possible to communicate with the MEGA API
     */
    public static function isOnline(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): bool {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }

        $id = self::getIdAndKey($link)[0];
        $type = self::getType($link);
        switch ($type) {
            case self::TYPE_FOLDER:
                $data = [
                    'a' => 'f',
                    'c' => 1,
                    'r' => 1,
                    'ca' => 1,
                ];
                break;
            case self::TYPE_FILE:
                $data = [
                    'a' => 'g',
                    'p' => $id,
                ];
                break;
            default:
                throw new \InvalidArgumentException("Invalid type $type");
        }

        $url = self::API_URL . '/cs?id=' .
            substr((string) (mt_rand() / mt_getrandmax()), 2, 10) . '&n=' . $id;
        $payload = [$data];

        $result = self::postCall($url, $payload, $verifyCertificate, $verbose);
        if (!$result) {
            throw new \Exception('Connection failed');
        }
        if (is_numeric($result) && intval($result) <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param string $link              MEGA link
     * @return string[]                 id and decryption key
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function getIdAndKey(string $link): array
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }
        if (!self::isNewFormat($link)) {
            $link = self::convertFromOldFormat($link);
        }

        $last = explode('/', $link)[4];
        if (str_contains($last, '#')) {
            [$id, $key] = explode('#', $last);
            if ($key === '') {
                $key = null;
            }
            return [$id, $key];
        } else {
            return [$last, null];
        }
    }

    /**
     * @param string $link              MEGA link in the new format
     * @return string                   link type `file` or `folder`
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function getType(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }
        if (!self::isNewFormat($link)) {
            $link = self::convertFromOldFormat($link);
        }
        return explode('/', $link)[3];
    }

    /**
     * @param string $link              MEGA link, old or new format
     * @return bool                     true if the decryption key is included in the link
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function containsKey(string $link): bool
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }
        return preg_replace(self::REGEX_NEW_WITH_KEY, '', $link) === '' ||
            preg_replace(self::REGEX_OLD_WITH_KEY, '', $link) === '';
    }

    /**
     * @param string $link  MEGA link, old or new format
     * @return bool         true if the link is valid against a REGEX pattern
     */
    public static function isValid(string $link): bool
    {
        return preg_replace(self::REGEX_NEW_VALID, '', $link) === '' ||
            preg_replace(self::REGEX_OLD_VALID, '', $link) === '';
    }

    /**
     * @param string $link              MEGA link, old or new format
     * @return bool                     true if the link's format is new
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function isNewFormat(string $link): bool
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }
        return preg_replace(self::REGEX_NEW_VALID, '', $link) === '';
    }

    /**
     * @param string $link              MEGA link in the old format
     * @return string                   link converted to the new format
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function convertFromOldFormat(string $link): string
    {
        if (!self::isValid($link)) {
            throw new \InvalidArgumentException("The link $link is not valid");
        }
        if (self::isNewFormat($link)) {
            return trim($link);
        }

        $link = trim($link);

        $splittedLink = explode('!', $link);
        $id = $splittedLink[1];
        if (count($splittedLink) === 3) {
            $key = $splittedLink[2];
        }
        $newFormatLink = 'https://mega.nz/' .
            (str_contains($link, '/#F!') ? self::TYPE_FOLDER : self::TYPE_FILE) . '/' . $id;
        if (!empty($key)) {
            $newFormatLink .= '#' . $key;
        }

        return $newFormatLink;
    }

    /**
     * @param string $url               url of the MEGA API
     * @param mixed $payload            payload to send in the body request
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @param bool $verbose             if true it will print cURL details
     * @return string|bool              response of the curl_exec function
     */
    public static function postCall(
        string $url,
        mixed $payload,
        bool $verifyCertificate,
        bool $verbose,
    ): string|bool {
        // initialize curl handler
        $ch = curl_init($url);
        // prepare payload
        $bodyContent = json_encode($payload);
        // set headers
        $header = [
            "Content-Type: application/json",
            "Accept: */*",
            "Content-Length: " . strlen($bodyContent),
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
            "Accept-Encoding: gzip, deflate, br",
            "Connection: keep-alive",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // set POST payload
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);

        // set other cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!$verifyCertificate) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if ($verbose) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
