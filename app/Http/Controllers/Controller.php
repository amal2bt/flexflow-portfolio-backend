<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    /**
     * Store an uploaded file (if present) and return the public URL/path.
     * Preserves the previous file when nothing new is uploaded, and removes
     * the old file from disk when it is replaced.
     */
    protected function storeUpload(Request $request, string $field, ?string $current = null, string $folder = 'uploads'): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        $this->deleteUpload($current);

        $file = $request->file($field);
        $path = $file->store($folder, 'public');

        return '/storage/'.$path;
    }

    /**
     * Resolve an image/media field on a multipart save.
     *  - a new file replaces the current file (and deletes the old one),
     *  - `remove_image=1` deletes the stored file and returns null,
     *  - otherwise the current value is kept.
     */
    protected function resolveUpload(Request $request, string $field, ?string $current = null, string $folder = 'uploads'): ?string
    {
        if ($request->hasFile($field)) {
            return $this->storeUpload($request, $field, $current, $folder);
        }

        if ($request->boolean('remove_image')) {
            $this->deleteUpload($current);

            return null;
        }

        return $current;
    }

    /**
     * Delete a previously stored public file when it lives under /storage.
     */
    protected function deleteUpload(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $relative = ltrim(str_replace('/storage/', '', $url), '/');

        // Defence in depth: never resolve a path outside the public disk, even
        // if a malicious/legacy value ends up in the database.
        if ($relative === '' || str_contains($relative, '..')) {
            return;
        }

        if (Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
