<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;
use function GenDiff\GenDiff\genDiff;
use function GenDiff\Formatters\showFormatted;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
    $expected1 = "{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}";
    $this->assertEquals($expected1, genDiff('./src/file1.json', './src/file2.json', 'stylish'));
    $this->assertEquals($expected1, genDiff('./src/file1.yml', './src/file2.yml', 'stylish'));
    $expected2 = "{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow:
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}";
    $this->assertEquals($expected2, genDiff('./src/file3.json', './src/file4.json', 'stylish'));
    $this->assertEquals($expected2, genDiff('./src/file3.yml', './src/file4.yml', 'stylish'));
  $expected3 = "{
Property 'common.follow' was added with value: false
Property 'common.setting2' was removed
Property 'common.setting3' was updated. From true to null
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: [complex value]
Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
Property 'common.setting6.ops' was added with value: 'vops'
Property 'group1.baz' was updated. From 'bas' to 'bars'
Property 'group1.nest' was updated. From [complex value] to 'str'
Property 'group2' was removed
Property 'group3' was added with value: [complex value]
}";
    $this->assertEquals($expected3, genDiff('./src/file3.json', './src/file4.json', 'plain')); 
    }
}
