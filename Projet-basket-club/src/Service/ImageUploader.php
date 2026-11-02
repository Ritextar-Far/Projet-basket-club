<?php

declare(strict_types=1);

namespace App\Service;

use Respect\Validation\Validator as v;

class ImageUploader
{
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    private const MAX_SIZE = '2MB';

    public function __construct(private string $directory)
    {
    }

    public function hasFile(?array $file): bool
    {
        return $file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function validate(array $file): ?string
    {
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            return 'L\'image ne doit pas dépasser 2 Mo.';
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Une erreur est survenue lors de l\'envoi de l\'image.';
        }

        if (!v::size(null, self::MAX_SIZE)->validate($file['tmp_name'])) {
            return 'L\'image ne doit pas dépasser 2 Mo.';
        }

        if (!v::in(array_keys(self::TYPES))->validate($this->getMimeType($file['tmp_name']))) {
            return 'Le fichier doit être une image JPG, PNG, GIF ou WEBP.';
        }

        return null;
    }

    public function save(array $file): string
    {
        $extension = self::TYPES[$this->getMimeType($file['tmp_name'])];
        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;

        move_uploaded_file($file['tmp_name'], $this->directory . '/' . $fileName);

        return $fileName;
    }

    private function getMimeType(string $path): string
    {
        return (string) (new \finfo(FILEINFO_MIME_TYPE))->file($path);
    }
}
