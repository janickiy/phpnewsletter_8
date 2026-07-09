<?php

namespace Tests\Unit;

use App\Http\Controllers\InstallController;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class InstallControllerTest extends TestCase
{
    public function test_detect_app_url_uses_site_root_for_root_installation(): void
    {
        $request = Request::create('https://site3.local/install/install-app', 'POST', [], [], [], [
            'SCRIPT_NAME' => '/index.php',
        ]);

        $this->assertSame('https://site3.local/', $this->detectAppUrl($request));
    }

    public function test_detect_app_url_keeps_subdirectory_installation_path(): void
    {
        $request = Request::create('https://site3.local/phpnewsletter/install/install-app', 'POST', [], [], [], [
            'SCRIPT_NAME' => '/phpnewsletter/index.php',
        ]);

        $this->assertSame('https://site3.local/phpnewsletter', $this->detectAppUrl($request));
    }

    private function detectAppUrl(Request $request): string
    {
        $method = new ReflectionMethod(InstallController::class, 'detectAppUrl');

        return $method->invoke(new InstallController(), $request);
    }
}
