<?php

namespace Storm\Query;

use DateTime;
use DateTimeZone;

class ParameterNormalizer
{
    public static function normalize(array $parameters): array
    {
        $normalized = array();
        foreach($parameters as $key => $value) {
            if ($value instanceof DateTime) {
                $value->setTimezone(new DateTimeZone('UTC'));
                $value = $value->format('Y-m-d H:i:s');
            }
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $normalized[$key] = $value;
        }
        return $normalized;
    }
}