<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $hasApplicationKey = trim((string) env('APP_KEY')) !== '';
        $isInstalled = $hasEnvironmentFile && $hasApplicationKey;

        if (!$isInstalled && !$request->is('install*')) {
            return redirect()->to('/install');
        }

        if ($isInstalled && $request->is('install*') && !$request->is('install/complete')) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
