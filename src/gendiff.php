<?php

namespace GenDiff\GenDiff;

use Symfony\Component\Yaml\Yaml;

// use GenDiff\Parser;
// use function GenDiff\Parser\parse;
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

function checkValueType($value)
{
    if (is_bool($value)) {
        $value = strbool($value);
    } elseif (is_null($value)) {
        $value = 'null';
    }
    return $value;
}

function strbool($value)
{
    return $value ? 'true' : 'false';
}

function toString($value)
{
    return trim(var_export($value, true), "'");
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

// function processValue($key, $array)
// {
//     return $key . ": " . checkValueType($array[$key]) . "\n";
// }

// function processArray($array, $level)
// {
//     $subString = '';
//     $spacer = str_repeat(SPACE, $level);
//     $keyArray = array_keys($array);
//     foreach ($keyArray as $key) {
//         if (!is_array($array[$key])) {
//            $subString .= processValue($key, $array);
//         } else {
//             $subString .= $key . ": {\n";
//             $subString .= processArray($array[$key], $level);
//             $subString .= "}\n";
//         }
//     }
//     return $subString;
// }

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
        if ((array_key_exists($key, $arr1)) && (array_key_exists($key, $arr2)) && is_array($arr1[$key]) && is_array($arr2[$key])) {
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
        $result = toString(strbool($value));
    } else {
        $result = toString($value);
    }
    return $result;
}

function processArray($subValue, $spacer, $level)
{
    $subResult = '';
    $newString = '';
    $keysArray = array_keys($subValue);
    foreach ($keysArray as $key) {
        if (!is_array($subValue[$key])) {
            $newString = str_repeat($spacer, $level) . $key . ": " . processValue($subValue[$key]) . "\n";
            $subResult .= $spacer . $newString;
        } else {
            $level++;
            $subResult .= str_repeat($spacer, $level) . $key . ": {\n";
            $subResult .= processArray($subValue[$key], $spacer, $level);
            $subResult .= str_repeat($spacer, $level) . "}\n";
        }
    }
    return $subResult;
}

function stringify($value)
{
    $level = 1;
    $spacer = str_repeat(SPACE, $level);
    if (!is_array($value)) {
        $result = $spacer . processValue($value);
    } else {
        $keysArray = array_keys($value);
        $result = "{\n";
        foreach ($keysArray as $key) {
            if (!is_array($value[$key])) {
                $result .= $key . ": " . processValue($value[$key]) . "\n";
            } else {
                $result .= $key . ": {\n";
                $result .= processArray($value[$key], $spacer, $level);
                $result .= $spacer . "}\n";
            }
        }
        $result .= "}";
    }
    return $result;
}

function genDiff($fileName1, $fileName2)
{
    $level = 0;
    // $spacer = str_repeat(SPACE, $level);
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    // $resultString = "{\n";
    // размечаем строку
    $resultString = stringify(checkArraysDifferences($file1Array, $file2Array));
    // $resultString .= "}";
    // дополняем строку
    return $resultString;
}
