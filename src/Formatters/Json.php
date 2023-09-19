<?php

namespace Differ\Formatters\Json;

function showJsonDiff(array $differ): string
{
    // var_dump(json_encode($differ, JSON_PRETTY_PRINT));
    return json_encode($differ, JSON_PRETTY_PRINT);
}
