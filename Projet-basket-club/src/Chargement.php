<?php

declare(strict_types=1);

namespace App;

// App\Dossier\Classe => src/Dossier/Classe.php
spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
