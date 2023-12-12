<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Locale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(
            in_array($request->header('Accept-Language'), ['ru', 'uz', 'en']) ? $request->header('Accept-Language') : 'ru'
        );
        return $next($request);
    }
}
