<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parser\parse;
use function Differ\Formatters\showFormatted;

function findArraysDiff(array $arr1, array $arr2): array
{
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    sort($keyArray);

    $result = [];

    foreach ($keyArray as $key) {
        if (!array_key_exists($key, $arr1)) {
            $result[$key] = ['status' => 'added', 'value' => $arr2[$key]];
        } elseif (!array_key_exists($key, $arr2)) {
            $result[$key] = ['status' => 'removed', 'value' => $arr1[$key]];
        } elseif (is_array($arr1[$key]) && is_array($arr2[$key])) {
            $result[$key] = ['status' => 'nested', 'children' => findArraysDiff($arr1[$key], $arr2[$key])];
        } elseif ($arr1[$key] !== $arr2[$key]) {
            $result[$key] = [
                'status' => 'updated',
                'oldValue' => $arr1[$key],
                'newValue' => $arr2[$key],
            ];
        } else {
            $result[$key] = ['status' => 'unchanged', 'value' => $arr1[$key]];
        }
    }

    return $result;
}

function genDiff($fileName1, $fileName2, $formatName = 'stylish')
{
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    $differ = findArraysDiff($file1Array, $file2Array);
    $resultString = showFormatted($differ, $formatName);
    return $resultString;
}
