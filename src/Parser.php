<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getArrayFromJson(string $fileName)
{
    $file = file_get_contents($fileName);
    return $fileArray = json_decode($file, true);
}

function getArrayFromYAML(string $fileName)
{
    $file = Yaml::parseFile($fileName);
    return $file;
}

function parse(string $fileName)
{
    $extension = strrchr($fileName, '.');
    if (($extension === '.yaml') || ($extension === '.yml')) {
        $array = getArrayFromYAML($fileName);
    } else {
        $array = getArrayFromJson($fileName);
    }
    return $array;
}
