<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CesiumProxyController extends Controller
{
    /**
     * Proxy request to Cesium Ion Asset Endpoint.
     *
     * @param Request $request
     * @param string $assetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetEndpoint(Request $request, $assetId)
    {
        // 1. Input Validation
        if (!is_numeric($assetId)) {
            Log::warning("Invalid Cesium Asset ID attempted: {$assetId} from IP " . $request->ip());
            return response()->json(['error' => 'Invalid Asset ID'], 400);
        }

        $cesiumToken = config('cesium.access_token');

        if (empty($cesiumToken)) {
            Log::critical('Cesium access token is missing in server configuration.');
            return response()->json(['error' => 'Service configuration error'], 500);
        }

        // 2. Logging
        Log::info("Proxying Cesium asset request", [
            'asset_id' => $assetId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // 3. Proxy Request
            // Pass through any query parameters sent by the client, BUT remove any client-side access_token
            $queryParams = $request->query();
            unset($queryParams['access_token']);

            $response = Http::withToken($cesiumToken)
                ->get("https://api.cesium.com/v1/assets/{$assetId}/endpoint", $queryParams);

            if ($response->failed()) {
                Log::error("Cesium API Upstream Error", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'asset_id' => $assetId
                ]);
                return response()->json(['error' => 'Failed to retrieve asset endpoint'], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error("Cesium Proxy Exception", [
                'message' => $e->getMessage(),
                'asset_id' => $assetId
            ]);
            return response()->json(['error' => 'Proxy service unavailable'], 500);
        }
    }

    /**
     * Proxy request to Cesium Geocode Search.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function geocode(Request $request)
    {
        return $this->handleGeocodeRequest($request, 'search');
    }

    /**
     * Proxy request to Cesium Geocode Autocomplete.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function geocodeAutocomplete(Request $request)
    {
        return $this->handleGeocodeRequest($request, 'autocomplete');
    }

    /**
     * Handle common geocode proxy logic.
     *
     * @param Request $request
     * @param string $endpointType 'search' or 'autocomplete'
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleGeocodeRequest(Request $request, $endpointType)
    {
        $cesiumToken = config('cesium.access_token');

        if (empty($cesiumToken)) {
            Log::critical('Cesium access token is missing in server configuration.');
            return response()->json(['error' => 'Service configuration error'], 500);
        }

        $text = $request->query('text');
        if (empty($text)) {
            return response()->json(['error' => 'Search text is required'], 400);
        }

        Log::info("Proxying Cesium geocode {$endpointType} request", [
            'text' => $text,
            'ip' => $request->ip()
        ]);

        try {
            $queryParams = $request->query();
            unset($queryParams['access_token']); // Remove client placeholder

            $response = Http::withToken($cesiumToken)
                ->get("https://api.cesium.com/v1/geocode/{$endpointType}", $queryParams);

            if ($response->failed()) {
                Log::error("Cesium Geocode API Upstream Error", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'endpoint' => $endpointType
                ]);
                return response()->json(['error' => 'Failed to retrieve geocode results'], $response->status());
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error("Cesium Geocode Proxy Exception", [
                'message' => $e->getMessage(),
                'endpoint' => $endpointType
            ]);
            return response()->json(['error' => 'Proxy service unavailable'], 500);
        }
    }
}
