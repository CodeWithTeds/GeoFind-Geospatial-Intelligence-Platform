<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class IpControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. Check Blacklist (simulated - in real app, fetch from DB or Cache)
        $blacklist = config('security.ip_blacklist', []);
        if (in_array($ip, $blacklist)) {
            Log::warning("Blocked access attempt from blacklisted IP: {$ip}");
            abort(403, 'Access Denied');
        }

        // 2. Check Whitelist (if enabled)
        // For a public game, strict whitelisting is usually OFF, but we implement the logic.
        $whitelistEnabled = config('security.ip_whitelist_enabled', false);
        $whitelist = config('security.ip_whitelist', []);

        if ($whitelistEnabled && !in_array($ip, $whitelist)) {
            Log::warning("Blocked access attempt from non-whitelisted IP: {$ip}");
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
