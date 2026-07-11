<?php

namespace App\Http\Controllers\Admin;


use App\Helpers\PermissionsHelper;
use App\Services\DownloadService;
use App\Models\Redirect;
use App\Support\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;


class RedirectController  extends Controller
{
    /**
     * Inject the download service used to generate redirect reports.
     */
    public function __construct(private DownloadService $downloadService)
    {
        parent::__construct();
    }

    /**
     * Show the redirect tracking overview page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.redirect.index', [
            'infoAlert' => __('frontend.hint.redirect_index'),
            'title' => __('frontend.title.redirect_index'),
        ]);
    }

    /**
     * Clear all redirect tracking statistics.
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        abort_unless(PermissionsHelper::has_permission('admin'), 403);

        try {
            Redirect::truncate();

            return response()->json([
                'success' => true,
                'message' => __('message.statistics_cleared'),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => __('frontend.str.delete_error'),
            ], 500);
        }
    }

    /**
     * Download redirect tracking details for a tracked URL.
     *
     * @param string $url
     * @return StreamedResponse
     */
    public function download(string $url): StreamedResponse
    {
        abort_unless(PermissionsHelper::has_permission('admin'), 403);
        abort_unless($this->canViewRedirectUrl($url), 404);

        return $this->downloadService->redirect($url);
    }

    /**
     * Show redirect tracking details for a tracked URL.
     *
     * @param string $url
     * @return View
     */
    public function info(string $url): View
    {
        abort_unless($this->canViewRedirectUrl($url), 404);

        return view('admin.redirect.info', [
            'url' => $url,
            'infoAlert' => __('frontend.hint.redirectlog_info'),
            'title' => __('frontend.title.redirect_info'),
        ]);
    }

    private function canViewRedirectUrl(string $url): bool
    {
        $decodedUrl = $this->decodeRouteBase64($url);
        $query = Redirect::query()->where('url', $decodedUrl);

        ProjectAccess::scopeRedirectQuery($query);

        return $query->exists();
    }

    private function decodeRouteBase64(string $value): string
    {
        $normalized = strtr($value, '-_', '+/');
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($normalized, true) ?: '';
    }
}
