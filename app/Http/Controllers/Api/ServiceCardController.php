<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCardController extends Controller
{
    public function index(Service $service): JsonResponse
    {
        return response()->json($service->cards);
    }

    public function store(Request $request, Service $service): JsonResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveUpload($request, 'image', null, 'services');

        $card = $service->cards()->create($data);

        return response()->json($card, 201);
    }

    public function show(ServiceCard $card): JsonResponse
    {
        return response()->json($card);
    }

    public function update(Request $request, ServiceCard $card): JsonResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveUpload($request, 'image', $card->image, 'services');

        $card->update($data);

        return response()->json($card->fresh());
    }

    public function destroy(ServiceCard $card): JsonResponse
    {
        $this->deleteUpload($card->image);
        $card->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:3072',
            'sort' => 'nullable|integer',
        ]);
    }
}