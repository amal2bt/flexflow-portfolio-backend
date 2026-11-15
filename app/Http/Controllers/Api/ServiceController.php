<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Service::with('cards')->orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']).'-'.Str::lower(Str::random(4));

        $service = Service::create($data);

        return response()->json($service->load('cards'), 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json($service->load('cards'));
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $service->update($this->validated($request, $service));

        return response()->json($service->fresh()->load('cards'));
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'slug' => 'nullable|string|max:255|unique:services,slug,'.($service?->id ?? 'NULL'),
            'title' => 'required|string|max:255',
            'short' => 'nullable|string',
            'icon' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:30',
            'side_title' => 'nullable|string|max:255',
            'side_copy' => 'nullable|string',
            'heading' => 'nullable|string|max:255',
            'intro' => 'nullable|string',
            'closing' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);
    }
}
