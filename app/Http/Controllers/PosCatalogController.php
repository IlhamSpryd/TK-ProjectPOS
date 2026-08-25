<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosCatalogController extends Controller
{
    /**
     * Mengambil JSON payload katalog dengan caching di sisi browser dan server.
     */
    public function __invoke(Request $request, ProductCatalogService $catalogService): JsonResponse
    {
        $staff = Auth::user();
        if (!$staff) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $store = $staff->getActiveStore();
        if (!$store) {
            return response()->json(['error' => 'No active store'], 403);
        }

        $catalog = $catalogService->getCatalog($store->id, $staff->tenant_id);

        return response()->json($catalog)
            ->setCache(['public' => true, 'max_age' => 60]); // 60 detik cache browser
    }
}
