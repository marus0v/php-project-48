<?php

namespace GenDiff\Formatters\Stylish;

const SPACER = '    ';
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

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

function showStylishDiff(array $diff, int $level = 0): string
{
    $indent = str_repeat(SPACER, $level);
    $output = [];
    foreach ($diff as $key => $node) {
        switch ($node['status']) {
            case 'added':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . ADD . "{$key}:"
                : $output[] = "{$indent}" . ADD . "{$key}: {$formattedValue}";
                // var_dump($output);
                break;
            case 'removed':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . SUB . "{$key}:"
                : $output[] = "{$indent}" . SUB . "{$key}: {$formattedValue}";
                // var_dump($output);
                break;
            case 'updated':
                $formattedOldValue = formatValue($node['oldValue'], $level + 1);
                $formattedNewValue = formatValue($node['newValue'], $level + 1);
                $formattedOldValue === ''
                ? $output[] = "{$indent}" . SUB . "{$key}:"
                : $output[] = "{$indent}" . SUB . "{$key}: {$formattedOldValue}";
                $formattedNewValue === ''
                ? $output[] = "{$indent}" . ADD . "{$key}:"
                : $output[] = "{$indent}" . ADD . "{$key}: {$formattedNewValue}";
                // var_dump($output);
                break;
            case 'nested':
                $output[] = "{$indent}" . SPACE . "{$key}: {\n"
                    . showStylishDiff($node['children'], $level + 1)
                    . "\n{$indent}" . SPACE . "}";
                // var_dump($output);
                break;
            case 'unchanged':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . SPACE . "{$key}:"
                : $output[] = "{$indent}" . SPACE . "{$key}: {$formattedValue}";
                // var_dump($output);
                break;
        }
    }
    var_dump($output);
    return implode("\n", $output);
}

function formatValue($value, int $depth): string
{
    if (is_array($value)) {
        $formattedArray = array_map(function ($key, $val) use ($depth) {
            $formattedValue = formatValue($val, $depth + 1);
            $keyIndent = str_repeat(" ", $depth * 4);
            return "{$keyIndent}    {$key}: {$formattedValue}";
        }, array_keys($value), $value);
        return "{\n" . implode("\n", $formattedArray) . "\n" . str_repeat(" ", $depth * 4) . "}";
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return trim($value, "'");
}

function getStylishDiff(array $diff): string
{
    return "{\n" . showStylishDiff($diff) . "\n}";
}