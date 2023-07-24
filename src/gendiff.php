<?php

namespace GenDiff\GenDiff;

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

function genDiff($fileName1, $fileName2)
{
    $file1Array = getArrayFromJson($fileName1);
    var_dump($file1Array);
    $file2Array = getArrayFromJson($fileName2);
    $file1ArrayKeys = array_keys($file1Array);
    $file2ArrayKeys = array_keys($file2Array);
    // разобрали json на массивы
    $keyArray = array_unique(array_merge($file1ArrayKeys, $file2ArrayKeys));
    sort($keyArray);
    // почистили и отсортировали массив ключей
    $resultString = "{\n";
    // проверяем значение по ключам
    foreach ($keyArray as $key) {
        // оба json содержат данные по ключу
        if ((array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $value2 = $file2Array[$key];
            if ($value1 === $value2) {
            // данные одинаковые
                $resultString .= "    " . $key . ": " . checkValueType($value1) . "\n";
            } else {
            // данные отличаются
                $resultString .= "  - " . $key . ": " . checkValueType($value1) . "\n";
                $resultString .= "  + " . $key . ": " . checkValueType($value2) . "\n";
            }
        } elseif ((array_key_exists($key, $file1Array)) && (!array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $resultString .= "  - " . $key . ": " . checkValueType($value1) . "\n";
            // данные только в первом json
        } elseif ((!array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value2 = $file2Array[$key];
            $resultString .= "  + " . $key . ": " . checkValueType($value2) . "\n";
            // данные только во втором json
        }
    }
    $resultString .= "}";
    // дополняем строку
    var_dump($resultString);
    return $resultString;
}
