<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ContactItem::orderBy('sort')->get());
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(ContactItem::create($this->validated($request)), 201);
    }

    public function show(ContactItem $contactItem): JsonResponse
    {
        return response()->json($contactItem);
    }

    public function update(Request $request, ContactItem $contactItem): JsonResponse
    {
        $contactItem->update($this->validated($request));

        return response()->json($contactItem->fresh());
    }

    public function destroy(ContactItem $contactItem): JsonResponse
    {
        $contactItem->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'icon' => 'nullable|string|max:60',
            'label' => 'required|string|max:255',
            'value' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);
    }
}
