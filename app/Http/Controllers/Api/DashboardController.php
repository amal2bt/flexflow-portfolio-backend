<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'pending_messages' => Message::where('status', 'pending')->count(),
                'total_messages' => Message::count(),
                'pending_testimonials' => Testimonial::where('status', 'pending')->count(),
                'total_testimonials' => Testimonial::count(),
            ],
            'recent_messages' => Message::latest()->take(10)->get(),
        ]);
    }
}
