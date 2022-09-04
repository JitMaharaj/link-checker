<?php

declare(strict_types=1);

use LinkChecker\Constants;
use LinkChecker\LinkChecker;
use PHPUnit\Framework\TestCase;

final class LinkCheckerTest extends TestCase
{

    public function testGetLinkStatus(): void
    {
        $linksToCheck = [
            'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/fil/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
            'https://mega.nz/file/xxxxxxxx#' => Constants::STATUS_OFFLINE,
            'https://mega.nzfile/xxxxxxxx#' => Constants::STATUS_INVALID,
            'https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/#!xxxxxxxxzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
            'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view' => Constants::STATUS_OFFLINE,
            'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/error' => Constants::STATUS_INVALID,
            'https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' => Constants::STATUS_OFFLINE,
            'https://drive.google.com/file/d/0B17t2HhTjZgFRTRTbVhvZVZ6V28/view?resourcekey=0-KWDMMWoE6Ozd8t6ZSf_idg' => Constants::STATUS_ONLINE,
        ];

        foreach ($linksToCheck as $link => $expectedStatus) {
            $this->assertSame(LinkChecker::getLinkStatus($link), $expectedStatus, $link);
        }
    }

    public function testCheckLinks(): void
    {
        $linksToCheck = [
            'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/fil/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
            'https://mega.nz/file/xxxxxxxx#' => Constants::STATUS_OFFLINE,
            'https://mega.nzfile/xxxxxxxx#' => Constants::STATUS_INVALID,
            'https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_OFFLINE,
            'https://mega.nz/#!xxxxxxxxzzzzzzzzzzzzzzzzzzzzz' => Constants::STATUS_INVALID,
            'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view' => Constants::STATUS_OFFLINE,
            'https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/error' => Constants::STATUS_INVALID,
            'https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' => Constants::STATUS_OFFLINE,
            'https://drive.google.com/file/d/0B17t2HhTjZgFRTRTbVhvZVZ6V28/view?resourcekey=0-KWDMMWoE6Ozd8t6ZSf_idg' => Constants::STATUS_ONLINE,
        ];

        $result = LinkChecker::checkLinks(array_keys($linksToCheck), false);
        foreach ($result as $link => $status) {
            $this->assertSame($linksToCheck[$link], $status, $link);
        }
    }
}
