<?php

if (!function_exists('array_find')) {
    function array_find($array, $callback)
    {
        foreach($array as $item) {
            if (call_user_func($callback, $item) === true) {
                return $item;
            }
        }
        return false;
    }
}
function remove_new_lines($text   ): string
{
    return str_replace("\n", " ", $text);
}

function get_nth_line($text, $i): ?string
{
    $element = explode("\n", $text);
    if ($i <= count($element)) {
        return $element[$i];
    }
    return null;
}