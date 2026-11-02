<?php

declare(strict_types=1);

namespace App\Security;

class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    public static function get(): string
    {
        self::startSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function isValid(?string $token): bool
    {
        self::startSession();

        return isset($_SESSION[self::SESSION_KEY])
            && is_string($token)
            && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
