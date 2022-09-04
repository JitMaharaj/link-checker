<?php

declare(strict_types=1);

use LinkChecker\Constants;
use LinkChecker\GoogleDrive;
use PHPUnit\Framework\TestCase;

final class GoogleDriveTest extends TestCase
{
    /**
     * @dataProvider linksValidFormat
     */
    public function testValidFormat(string $link, bool $isValid): void
    {
        $this->assertSame($isValid, GoogleDrive::isValid($link), $link);
    }

    /**
     * @return array    array of [link, is_valid]
     */
    public function linksValidFormat(): array
    {
        return [
            // valid
            ['https://drive.google.com/file/d/0B17t2HhTjZgFRTRTbVhvZVZ6V28/edit?resourcekey=0-KWDMMWoE6Ozd8t6ZSf_idg', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?AnythingHereIsValid', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?usp=sharing', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit?usp=sharing', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit?', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/?', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?', true],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', true],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?usp=sharing', true],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/?usp=sharing', true],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?', true],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/', true],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/?', true],
            // invalid
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/viewusp=sharing', false],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edits', false],
            ['https://drive.google.com/filed/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', false],
            ['https://drive.google.com/file/dxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', false],
            ['https://drive.google.com/fie/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', false],
            ['https://drive.google.com/file/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', false],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxusp=sharing', false],
            ['https://drive.google.com/drive/folder/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?usp=sharing', false],
            ['https://drive.google.com/drivefolders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?usp=sharing', false],

        ];
    }

    /**
     * @dataProvider linksType
     */
    public function testGetType(string $link, string $type): void
    {
        $this->assertSame($type, GoogleDrive::getType($link), $link);
    }

    /**
     * @return array    array of [link, type]
     */
    public function linksType(): array
    {
        return [
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?usp=sharing', Constants::TYPE_FILE],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?', Constants::TYPE_FILE],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/', Constants::TYPE_FILE],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', Constants::TYPE_FILE],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?usp=sharing', Constants::TYPE_FOLDER],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?', Constants::TYPE_FOLDER],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/', Constants::TYPE_FOLDER],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', Constants::TYPE_FOLDER],

        ];
    }

    /**
     * @dataProvider linksId
     */
    public function testGetId(
        string $link,
        string $expId,
    ): void {
        $id = GoogleDrive::getId($link);
        $this->assertSame($expId, $id);
    }

    /**
     * @return array    array of [link, expected_id]
     */
    public function linksId(): array
    {
        return [
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view?usp=sharing', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/view', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/file/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?usp=sharing', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx?', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],
            ['https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'],

        ];
    }
}
