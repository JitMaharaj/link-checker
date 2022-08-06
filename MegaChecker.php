<?php

class MegaChecker
{
    const API_URL = 'https://g.api.mega.co.nz';

    //TODO: add support to old version links:
    // file => https://mega.nz/#!yyyyyy!bbbbbbbbbbbbbbbbbbb
    // folder => https://mega.nz/#F!xxxxxxx!zzzzzzzzzzzzzzzzzzzzz
    const REGEX_VALID = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{0,8}#?[a-zA-Z0-9_-]*/';
    const REGEX_CONTAINS_KEY = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{0,8}#[a-zA-Z0-9_-]+/';
    const REGEX_DOES_CONTAIN_KEY = '/https:\/\/mega\.nz\/(file|folder)\/[a-zA-Z0-9]{0,8}#?/';

    const TYPE_FILE = 'file';
    const TYPE_FOLDER = 'folder';

    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_UNKNOWN = 'unknown';

    /**
     * @param string[] $links           array of MEGA links
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
            } catch (Exception $e) {
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
     * @return bool                     true if the link is valid, false otherwise
     * @throws InvalidArgumentException if the MEGA link is not valid
     * @throws Exception                if it was not possible to communicate with the MEGA API
     */
    public static function isOnline(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): bool {
        if (!self::isValid($link)) {
            throw new InvalidArgumentException("The link $link is not valid");
        }

        // get the file_handler/file_id
        $splittedLink = explode('/', $link);
        if (self::containsKey($link)) {
            // [$id, $key] = explode('#', $splittedLink[4]);
            $id = explode('#', $splittedLink[4])[0];
        } else {
            if (str_contains($link, '#')) {
                $id = explode('#', $splittedLink[4])[0];
            } else {
                $id = $splittedLink[4];
            }
        }

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
                throw new InvalidArgumentException("Invalid link type $type");
        }

        $url = self::API_URL . '/cs?id=' .
            substr((string) (mt_rand() / mt_getrandmax()), 2, 10) . '&n=' . $id;
        $payload = [$data];

        $result = self::postCall($url, $payload, $verifyCertificate, $verbose);
        if (!$result) {
            throw new Exception('Connection failed');
        }
        if (is_numeric($result) && intval($result) <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param string $link              MEGA link
     * @return string                   link type `file` or `folder`
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function getType(string $link): string
    {
        if (!self::isValid($link)) {
            throw new InvalidArgumentException("The link $link is not valid");
        }
        return explode('/', $link)[3];
    }

    /**
     * @param string $link              MEGA link
     * @return bool                     true if the decryption key is included in the link
     * @throws InvalidArgumentException if the MEGA link is not valid
     */
    public static function containsKey(string $link): bool
    {
        if (!self::isValid($link)) {
            throw new InvalidArgumentException("The link $link is not valid");
        }
        return preg_replace(self::REGEX_CONTAINS_KEY, '', $link) === '';
    }

    /**
     * @param string $link  MEGA link
     * @return bool         true if the link is valid against a REGEX pattern
     */
    public static function isValid(string $link): bool
    {
        return preg_replace(self::REGEX_VALID, '', $link) === '';
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
