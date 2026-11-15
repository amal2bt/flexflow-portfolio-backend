<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Bound the page size so a caller cannot ask for millions of rows and
        // exhaust memory/CPU (resource-exhaustion DoS). (int) casting keeps the
        // value parameter-bound and non-injectable.
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 8);
        $perPage = max(1, min(100, $perPage));

        return response()->json(Partner::orderBy('id')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['logo'] = $this->storeUpload($request, 'logo', null, 'partners');

        return response()->json(Partner::create($data), 201);
    }

    public function show(Partner $partner): JsonResponse
    {
        return response()->json($partner);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $data = $this->validated($request);
        $data['logo'] = $this->resolveUpload($request, 'logo', $partner->logo, 'partners');

        $partner->update($data);

        return response()->json($partner->fresh());
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $this->deleteUpload($partner->logo);
        $partner->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'sub' => 'nullable|string|max:255',
            'mark' => 'nullable|string|max:60',
            'cls' => 'nullable|string|max:60',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'sort' => 'nullable|integer',
        ]);
    }
}
