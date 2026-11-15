<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcessStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessStepController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ProcessStep::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(ProcessStep::create($this->validated($request)), 201);
    }

    public function show(ProcessStep $processStep): JsonResponse
    {
        return response()->json($processStep);
    }

    public function update(Request $request, ProcessStep $processStep): JsonResponse
    {
        $processStep->update($this->validated($request));

        return response()->json($processStep->fresh());
    }

    public function destroy(ProcessStep $processStep): JsonResponse
    {
        $processStep->delete();

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
