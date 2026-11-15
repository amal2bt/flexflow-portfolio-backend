<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcessSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessSectionController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(ProcessSection::firstOrCreate([], [
            'title' => 'Our Proven Development Process',
        ]));
    }

    public function update(Request $request): JsonResponse
    {
        $section = ProcessSection::firstOrCreate([]);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:3072',
        ]);

        $data['image'] = $this->resolveUpload($request, 'image', $section->image, 'process');

        $section->update($data);

        return response()->json($section->fresh());
    }
}
