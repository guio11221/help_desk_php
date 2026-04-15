<?php

declare(strict_types=1);

final class Validator
{
    public static function email(string $value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function minLength(string $value, int $min): bool
    {
        return mb_strlen(trim($value)) >= $min;
    }

    public static function in(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }
}

