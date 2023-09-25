<?php

namespace Differ\Formatters\Stylish;

const SPACER = '    ';
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

function strbool(mixed $value): string
{
    return $value ? 'true' : 'false';
}

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
                break;
            case 'removed':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . SUB . "{$key}: "
                : $output[] = "{$indent}" . SUB . "{$key}: {$formattedValue}";
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
                break;
            case 'nested':
                $output[] = "{$indent}" . SPACE . "{$key}: {\n"
                    . formStylishDiff($node['children'], $level + 1)
                    . "\n{$indent}" . SPACE . "}";
                break;
            case 'unchanged':
                $formattedValue = formatValue($node['value'], $level + 1);
                $formattedValue === ''
                ? $output[] = "{$indent}" . SPACE . "{$key}:"
                : $output[] = "{$indent}" . SPACE . "{$key}: {$formattedValue}";
                break;
        }
    }
    // var_dump($output);
    return implode("\n", $output);
    // return $output;
}

function formatValue(mixed $value, int $level): string
{
    // $result = '';
    if (is_array($value)) {
        $formattedArray = array_map(function ($key, $val) use ($level) {
            $formattedValue = formatValue($val, $level + 1);
            $keyIndent = str_repeat(SPACER, $level);
            return "{$keyIndent}" . SPACE . "{$key}: {$formattedValue}";
        }, array_keys($value), $value);
        return "{\n" . implode("\n", $formattedArray) . "\n" . str_repeat(SPACER, $level) . "}";
    } elseif (is_null($value)) {
        return 'null';
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
        // $result = strbool($value);;
    }
    return trim($value, "'");
    // return $result;
}

function showStylishDiff(array $diff): string
{
    // $stylishDiff = implode("\n", formStylishDiff($diff));
    // var_dump($stylishDiff);
    return "{\n" . formStylishDiff($diff) . "\n}";
}
