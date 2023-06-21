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
    $file1 = file_get_contents($fileName1);
    $file2 = file_get_contents($fileName2);
    // var_dump($file1);
    $file1Array = json_decode($file1, true);
    // var_dump($file1Array);
    $file2Array = json_decode($file2, true);
    // var_dump($file2Array);
    $file1ArrayKeys = array_keys(getArrayFromJson($fileName1));
    // var_dump($file1ArrayKeys[0]);
    // $file1ArrayKeysVal = array_values($file1ArrayKeys);
    // var_dump($file1ArrayKeysVal[0]);
    // $file1ArrayKeysValue = [];
    // foreach ($file1ArrayKeys as $key) {
    //     $value = explode(' ' , $key);
    //     var_dump($value[1]);
    //     $file1ArrayKeysValue[] = $value[1];
    // }
    // var_dump($file1ArrayKeysValue);
    $file2ArrayKeys = array_keys(getArrayFromJson($fileName2));
    // var_dump($file2ArrayKeys);
    $keyArray = array_unique(array_merge($file1ArrayKeys, $file2ArrayKeys));
    sort($keyArray);
    // var_dump($keyArray);
    // var_dump($keyArray[0]);
    $result = [];
    foreach ($keyArray as $key) {
        var_dump($key);
        var_dump($file1Array[$key]);
        var_dump($file2Array[$key]);
        if ((array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $value2 = $file2Array[$key];
            // var_dump($value1);
            // var_dump($value2);
            if ($value1 === $value2) {
                $result[] = [$key => $value1];
                // var_dump($result);
            }
        } 
        else if ((array_key_exists($key, $file1Array)) && (!array_key_exists($key, $file2Array))) {
            $value1 = $file1Array[$key];
            $result[] = [$key => $value1];
            // var_dump($value1);
        }
        else if ((!array_key_exists($key, $file1Array)) && (array_key_exists($key, $file2Array))) {
            $value2 = $file2Array[$key];
            $result[] = [$key => $value2];
            // var_dump($value2);
        }
    var_dump($result);
    return $result;
    }

    // print_r('Hello, Gendiff');
}