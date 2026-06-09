<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class Upload
{
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    private const ALLOWED_EXT  = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

    public static function save(UploadedFile $file, string $subfolder = 'bukti'): string
    {
        $maxBytes = (int) config('app.upload_max_mb', 5) * 1024 * 1024;

        if ($file->getSize() > $maxBytes) {
            throw new \RuntimeException('File terlalu besar. Maksimal ' . config('app.upload_max_mb', 5) . 'MB.');
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            throw new \RuntimeException('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('Ekstensi file tidak valid.');
        }

        // UUID filename to avoid collisions and enumeration
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        // On shared hosting index.php lives at the doc root (= base_path),
        // so public_path() is a sub-directory that isn't web-accessible at /uploads/.
        // Use base_path('uploads') in that case; public_path('uploads') otherwise.
        $uploadsRoot = file_exists(base_path('index.php'))
            ? base_path('uploads')
            : public_path('uploads');
        $dest = $uploadsRoot . DIRECTORY_SEPARATOR . $subfolder;

        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        $file->move($dest, $filename);

        return "/uploads/{$subfolder}/{$filename}";
    }
}
