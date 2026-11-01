<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Protège les formulaires contre les attaques CSRF : un jeton secret est stocké
 * en session et doit être renvoyé par le formulaire pour que l'envoi soit accepté.
 */
class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    /**
     * Retourne le jeton de la session (et le crée s'il n'existe pas encore).
     */
    public static function get(): string
    {
        self::startSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Vérifie que le jeton envoyé par le formulaire est bien celui de la session.
     */
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
