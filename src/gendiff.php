<?php

namespace GenDiff\GenDiff;

use Symfony\Component\Yaml\Yaml;

// use GenDiff\Parser;
// use function GenDiff\Parser\parse;
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

function strbool($value)
{
    return $value ? 'true' : 'false';
}

function getArrayFromJson($fileName)
{
    $file = file_get_contents($fileName);
    return $fileArray = json_decode($file, true);
}

function getArrayFromYAML($fileName)
{
    return Yaml::parseFile($fileName);
}

function parse($fileName)
{
    $extension = strrchr($fileName, '.');
    if (($extension === '.yaml') || ($extension === '.yml')) {
        $array = getArrayFromYAML($fileName);
    } else {
        $array = getArrayFromJson($fileName);
    }
    return $array;
}

function checkArraysDifferences(array $arr1, array $arr2)
{
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    $subResult = [];
    // var_dump($keyArray);
    foreach ($keyArray as $key) {
        // оба массива содержат данные по ключу
        if (array_key_exists($key, $arr1) && array_key_exists($key, $arr2) && is_array($arr1[$key]) && is_array($arr2[$key])) {
            $comparison = checkArraysDifferences($arr1[$key], $arr2[$key]);
            var_dump($key);
            $subResult[SPACE . $key] = $comparison;
        } elseif (!array_key_exists($key, $arr2)) {
            $subResult[SUB . $key] = $arr1[$key];
        } elseif (!array_key_exists($key, $arr1)) {
            $subResult[ADD . $key] = $arr2[$key];
        } elseif (($arr1[$key] !== $arr2[$key])) {
            $subResult[SUB . $key] = $arr1[$key];
            $subResult[ADD . $key] = $arr2[$key];
        } else {
            $subResult[SPACE . $key] = $arr1[$key];
        }
    }
    // var_dump($subResult);
    return $subResult;
}

function processValue($value)
{
    if (is_bool($value)) {
        $value = strbool($value);
    } elseif (is_null($value)) {
        $value = 'null';
    }
    return $value;
}

function processArray($subValue, $level)
{
    $subResult = '';
    $newString = '';
    $keysArray = array_keys($subValue);
    foreach ($keysArray as $key) {
        if (!is_array($subValue[$key])) {
            $newString = str_repeat(SPACE, $level) . $key . ": " . processValue($subValue[$key]) . "\n";
//             $subResult .= str_repeat(SPACE, $level) . $newString;
            $subResult .= $newString;
        } else {
            $level++;
            $subResult .= str_repeat(SPACE, $level) . $key . ": {\n";
            $subResult .= processArray($subValue[$key], $level);
            $subResult .= str_repeat(SPACE, $level) . "}\n";
        }
    }
    return $subResult;
}

function stringify($value)
{
    $level = 1;
    $spacer = str_repeat(SPACE, $level);
    if (!is_array($value)) {
        $result = str_repeat(SPACE, $level) . processValue($value);
    } else {
        $keysArray = array_keys($value);
        $result = "{\n";
        foreach ($keysArray as $key) {
            if (!is_array($value[$key])) {
                $result .= $key . ": " . processValue($value[$key]) . "\n";
            } else {
                $result .= $key . ": {\n";
                $result .= str_repeat(SPACE, $level) . processArray($value[$key], $level);
                $result .= str_repeat(SPACE, $level) . "}\n";
            }
        }
        $result .= "}";
    }
    return $result;
}

function genDiff($fileName1, $fileName2)
{
    // $level = 0;
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    $resultString = stringify(checkArraysDifferences($file1Array, $file2Array));
    return $resultString;
}
