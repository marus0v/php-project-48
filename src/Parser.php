<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function getFileData(string $fileName): string
{
    if (!file_exists($fileName)) {
        return throw new \Exception("File not found: '$fileName'");
    }

    return file_get_contents($fileName);
}

function getArrayFromJson(string $fileName)
{
    $file = getFileData($fileName);
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
