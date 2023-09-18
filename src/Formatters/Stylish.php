<?php

namespace Differ\Formatters\Stylish;

const SPACER = '    ';
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

function formStylishDiff(array $diff, int $level = 0): string
{
    $indent = str_repeat(SPACER, $level);
    $output = [];
    foreach ($diff as $key => $node) {
        switch ($node['status']) {
            case 'added':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . ADD . "{$key}: "
                : $output[] = "{$indent}" . ADD . "{$key}: {$formattedValue}";
                // var_dump($output);
                break;
            case 'removed':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . SUB . "{$key}: "
                : $output[] = "{$indent}" . SUB . "{$key}: {$formattedValue}";
                // var_dump($output);
                break;
            case 'updated':
                $formattedOldValue = formatValue($node['oldValue'], $level + 1);
                $formattedNewValue = formatValue($node['newValue'], $level + 1);
                $formattedOldValue === ''
                ? $output[] = "{$indent}" . SUB . "{$key}: "
                : $output[] = "{$indent}" . SUB . "{$key}: {$formattedOldValue}";
                $formattedNewValue === ''
                ? $output[] = "{$indent}" . ADD . "{$key}: "
                : $output[] = "{$indent}" . ADD . "{$key}: {$formattedNewValue}";
                // var_dump($output);
                break;
            case 'nested':
                $output[] = "{$indent}" . SPACE . "{$key}: {\n"
                    . formStylishDiff($node['children'], $level + 1)
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
    // var_dump($output);
    return implode("\n", $output);
    // return $output;
}

function formatValue($value, int $level): string
{
    if (is_array($value)) {
        $formattedArray = array_map(function ($key, $val) use ($level) {
            $formattedValue = formatValue($val, $level + 1);
            $keyIndent = str_repeat(SPACER, $level);
            return "{$keyIndent}" . SPACE . "{$key}: {$formattedValue}";
        }, array_keys($value), $value);
        return "{\n" . implode("\n", $formattedArray) . "\n" . str_repeat(SPACER, $level) . "}";
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return trim($value, "'");
}

function showStylishDiff(array $diff): string
{
    // $stylishDiff = implode("\n", formStylishDiff($diff));
    // var_dump($stylishDiff);
    return "{\n" . formStylishDiff($diff) . "\n}";
}
