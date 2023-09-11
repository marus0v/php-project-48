<?php

namespace GenDiff\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function GenDiff\Parser\parse;
use function GenDiff\Formatters\showFormatted;

const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';
// const SPACE = 'SPC';
// const ADD = 'ADD';
// const SUB = 'SUB';

function strbool($value)
{
    return $value ? 'true' : 'false';
}

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

function checkArraysDifferences(array $arr1, array $arr2): array
{
    $arrayKeys1 = array_keys($arr1);
    $arrayKeys2 = array_keys($arr2);
    $keyArray = array_unique(array_merge($arrayKeys1, $arrayKeys2));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    $subResult = [];
    // var_dump($keyArray);
    foreach ($keyArray as $key) {
        // оба массива содержат данные по ключу и данные - массивы
        if (
            array_key_exists($key, $arr1) &&
            array_key_exists($key, $arr2) &&
            is_array($arr1[$key]) &&
            is_array($arr2[$key])
        ) {
            $comparison = checkArraysDifferences($arr1[$key], $arr2[$key]);
            // var_dump($key);
            $subResult[$key][SPACE] = $comparison;
        } elseif (!array_key_exists($key, $arr2)) {
            // данные только в первом массиве
            if (is_array($arr1[$key])) {
                $result = checkArraysDifferences($arr1[$key], $arr1[$key]);
            } else {
                $result = $arr1[$key];
            }
            $subResult[$key][SUB] = $result;
        } elseif (!array_key_exists($key, $arr1)) {
            // данные только во втором массиве
            if (is_array($arr2[$key])) {
                $result = checkArraysDifferences($arr2[$key], $arr2[$key]);
            } else {
                $result = $arr2[$key];
            }
            $subResult[$key][ADD] = $result;
        } elseif (($arr1[$key] !== $arr2[$key])) {
            // оба массива содержат данные по ключу и данные - разные значения
            if (is_array($arr1[$key])) {
                $result1 = checkArraysDifferences($arr1[$key], $arr1[$key]);
            } else {
                $result1 = $arr1[$key];
            }
            $subResult[$key][SUB] = $result1;
            if (is_array($arr2[$key])) {
                $result2 = checkArraysDifferences($arr2[$key], $arr2[$key]);
            } else {
                $result2 = $arr2[$key];
            }
            $subResult[$key][ADD] = $result2;
        } else {
            // оба массива содержат данные по ключу и данные - одинаковые значения
            $subResult[$key][SPACE] = $arr1[$key];
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
    // $newString = '';
    $keysArray = array_keys($subValue);
    foreach ($keysArray as $key) {
        // var_dump($key);
        // var_dump($level);
        $subSubValue = $subValue[$key];
        foreach ($subSubValue as $subKey => $subSubSubValue) {
            // var_dump($subValue[$key]);
            // var_dump($subKey);
            // var_dump($subSubSubValue);
            if (!is_array($subSubSubValue)) {
                $subResult .= str_repeat(SPACE, $level) . $subKey . $key . ": " . processValue($subSubSubValue) . "\n";
                // var_dump($subResult);
            } else {
                // var_dump($key);
                // var_dump($level);
                $subResult .= str_repeat(SPACE, $level) . $subKey . $key . ": {\n";
                // $level++;
                $subResult .= processArray($subSubSubValue, $level += 1);
                $subResult .= str_repeat(SPACE, $level) . "}\n";
                $level--;
            }
        }
    }
    return $subResult;
}

function showStylish($value)
{
    $level = 0;
    if (!is_array($value)) {
        $result = processValue($value);
    } else {
        $result = "{\n";
        $result .= processArray($value, $level);
        $result .= "}";
    }
    return $result;
}

function processArray2Plain($value)
{
    // $subResult = "{\n";
    $subResult = "";
    $keysArray = array_keys($value);
    foreach ($keysArray as $key) {
        // var_dump($key);
        // var_dump($level);
        $subValue = $value[$key];
        foreach ($subValue as $subKey => $subSubValue) {
            // var_dump($value[$key]);
            // var_dump($subKey);
            // var_dump($subSubValue);
            if (!is_array($subSubValue)) {
                $subResult .= "Property '". $key . "." . $subKey . " was added with value: " . processValue($subSubSubValue) . "\n";
                // var_dump($subResult);
            } else {
                //var_dump($key);
                //var_dump($subKey);
                //var_dump($subSubValue);
                // var_dump($subSubValue[$subKey]);
                foreach ($subSubValue as $subSubKey => $subSubSubValue) {
                    //var_dump($subSubKey);
                    //var_dump($subSubSubValue);
                    //var_dump($subSubSubValue[$subSubKey]);
                    switch ($subKey) {
                        case '  + ':
                            $subLine = "Property '". $key . "." . $subSubKey . "' was added with value: " . processValue($subSubSubValue[$subSubKey]) . "\n";
                        case '  - ':
                            $subLine = "Property '". $key . "." . $subSubKey . "' was removed\n";
                        }
                        $subResult .= $subLine;
                // var_dump($level);
            //    $subResult .= str_repeat(SPACE, $level) . $subKey . $key . ": {\n";
                // $level++;
            //    $subResult .= processArray($subSubSubValue, $level += 1);
            //    $subResult .= str_repeat(SPACE, $level) . "}\n";
            //    $level--;
            //    $subResult .= "Property '". $key . "." . $subSubKey . "' was added with value: " . processValue($subSubSubValue[$subSubKey]) . "\n";
            //    var_dump($subResult);
                }
            }
        }
    }
    // $subResult .= "}";
    return $subResult;
}

function showPlain($value)
{
    if (!is_array($value)) {
        $result = processValue2Plain($value);
    } else {
        $result = "{\n";
        $result .= processArray2Plain($value);
        $result .= "}";
    }
    return $result;
}

/* function showFormatted($differ, $formatName)
{
    switch ($formatName) {
        case 'plain':
           return  showPlain($differ);
        // case 'json':
        //    return showJson($differ);
        case 'stylish':
            return showStylish($differ);
        default:
            throw new \Exception("Unknown format: $formatName");
    }
} */

function genDiff($fileName1, $fileName2, $formatName)
{
    $file1Array = parse($fileName1);
    $file2Array = parse($fileName2);
    $differ = findArraysDiff($file1Array, $file2Array);
    var_dump($differ);
    // $differ = checkArraysDifferences($file1Array, $file2Array);
    // $resultString = stringify(checkArraysDifferences($file1Array, $file2Array));
    $resultString = showFormatted($differ, $formatName);
    return $resultString;
}
