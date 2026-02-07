<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy
        // Allows scripts from self, cdn.tailwindcss.com, and Cloudflare Turnstile
        // Allows styles from self, unsafe-inline (for Tailwind/dynamic styles), and Google Fonts
        // Allows fonts from self, Google Fonts, and Cloudflare
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://challenges.cloudflare.com https://*.cloudflare.com https://unpkg.com https://cesium.com https://dev.virtualearth.net https://*.virtualearth.net blob:; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://cesium.com; " .
               "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; " .
               "img-src 'self' data: https://* blob:; " . // Blob needed for Cesium/Maps
               "connect-src 'self' https://challenges.cloudflare.com https://*.cloudflare.com https://api.mapbox.com https://events.mapbox.com https://*.cesium.com https://cesium.com https://dev.virtualearth.net https://*.virtualearth.net blob: data:; " .
               "frame-src 'self' https://challenges.cloudflare.com https://*.cloudflare.com; " .
               "worker-src 'self' blob: https://cesium.com; " .
               "child-src 'self' blob: https://cesium.com;";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(), camera=()');

        return $response;
    }
}
