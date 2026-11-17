<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoresBase64Images
{
    /**
     * Simpan gambar hasil crop (data URL base64) ke disk 'public'.
     *
     * @return string|null Path relatif file yang tersimpan, atau null bila data tidak valid.
     */
    protected function storeBase64Image(string $base64Data, string $directory): ?string
    {
        if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $base64Data)) {
            return null;
        }

        $rawImage = base64_decode(substr($base64Data, strpos($base64Data, ',') + 1), true);

        if ($rawImage === false || $rawImage === '') {
            return null;
        }

        $imageInfo = @getimagesizefromstring($rawImage);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if ($imageInfo === false || ! in_array($imageInfo['mime'], $allowedMimes, true)) {
            return null;
        }

        $extension = match ($imageInfo['mime']) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        };

        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

        Storage::disk('public')->put($path, $rawImage);

        return $path;
    }
}
