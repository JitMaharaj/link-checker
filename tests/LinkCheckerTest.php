<?php

declare(strict_types=1);

use LinkChecker\Constants;
use LinkChecker\LinkChecker;
use LinkChecker\Mega;
use PHPUnit\Framework\TestCase;

final class LinkCheckerTest extends TestCase
{
    public function testCheckLinks(): void
    {
        $linksToCheck = [
            'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/fil/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
            'https://mega.nz/file/xxxxxxxx#' => Constants::STATUS_OFFLINE,
            'https://mega.nzfile/xxxxxxxx#' => Constants::STATUS_INVALID,
            'https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/#!xxxxxxxxzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
        ];

        $result = LinkChecker::checkLinks(array_keys($linksToCheck), false);
        foreach ($result as $link => $status) {
            $this->assertSame($linksToCheck[$link], $status);
        }
    }
}
