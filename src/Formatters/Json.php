<?php

namespace Differ\Formatters\Json;

function showJsonDiff(array $differ): string
{
    return json_encode($differ, JSON_PRETTY_PRINT);
}
