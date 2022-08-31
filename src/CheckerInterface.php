<?php

namespace LinkChecker;

interface CheckerInterface
{
    /**
     * @param string $link              link to check
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @return bool                     true if the link is valid and online, false otherwise
     * @throws InvalidArgumentException if the link is not valid
     * @throws Exception                connection failed
     */
    public static function isOnline(
        string $link,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): bool;

    /**
     * @param string $link  link to check
     * @return bool         true if the link matches a REGEX
     */
    public static function isValid(string $link): bool;

    /**
     * @param string $link              link to check
     * @return string                   link type `file` or `folder`
     * @throws InvalidArgumentException if the link is not valid
     */
    public static function getType(string $link): string;
}
