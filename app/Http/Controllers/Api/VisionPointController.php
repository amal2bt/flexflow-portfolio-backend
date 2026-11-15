<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionPoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisionPointController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(VisionPoint::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(VisionPoint::create($this->validated($request)), 201);
    }

    public function show(VisionPoint $visionPoint): JsonResponse
    {
        return response()->json($visionPoint);
    }

    public function update(Request $request, VisionPoint $visionPoint): JsonResponse
    {
        $visionPoint->update($this->validated($request));

        return response()->json($visionPoint->fresh());
    }

    public function destroy(VisionPoint $visionPoint): JsonResponse
    {
        $visionPoint->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'vision_section_id' => 'required|exists:vision_sections,id',
            'point' => 'required|string',
            'sort' => 'nullable|integer',
        ]);
    }
}
