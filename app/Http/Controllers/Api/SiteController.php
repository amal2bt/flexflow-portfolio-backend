<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\Challenge;
use App\Models\ContactItem;
use App\Models\InvestFeature;
use App\Models\InvestSection;
use App\Models\InvestStat;
use App\Models\Partner;
use App\Models\ProcessSection;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\TopSection;
use App\Models\VisionSection;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    /**
     * Single aggregate payload consumed by the public marketing site so it can
     * hydrate every section with one request while keeping its local fallbacks.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'top' => TopSection::first(),
            'about' => AboutSection::first(),
            'vision' => VisionSection::with('points')->first(),
            'process' => [
                'section' => ProcessSection::first(),
                'steps' => ProcessStep::orderBy('sort')->get(),
            ],
            'challenges' => Challenge::orderBy('sort')->get(),
            'invest' => [
                'section' => InvestSection::first(),
                'stats' => InvestStat::orderBy('sort')->get(),
                'features' => InvestFeature::orderBy('sort')->get(),
            ],
            'services' => Service::with('cards')->orderBy('sort')->get(),
            'contact' => ContactItem::orderBy('sort')->get(),
            'partners' => Partner::orderBy('id')->get(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
