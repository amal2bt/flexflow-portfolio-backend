<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisionSectionController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(VisionSection::with('points')->firstOrCreate([], []));
    }

    public function update(Request $request): JsonResponse
    {
        $section = VisionSection::firstOrCreate([]);

        $data = $request->validate([
            'vision_text' => 'nullable|string',
            'vision_text_2' => 'nullable|string',
            'vision_text_3' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:3072',
            'points' => 'nullable|array|max:50',
            'points.*' => 'string|max:500',
        ]);

        $data['image'] = $this->resolveUpload($request, 'image', $section->image, 'sections');

        $section->update([
            'vision_text' => $data['vision_text'] ?? $section->vision_text,
            'vision_text_2' => $data['vision_text_2'] ?? $section->vision_text_2,
            'vision_text_3' => $data['vision_text_3'] ?? $section->vision_text_3,
            'image' => $data['image'],
        ]);

        // Replace the checklist points when provided. Always use the validated
        // payload (`$data`) rather than re-reading raw request input.
        if (array_key_exists('points', $data)) {
            $section->points()->delete();
            foreach (array_values($data['points'] ?? []) as $i => $point) {
                $section->points()->create(['point' => $point, 'sort' => $i]);
            }
        }

        return response()->json($section->fresh()->load('points'));
    }
}
