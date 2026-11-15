<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Challenge::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Challenge::create($this->validated($request)), 201);
    }

    public function show(Challenge $challenge): JsonResponse
    {
        return response()->json($challenge);
    }

    public function update(Request $request, Challenge $challenge): JsonResponse
    {
        $challenge->update($this->validated($request));

        return response()->json($challenge->fresh());
    }

    public function destroy(Challenge $challenge): JsonResponse
    {
        $challenge->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);
    }
}
