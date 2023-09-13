<?php

namespace GenDiff\Formatters\Plain;

function formPlainDiff($value, array $parentKeys = [])
{
    $output = [];

    foreach ($value as $key => $node) {
        $currentKeys = [...$parentKeys, $key];
        $valuePath = implode('.', $currentKeys);

        switch ($node['status']) {
            case 'added':
                $formattedValue = formatValue($node['value']);
                $output[] = "Property '{$valuePath}' was added with value: {$formattedValue}";
                break;
            case 'removed':
                $formattedValue = formatValue($node['value']);
                $output[] = "Property '{$valuePath}' was removed";
                break;
            case 'updated':
                $formattedOldValue = formatValue($node['oldValue']);
                $formattedNewValue = formatValue($node['newValue']);
                $output[] = "Property '{$valuePath}' was updated. From {$formattedOldValue} to {$formattedNewValue}";
                break;
            case 'nested':
                $output = array_merge($output, formPlainDiff($node['children'], $currentKeys));
                break;
        }
    }

    return $output;
}

function formatValue(mixed $value)
{
    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return $value;
}

function showPlainDiff(array $diff): string
{
    $plainDiff = formPlainDiff($diff);
    return "{\n" . implode("\n", $plainDiff) . "\n}";
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
                $subResult .= "Property '" . $key . "." . $subKey . " was added with value: " . processValue($subSubSubValue) . "\n";
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
                            $subLine = "Property '" . $key . "." . $subSubKey . "' was added with value: " . processValue($subSubSubValue[$subSubKey]) . "\n";
                        case '  - ':
                            $subLine = "Property '" . $key  . "." . $subSubKey . "' was removed\n";
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
