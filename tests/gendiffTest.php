<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;
use function GenDiff\GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
    $expected = "{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}";
    $this->assertEquals($expected, genDiff('./src/file1.json', './src/file2.json'));
    }
}
