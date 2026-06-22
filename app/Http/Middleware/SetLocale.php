<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\Locale;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale', 'fa'));

        if (! in_array($locale, Locale::SUPPORTED, true)) {
            $locale = 'fa';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
