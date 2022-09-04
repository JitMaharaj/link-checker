<?php

declare(strict_types=1);

use LinkChecker\Constants;
use LinkChecker\MediaFire;
use PHPUnit\Framework\TestCase;

final class MediaFireTest extends TestCase
{
    /**
     * @dataProvider linksValidFormat
     */
    public function testValidFormat(string $link, bool $isValid): void
    {
        $this->assertSame($isValid, MediaFire::isValid($link), $link);
    }

    /**
     * @return array    array of [link, is_valid]
     */
    public function linksValidFormat(): array
    {
        return [
            // valid
            ['https://www.mediafire.com/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/file', true],
            ['https://www.mediafire.com/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/', true],
            ['https://www.mediafire.com/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip', true],
            ['https://mediafire.com/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/file', true],
            ['https://mediafire.com/file/5td2knb1d0n6tkh/', true],
            ['https://mediafire.com/file/5td2knb1d0n6tkh', true],
            ['https://mediafire.com/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/anythinghereisvalid', true],
            ['https://www.mediafire.com/folder/xxxxxxxxx/myfoldername', true],
            ['https://www.mediafire.com/folder/xxxxxxxxx/', true],
            ['https://www.mediafire.com/folder/xxxxxxxxx', true],
            // invalid
            ['https://mediafire.co/file/5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/file', false],
            ['https://mediafire.com/kakaotalk_2.7.6.2046.zip/file', false],
            ['https://mediafire.com/file5td2knb1d0n6tkh/kakaotalk_2.7.6.2046.zip/file', false],
            ['https://www.mediafire.com/xxxxxxxxx/myfoldername', false],
            ['https://www.mediafire.com/xxxxxxxxx', false],
        ];
    }

    /**
     * @dataProvider linksType
     */
    public function testGetType(string $link, string $type): void
    {
        $this->assertSame($type, MediaFire::getType($link), $link);
    }

    /**
     * @return array    array of [link, type]
     */
    public function linksType(): array
    {
        return [
            ['https://mediafire.com/file/xxxxxxxxxx/filename.txt/anythinghereisvalid', Constants::TYPE_FILE],
            ['https://mediafire.com/file/xxxxxxxxxx/filename.txt/', Constants::TYPE_FILE],
            ['https://mediafire.com/file/xxxxxxxxxx/filename.txt', Constants::TYPE_FILE],
            ['https://www.mediafire.com/folder/xxxxxxxxx/myfoldername', Constants::TYPE_FOLDER],
            ['https://www.mediafire.com/folder/xxxxxxxxx/', Constants::TYPE_FOLDER],
            ['https://www.mediafire.com/folder/xxxxxxxxx', Constants::TYPE_FOLDER],
        ];
    }
}
