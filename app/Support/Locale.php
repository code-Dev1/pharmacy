<?php

namespace App\Support;

class Locale
{
    public const SUPPORTED = ['fa', 'ps', 'en'];

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function direction(?string $locale = null): string
    {
        return in_array($locale ?? self::current(), ['fa', 'ps'], true) ? 'rtl' : 'ltr';
    }

    public static function isRtl(?string $locale = null): bool
    {
        return self::direction($locale) === 'rtl';
    }

    public static function fontClass(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'font-rtl' : 'font-sans';
    }

    public static function alignment(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'text-right' : 'text-left';
    }
}
