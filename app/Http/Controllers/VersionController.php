<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class VersionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RequestException
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $itemId = config('installer.item_id');

        $version = Http::acceptJson()
            ->get(config('installer.site_url') . '/api/version', [
            'item_id' => $itemId,
        ]);

        if ($version->failed()) {
            throw new \Exception('From CodeShaper server: ' . $version['message']);
        }

        $response = $version->json();

        $current = config('app.version');
        $latest = $response['latest'];
        // 1 means current one is greater than latest
        // 0 means equal
        // -1 means current one is less than latest
        $isUpdateAvailable = false;
        if (version_compare($current, $latest) === -1) {
            $isUpdateAvailable = true;
        }

        $version = [
            'current' => config('app.version'),
            'latest' => $response['latest'],
            'is_update_available' => $isUpdateAvailable,
        ];

        return response()->json($version);
    }
}