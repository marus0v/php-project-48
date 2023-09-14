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
