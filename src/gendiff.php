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

function processValue($key, $array)
{
    return $key . ": " . checkValueType($array[$key]) . "\n";
}

function processArray($array, $level)
{
    $subString = '';
    $spacer = str_repeat(SPACE, $level);
    $keyArray = array_keys($array);
    foreach ($keyArray as $key) {
        if (!is_array($array[$key])) {
            $subString .= $spacer . processValue($key, $array);
        } else {
            $subString .= $key . ": {\n";
            $subString .= processArray($array[$key], $level);
            $subString .= "}\n";
        }
    }
    return $subString;
}

function checkArraysDifferences(array $arr1, array $arr2, $level)
{
    $spacer = str_repeat(SPACE, $level);
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    $subResult = '';
    foreach ($keyArray as $key) {
        // оба массива содержат данные по ключу
        if ((array_key_exists($key, $arr1)) && (array_key_exists($key, $arr2))) {
            if ((!is_array($arr1[$key])) && (!is_array($arr2[$key]))) {
                if ($arr1[$key] === $arr2[$key]) {
                // данные одинаковые
                    $subResult .= $spacer . SPACE . processValue($key, $arr1);
                } else {
                // данные отличаются
                    $subResult .= $spacer . SUB . processValue($key, $arr1);
                    $subResult .= $spacer . ADD . processValue($key, $arr2);
                }
            } elseif ((is_array($arr1[$key])) && (!is_array($arr2[$key]))) {
                // данные по ключу - значение и массив, отличаются
                $subResult .= $spacer . SUB . processArray($arr1[$key], $level);
                $subResult .= $spacer . ADD . processValue($key, $arr2);
            } elseif ((!is_array($arr1[$key])) && (is_array($arr2[$key]))) {
                // данные по ключу - значение и массив, отличаются
                $subResult .= $spacer . SUB . processValue($key, $arr1);
                $subResult .= $spacer . ADD . processArray($arr2[$key], $level);
            } else {
                $level++;
                $subResult .= $spacer . SPACE . $key . ": {\n";
                $subResult .= checkArraysDifferences($arr1[$key], $arr2[$key], $level);
                $subResult .= $spacer . SPACE . "}\n";
            }
        } elseif ((array_key_exists($key, $arr1)) && (!array_key_exists($key, $arr2))) {
            if (is_array($arr1[$key])) {
                $subResult .= $spacer . SUB . $key . ": {\n";
                $subResult .= $spacer . SPACE . processArray($arr1[$key], $level);
                $subResult .= $spacer . SPACE . "}\n";
            } else {
                $subResult .= $spacer . SUB . processValue($key, $arr1);
            // данные только в первом массиве
            }
        } elseif ((!array_key_exists($key, $arr1)) && (array_key_exists($key, $arr2))) {
            if (is_array($arr2[$key])) {
                // var_dump($arr2[$key]);
                $subResult .= $spacer . ADD . $key . ": {\n";
                $subResult .= $spacer . SPACE . processArray($arr2[$key], $level);
                $subResult .= $spacer . SPACE . "}\n";
            } else {
                $subResult .= $spacer . ADD . processValue($key, $arr2);
                // var_dump($subResult);
            // данные только во втором массиве
            }
        }
    }
    return $subResult;
}

function genDiff($fileName1, $fileName2)
{
    $level = 0;
    // $spacer = str_repeat(SPACE, $level);
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    $resultString = "{\n";
    // размечаем строку
    $resultString .= checkArraysDifferences($file1Array, $file2Array, $level);
    $resultString .= "}";
    // дополняем строку
    return $resultString;
}
