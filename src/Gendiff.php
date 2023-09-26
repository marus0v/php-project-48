<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Functional\sort;
use function Differ\Parser\parse;
use function Differ\Formatters\showFormatted;

function findArraysDiff(array $arr1, array $arr2): array
{
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    $sortedKeysArray = sort($keyArray, fn($a, $b) => strcmp($a, $b));
    $result = array_map(function ($key) use ($arr1, $arr2) {
        if (!array_key_exists($key, $arr1)) {
            return [
                'key' => $key,
                'status' => 'added',
                'value' => $arr2[$key]
            ];
        } elseif (!array_key_exists($key, $arr2)) {
            return [
                'key' => $key,
                'status' => 'removed',
                'value' => $arr1[$key]
            ];
        } elseif (is_array($arr1[$key]) && is_array($arr2[$key])) {
            return [
                'key' => $key,
                'status' => 'nested',
                'children' => findArraysDiff($arr1[$key], $arr2[$key])
            ];
        } elseif ($arr1[$key] !== $arr2[$key]) {
            return [
                'key' => $key,
                'status' => 'updated',
                'oldValue' => $arr1[$key],
                'newValue' => $arr2[$key],
            ];
        } else {
            return [
                'key' => $key,
                'status' => 'unchanged',
                'value' => $arr1[$key]
            ];
        }
    }, $sortedKeysArray);
    return $result;
}

function genDiff(string $fileName1, string $fileName2, string $formatName = 'stylish')
{
    /* try {
        $file1Array = parse($fileName1);
        $file2Array = parse($fileName2);
        $differ = findArraysDiff($file1Array, $file2Array);
        $resultString = showFormatted($differ, $formatName);
        return $resultString;
    } catch (\Throwable $ex) {
        var_dump($ex->getMessage());
    } */
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    $differ = findArraysDiff($file1Array, $file2Array);
    $resultString = showFormatted($differ, $formatName);
    return $resultString;
}
