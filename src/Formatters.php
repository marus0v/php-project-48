<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\showStylishDiff;
use function Differ\Formatters\Plain\showPlainDiff;
use function Differ\Formatters\Json\showJsonDiff;

function showFormatted($differ, $formatName)
{
    switch ($formatName) {
        case 'plain':
            return showPlainDiff($differ);
        case 'json':
            return showJsonDiff($differ);
        case 'stylish':
            return showStylishDiff($differ);
        default:
            throw new \Exception("Unknown format: $formatName");
    }
}
