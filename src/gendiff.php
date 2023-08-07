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
        return strbool($value);
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
    $file = Yaml::parseFile($fileName);
    // return $fileArray = json_decode($file, true);
    // var_dump($file);
    return $file;
}

function parse($fileName)
{
    $extension = strrchr($fileName, '.');
    // var_dump($extension);
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

function checkArraysDifferences(array $arr1, array $arr2)
{
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    var_dump($keyArray);
    foreach ($keyArray as $key) {
        // оба json содержат данные по ключу
        if ((array_key_exists($key, $arr1)) && (array_key_exists($key, $arr2))) {
            if ((!is_array($arr1[$key])) && (!is_array($arr2[$key]))) {
                // данные по ключу - значения
                $value1 = $arr1[$key];
                $value2 = $arr2[$key];
                if ($value1 === $value2) {
                // данные одинаковые
                    // $resultString .= SPACE . $key . ": " . checkValueType($value1) . "\n";
                    $resultString .= SPACE . processValue($key, $arr1);
                } else {
                // данные отличаются
                    $resultString .= SUB . $key . ": " . checkValueType($value1) . "\n";
                    $resultString .= ADD . $key . ": " . checkValueType($value2) . "\n";
                }
            } elseif ((is_array($arr1[$key])) && (!is_array($arr2[$key]))) {

            }
        }
    }

}

function genDiff($fileName1, $fileName2)
{
    $file1Array = parse($fileName1);
    // var_dump($file1Array);
    $file2Array = parse($fileName2);
    $file1ArrayKeys = array_keys($file1Array);
    $file2ArrayKeys = array_keys($file2Array);
    // разобрали json на массивы
    $keyArray = array_unique(array_merge($file1ArrayKeys, $file2ArrayKeys));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    // checkArraysDifferences($file1Array, $file2Array);
    $resultString = "{\n";
    // проверяем значение по ключам
    foreach ($keyArray as $key) {
        // оба json содержат данные по ключу
        if ((array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $value2 = $file2Array[$key];
            if ($value1 === $value2) {
            // данные одинаковые
                // $resultString .= SPACE . $key . ": " . checkValueType($value1) . "\n";
                $resultString .= SPACE . processValue($key, $file1Array);
            } else {
            // данные отличаются
                $resultString .= SUB . processValue($key, $file1Array);
                $resultString .= ADD . processValue($key, $file2Array);
            }
        } elseif ((array_key_exists($key, $file1Array)) && (!array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $resultString .= SUB . processValue($key, $file1Array);
            // данные только в первом json
        } elseif ((!array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value2 = $file2Array[$key];
            $resultString .= ADD . processValue($key, $file2Array);
            // данные только во втором json
        }
    }
    $resultString .= "}";
    // дополняем строку
    var_dump($resultString);
    return $resultString;
}
