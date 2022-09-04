<?php

declare(strict_types=1);

use LinkChecker\Constants;
use LinkChecker\Mega;
use PHPUnit\Framework\TestCase;

final class MegaTest extends TestCase
{
    /**
     * @dataProvider linksValidFormat
     */
    public function testValidFormat(string $link, bool $isValid, bool $isNewFormat): void
    {
        $this->assertSame($isValid, Mega::isValid($link));

        if (Mega::isValid($link)) {
            $this->assertSame($isNewFormat, Mega::isNewFormat($link));
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
        $this->assertSame($type, Mega::getType($link), $link);
    }

    /**
     * @return array    array of [link, type]
     */
    public function linksType(): array
    {
        return [
            // new format
            ['https://mega.nz/file/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', Constants::TYPE_FILE],
            ['https://mega.nz/file/xxxxxxxx#', Constants::TYPE_FILE],
            ['https://mega.nz/folder/xxxxxxxx', Constants::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx#zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', Constants::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx#', Constants::TYPE_FOLDER],
            ['https://mega.nz/folder/xxxxxxxx', Constants::TYPE_FOLDER],
            // old format
            ['https://mega.nz/#!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', Constants::TYPE_FILE],
            ['https://mega.nz/#!xxxxxxxx!', Constants::TYPE_FILE],
            ['https://mega.nz/#!xxxxxxxx', Constants::TYPE_FILE],
            ['https://mega.nz/#F!xxxxxxxx!zzzzzzzzzzzzzzzzzzzzz', Constants::TYPE_FOLDER],
            ['https://mega.nz/#F!xxxxxxxx!', Constants::TYPE_FOLDER],
            ['https://mega.nz/#F!xxxxxxxx', Constants::TYPE_FOLDER],
        ];
    }

    /**
     * @dataProvider linksConversion
     */
    public function testConversion(string $oldLink, string $expectedNewLink): void
    {
        $this->assertSame($expectedNewLink, Mega::convertFromOldFormat($oldLink));
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
        [$id, $key] = Mega::getIdAndKey($link);
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
