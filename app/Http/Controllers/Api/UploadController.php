<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Generic single-file upload used by the admin "Choose File" inputs.
     *
     * Only the `file` field is accepted. Do not fall back to "the first
     * uploaded file": that would let a request put an unvalidated file in an
     * arbitrary field name and bypass the MIME/size allow-list.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:3072'],
            'folder' => ['nullable', 'string', 'in:invest,partners,process,sections,services'],
        ]);

        // Strictly whitelisted target directories keep uploads from escaping
        // into arbitrary (or traversable) storage paths.
        $folder = $request->input('folder', 'sections');
        $path = $request->file('file')->store($folder, 'public');

        return response()->json([
            'path' => '/storage/'.$path,
            'url' => asset('storage/'.$path),
        ], 201);
    }
}
