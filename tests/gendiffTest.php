<?php

namespace Hexlet\Phpunit\Tests;

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
        $this->assertEquals($expected, genDiff($argv[1], $argv[2]));
    }
}
