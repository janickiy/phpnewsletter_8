<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class Install
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $hasEnvironmentFile = file_exists(base_path('.env'));

        if (!$hasEnvironmentFile && !$request->is('install*')) {
            return redirect()->to('/install');
        }

        if ($hasEnvironmentFile && $request->is('install*')) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
