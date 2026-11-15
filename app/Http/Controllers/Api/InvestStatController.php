<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestStatController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(InvestStat::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(InvestStat::create($this->validated($request)), 201);
    }

    public function show(InvestStat $investStat): JsonResponse
    {
        return response()->json($investStat);
    }

    public function update(Request $request, InvestStat $investStat): JsonResponse
    {
        $investStat->update($this->validated($request));

        return response()->json($investStat->fresh());
    }

    public function destroy(InvestStat $investStat): JsonResponse
    {
        $investStat->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|integer',
            'sort' => 'nullable|integer',
        ]);
    }
}
