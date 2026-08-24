<?php

namespace App\Services;

class DocumentAssetService
{
    public function dataUri(?string $relativePath): ?string
    {
        if (! $relativePath) return null;
        $path = public_path(ltrim($relativePath, '/'));
        if (! is_file($path)) return null;
        $mime = mime_content_type($path) ?: 'image/png';
        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }
}
