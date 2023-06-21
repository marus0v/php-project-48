<?php

namespace GenDiff\GenDiff;

// use function cli\line;
// use function cli\prompt;
// use function BrainGames\Engine\runGame;

// use const BrainGames\Engine\TOTALWINSNUMBER;

// const DESCRIPTION = 'Answer "yes" if the number is even, otherwise answer "no".';

function getArrayFromJson($fileName)
{
    $file = file_get_contents($fileName);
    return $fileArray = json_decode($file, true);
}

function genDiff($fileName1, $fileName2)
{
    $file1Array = getArrayFromJson($fileName1);
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
                $resultString .= "    " . $key . ": " . $value1 . "\n";
            } else {
            // данные отличаются
                $resultString .= "  - " . $key . ": " . $value1 . "\n";
                $resultString .= "  + " . $key . ": " . $value2 . "\n";
            }
        } 
        else if ((array_key_exists($key, $file1Array)) && (!array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $resultString .= "  - " . $key . ": " . $value1 . "\n";
            // данные только в первом json
        }
        else if ((!array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value2 = $file2Array[$key];
            $resultString .= "  + " . $key . ": " . $value2 . "\n";
            // данные только во втором json
        }
    }
    $resultString .= "}";
    // дополняем строку
    var_dump($resultString);
    return $resultString;
}