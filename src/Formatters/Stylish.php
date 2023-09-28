<?php

namespace Differ\Formatters\Stylish;

const SPACER = '    ';
const SPACE = '    ';
const ADD = '  + ';
const SUB = '  - ';

function strbool(bool $value): string
{
    return $value ? 'true' : 'false';
}

function getIndent(int $level = 1): string
{
    return str_repeat(SPACER, $level);
}

function formStylishDiff(array $diff, int $level = 0): string
{
    $output = array_map(function ($node) use ($level) {
        $indent = getIndent($level);
        $key = $node['key'];
        switch ($node['status']) {
            case 'added':
                $formattedValue = stringify($node['value'], $level + 1);
                return "$indent" . ADD . "$key: $formattedValue";
            case 'removed':
                $formattedValue = stringify($node['value'], $level + 1);
                return "$indent" . SUB . "$key: $formattedValue";
            case 'nested':
                $nestedDiff = formStylishDiff($node['children'], $level + 1);
                return "$indent" . SPACER . "$key: {\n$nestedDiff\n$indent    }";
            case 'updated':
                $formattedOldValue = stringify($node['oldValue'], $level + 1);
                $formattedNewValue = stringify($node['newValue'], $level + 1);
                return "$indent" . SUB . "$key: $formattedOldValue\n$indent" . ADD . "$key: $formattedNewValue";
            case 'unchanged':
                $formattedValue = stringify($node['value'], $level + 1);
                return "$indent" . SPACER . "$key: $formattedValue";
        }
    }, $diff);
    return implode("\n", $output);
}

function stringify(mixed $value, int $level): string
{
    if (!is_array($value)) {
        return formatValue($value);
    }

    $indent = getIndent($level);
    $formattedArray = array_map(function ($key, $val) use ($level, $indent) {
        $formattedValue = stringify($val, $level + 1);
        return "$indent" . SPACER . "$key: $formattedValue";
    }, array_keys($value), $value);

    return "{\n" . implode("\n", $formattedArray) . "\n" . $indent . "}";
}

function formatValue(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return trim($value, "'");
    }
}

function showStylishDiff(array $diff): string
{
    return "{\n" . formStylishDiff($diff) . "\n}";
}
