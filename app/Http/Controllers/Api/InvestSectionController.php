<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestSectionController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(InvestSection::firstOrCreate([], [
            'title' => 'This Is Where FlexFlow Comes In',
        ]));
    }

    public function update(Request $request): JsonResponse
    {
        $section = InvestSection::firstOrCreate([]);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $data['image'] = $this->resolveUpload($request, 'image', $section->image, 'invest');

        $section->update($data);

        return response()->json($section->fresh());
    }
}
