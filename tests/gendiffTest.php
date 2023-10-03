<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Formatters\showFormatted;
use function Differ\Parser\getArrayFromJson;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $expected1 = file_get_contents('./tests/fixtures/1level.txt');
        $this->assertEquals($expected1, genDiff('./tests/fixtures/file1.json', './tests/fixtures/file2.json', 'stylish'));
        $this->assertEquals($expected1, genDiff('./tests/fixtures/file1.yml', './tests/fixtures/file2.yml', 'stylish'));

        $expected2 = file_get_contents('./tests/fixtures/stylish.txt');
        $this->assertEquals($expected2, genDiff('./tests/fixtures/file3.json', './tests/fixtures/file4.json', 'stylish'));
        $this->assertEquals($expected2, genDiff('./tests/fixtures/file3.yml', './tests/fixtures/file4.yml', 'stylish'));

        $expected3 = file_get_contents('./tests/fixtures/plain.txt');
        $this->assertEquals($expected3, genDiff('./tests/fixtures/file3.json', './tests/fixtures/file4.json', 'plain'));

        $expected4 = file_get_contents('./tests/fixtures/json.txt');
        $this->assertEquals($expected4, genDiff('./tests/fixtures/file3.json', './tests/fixtures/file4.json', 'json'));
    }
}
