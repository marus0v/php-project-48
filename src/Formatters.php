<?php

namespace GenDiff\Formatters;

use function GenDiff\Formatters\Stylish\showStylishDiff;
use function GenDiff\Formatters\Plain\showPlainDiff;
use function GenDiff\Formatters\Json\showJsonDiff;

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
