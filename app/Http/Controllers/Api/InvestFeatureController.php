<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestFeatureController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(InvestFeature::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(InvestFeature::create($this->validated($request)), 201);
    }

    public function show(InvestFeature $investFeature): JsonResponse
    {
        return response()->json($investFeature);
    }

    public function update(Request $request, InvestFeature $investFeature): JsonResponse
    {
        $investFeature->update($this->validated($request));

        return response()->json($investFeature->fresh());
    }

    public function destroy(InvestFeature $investFeature): JsonResponse
    {
        $investFeature->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'icon' => 'nullable|string|max:60',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);
    }
}
