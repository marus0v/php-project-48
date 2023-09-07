<?php

namespace Differ\Formatters;

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
                var_dump($key);
                var_dump($level);
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

function stringify($value)
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

function showFormatted($differ, $formatName)
{
    switch ($formatName) {
        // case 'plain':
        //    return  showPlain($differ);
        // case 'json':
        //    return showJson($differ);
        case 'stylish':
            return stringify($differ);
        default:
            throw new \Exception("Unknown format: $formatName");
    }
}
