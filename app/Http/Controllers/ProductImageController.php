<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductImageController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        abort_unless(str_starts_with($path, 'products/'), 404);
        abort_unless(basename($path) === substr($path, strlen('products/')), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
