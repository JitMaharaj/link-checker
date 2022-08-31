<?php

namespace LinkChecker;

class LinkChecker
{
    /**
     * @param string[] $links           array of links
     * @param bool $continueOnError     if true the script will not be interrupted if a link in invalid
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @return string[]                 array with the structure ['link' => 'online/offline/invalid']
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
            $status = Constants::STATUS_INVALID;
            /** @var CheckerInterface $host */
            foreach ([Mega::class, GoogleDrive::class] as $host) {
                try {
                    if ($host::isValid($link)) {
                        $status = $host::isOnline(
                            $link,
                            $verifyCertificate,
                            $verbose
                        ) ? Constants::STATUS_ONLINE : Constants::STATUS_OFFLINE;
                        break;
                    }
                } catch (\Exception $e) {
                    if ($continueOnError) {
                        $status = Constants::STATUS_UNKNOWN;
                        break;
                    } else {
                        throw $e;
                    }
                }
            }
            $result[$link] = $status;
        }
        return $result;
    }
}
