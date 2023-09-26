<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function formPlainDiff(array $diff, array $parentKeys = []): array
{
    /* $output = [];

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

    return $output; */
    return array_map(function ($node) use ($parentKeys) {
        $key = $node['key'];
        $currentKeys = [...$parentKeys, $key];
        $propertyPath = implode('.', $currentKeys);

        switch ($node['status']) {
            case 'added':
                $formattedValue = formatValue($node['value']);
                return ["Property '$propertyPath' was added with value: $formattedValue"];
            case 'removed':
                return ["Property '$propertyPath' was removed"];
            case 'nested':
                return formPlainDiff($node['children'], $currentKeys);
            case 'updated':
                $formattedOldValue = formatValue($node['oldValue']);
                $formattedNewValue = formatValue($node['newValue']);
                return ["Property '$propertyPath' was updated. From $formattedOldValue to $formattedNewValue"];
            case 'unchanged':
                return [];
        }
    }, $diff);

}

function formatValue(mixed $value): string
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
    return implode("\n", flatten(formPlainDiff($diff)));
}
