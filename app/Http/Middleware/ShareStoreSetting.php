<?php

namespace App\Http\Middleware;

use App\Models\StoreSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareStoreSetting
{
    /**
     * Bagikan pengaturan toko (termasuk logo) ke seluruh view yang dirender,
     * sehingga navbar/header selalu mendapat data terbaru dari database.
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('storeSetting', StoreSetting::current());

        return $next($request);
    }
}
