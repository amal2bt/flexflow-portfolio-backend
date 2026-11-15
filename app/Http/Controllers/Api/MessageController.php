<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Validate the filter instead of passing arbitrary query input to the
        // database. (The query itself is already parameter-bound by Eloquent.)
        $request->validate([
            'status' => ['nullable', 'in:pending,read,archived'],
        ]);

        $query = Message::query()->latest();

        if (($status = $request->query('status')) !== null) {
            $query->where('status', $status);
        }

        return response()->json($query->get());
    }

    /**
     * Public contact form submission.
     *
     * This endpoint is unauthenticated, so it is the highest-risk input path
     * of the application. Values are validated, stripped of every HTML tag and
     * stored as plain text; the React frontend also escapes on render, giving
     * two independent layers against stored XSS.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
            // Honeypot: the real form never sends this field. Bots usually do.
            'website' => ['prohibited'],
        ]);

        $data = $this->sanitize($data);

        if ($data['name'] === '' || $data['subject'] === '') {
            return response()->json([
                'message' => 'The name and subject fields are required.',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'subject' => ['The subject field is required.'],
                ],
            ], 422);
        }

        // Status is always assigned server-side; a client can never publish or
        // mark its own submission as read.
        $data['status'] = 'pending';

        $message = Message::create($data);

        return response()->json($message, 201);
    }

    public function show(Message $message): JsonResponse
    {
        return response()->json($message);
    }

    public function update(Request $request, Message $message): JsonResponse
    {
        $data = $this->sanitize($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email:rfc', 'max:255'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
            'status' => ['sometimes', 'in:pending,read,archived'],
        ]));

        $message->update($data);

        return response()->json($message->fresh());
    }

    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Strip HTML tags from free-text fields and normalise whitespace.
     * Email/status are untouched (they are already strictly validated).
     */
    private function sanitize(array $data): array
    {
        foreach (['name', 'subject', 'body'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $value = strip_tags((string) $data[$field]);

                // strip_tags() can leave fragments of malformed/nested tags
                // (e.g. "<scr<script>ipt>"). Remove any residual angle brackets
                // and control characters so the stored value is plain text.
                $value = str_replace(['<', '>'], '', $value);
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;

                $data[$field] = trim($value);
            }
        }

        return $data;
    }
}
