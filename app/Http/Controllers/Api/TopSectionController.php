<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TopSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopSectionController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(TopSection::firstOrCreate([], []));
    }

    public function update(Request $request): JsonResponse
    {
        $section = TopSection::firstOrCreate([]);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:3072',
        ]);

        $data['image'] = $this->resolveUpload($request, 'image', $section->image, 'sections');

        $section->update($data);

        return response()->json($section->fresh());
    }
}
