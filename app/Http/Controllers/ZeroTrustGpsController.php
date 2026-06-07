<?php

namespace App\Http\Controllers;

use App\Services\GpsLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZeroTrustGpsController extends Controller
{
    public function __construct(
        protected GpsLocationService $gpsService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
        ]);

        $user = $request->user();
        $gps = $this->gpsService->store($request, $user->id, $data);

        return response()->json([
            'status' => 'ok',
            'gps' => [
                'latitude' => $gps['latitude'],
                'longitude' => $gps['longitude'],
            ],
        ]);
    }
}
