<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutSectionController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(AboutSection::firstOrCreate([], []));
    }

    public function update(Request $request): JsonResponse
    {
        $section = AboutSection::firstOrCreate([]);

        $data = $request->validate([
            'heading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'catchphrase' => 'nullable|string|max:255',
            'paragraph_2' => 'nullable|string',
            'paragraph_3' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:3072',
        ]);

        $data['image'] = $this->resolveUpload($request, 'image', $section->image, 'sections');

        $section->update($data);

        return response()->json($section->fresh());
    }
}
