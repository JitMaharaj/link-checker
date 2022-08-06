<?php

declare(strict_types=1);

use MegaChecker\MegaChecker;
use PHPUnit\Framework\TestCase;

final class MegaCheckerTest extends TestCase
{
    /**
     * @dataProvider linksValidFormat
     */
    public function testValidFormat(string $link, bool $isValid, bool $isNewFormat): void
    {
        $this->assertSame($isValid, MegaChecker::isValid($link));

        if (MegaChecker::isValid($link)) {
            $this->assertSame($isNewFormat, MegaChecker::isNewFormat($link));
        }
    }

    /**
     * @return array    array of [link, is_valid, is_new_format]
     */
    public function linksValidFormat(): array
    {
        return [
            // new format - valid
            ['https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', true, true],
            ['https://mega.nz/file/xxxxxxxx#', true, true],
            ['https://mega.nz/folder/xxxxxxxx', true, true],
            ['https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', true, true],
            ['https://mega.nz/folder/xxxxxxxx#', true, true],
            ['https://mega.nz/folder/xxxxxxxx', true, true],
            // new format - invalid
            ['https://mega.nz/file/xxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['https://mega.nz/file/xxxx', false, true],
            ['https://mega.nz/files/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['http://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['https:/mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['https://mega.nz/folder/xxxxxxxxzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['https://mega.nz/folders/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            ['https://mega.nz/folder/#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false, true],
            // old format - valid
            ['https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', true, false],
            ['https://mega.nz/#!xxxxxxxx!', true, false],
            ['https://mega.nz/#!xxxxxxxx', true, false],
            ['https://mega.nz/#F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', true, false],
            ['https://mega.nz/#F!xxxxxxxx!', true, false],
            ['https://mega.nz/#F!xxxxxxxx', true, false],
            // old format - invalid
            ['https://mega.nz/#!xxxxxxx', false, false],
            ['https://mega.nz/#xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/#!xxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/#Fxxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/Fxxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/#Fxxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
            ['https://mega.nz/#F!xxxxxx!zzzzzzzzzzzzzzzzzzzzz', false, false],
        ];
    }

    /**
     * @dataProvider linksType
     */
    public function testGetType(string $link, string $type): void
    {
        $this->assertSame($type, MegaChecker::getType($link), $link);
    }

    /**
     * @return array    array of [link, type]
     */
    public function linksType(): array
    {
        return [
            // new format
            ['https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', MegaChecker::TYPE_FILE],
            ['https://mega.nz/file/xxxxxxxx#', MegaChecker::TYPE_FILE],
            ['https://mega.nz/folder/xxxxxxxx', MegaChecker::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', MegaChecker::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx#', MegaChecker::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx', MegaChecker::TYPE_FOLDER],
            // old format
            ['https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', MegaChecker::TYPE_FILE],
            ['https://mega.nz/#!xxxxxxxx!', MegaChecker::TYPE_FILE],
            ['https://mega.nz/#!xxxxxxxx', MegaChecker::TYPE_FILE],
            ['https://mega.nz/#F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', MegaChecker::TYPE_FOLDER],
            ['https://mega.nz/#F!xxxxxxxx!', MegaChecker::TYPE_FOLDER],
            ['https://mega.nz/#F!xxxxxxxx', MegaChecker::TYPE_FOLDER],
        ];
    }

    /**
     * @dataProvider linksConversion
     */
    public function testConversion(string $oldLink, string $expectedNewLink): void
    {
        $this->assertSame($expectedNewLink, MegaChecker::convertFromOldFormat($oldLink));
    }

    /**
     * @return array    array of [oldLink, expectedNewLink]
     */
    public function linksConversion(): array
    {
        return [
            [
                'https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
                'https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ],
            [
                'https://mega.nz/#!xxxxxxxx!',
                'https://mega.nz/file/xxxxxxxx',
            ],
            [
                'https://mega.nz/#!xxxxxxxx',
                'https://mega.nz/file/xxxxxxxx',
            ],
            [
                'https://mega.nz/#F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
                'https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz',
            ],
            [
                'https://mega.nz/#F!xxxxxxxx!',
                'https://mega.nz/folder/xxxxxxxx',
            ],
            [
                'https://mega.nz/#F!xxxxxxxx',
                'https://mega.nz/folder/xxxxxxxx',
            ],

        ];
    }

    /**
     * @dataProvider linksIdKey
     */
    public function testGetIdAndKey(
        string $link,
        string $expId,
        ?string $expKey
    ): void {
        [$id, $key] = MegaChecker::getIdAndKey($link);
        $this->assertSame($expId, $id);
        $this->assertSame($expKey, $key);
    }

    /**
     * @return array    array of [link, expected_id, expected_key]
     */
    public function linksIdKey(): array
    {
        return [
            // new format
            ['https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 'xxxxxxxx', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz'],
            ['https://mega.nz/file/xxxxxxxx#', 'xxxxxxxx', null],
            ['https://mega.nz/folder/xxxxxxxx', 'xxxxxxxx', null],
            ['https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 'xxxxxxxx', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz'],
            ['https://mega.nz/folder/xxxxxxxx#', 'xxxxxxxx', null],
            ['https://mega.nz/folder/xxxxxxxx', 'xxxxxxxx', null],
            // old format
            ['https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', 'xxxxxxxx', 'zzzzzzzzzzzzzzzzzzzzz'],
            ['https://mega.nz/#!xxxxxxxx!', 'xxxxxxxx', null],
            ['https://mega.nz/#!xxxxxxxx', 'xxxxxxxx', null],
            ['https://mega.nz/#F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', 'xxxxxxxx', 'zzzzzzzzzzzzzzzzzzzzz'],
            ['https://mega.nz/#F!xxxxxxxx!', 'xxxxxxxx', null],
            ['https://mega.nz/#F!xxxxxxxx', 'xxxxxxxx', null],
        ];
    }
}
