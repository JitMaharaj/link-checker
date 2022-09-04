<?php

namespace LinkChecker;

class LinkChecker
{
    const HOSTING_PROVIDERS = [
        Mega::class,
        GoogleDrive::class,
    ];

    /**
     * @param string $link              link to check
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @param bool $verbose             if true it will print cURL details
     * @return string                   link status: online/offline/invalid
     * @throws Exception                connection failed
     */
    public static function getLinkStatus(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): string {
        $link = trim($link);
        $status = Constants::STATUS_INVALID;
        /** @var CheckerInterface $host */
        foreach (self::HOSTING_PROVIDERS as $host) {
            if ($host::isValid($link)) {
                $status = $host::isOnline(
                    $link,
                    $verifyCertificate,
                    $verbose
                ) ? Constants::STATUS_ONLINE : Constants::STATUS_OFFLINE;
                break;
            }
        }
        return $status;
    }
    /**
     * @param string[] $links           array of links
     * @param bool $continueOnError     if true the script will not be interrupted if a link in invalid
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @param bool $verbose             if true it will print cURL details
     * @return string[]                 array with the structure ['link' => 'online/offline/invalid/unknown']
     */
    public static function checkLinks(
        array $links,
        bool $continueOnError = false,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): array {
        $result = [];
        foreach ($links as $link) {
            try {
                $status = self::getLinkStatus($link, $verifyCertificate, $verbose);
            } catch (\Exception $e) {
                if ($continueOnError) {
                    $status = Constants::STATUS_UNKNOWN;
                    break;
                } else {
                    throw $e;
                }
            }
            $result[$link] = $status;
        }
        return $result;
    }
}
