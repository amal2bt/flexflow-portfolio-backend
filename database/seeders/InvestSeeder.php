<?php

namespace Database\Seeders;

use App\Models\InvestFeature;
use App\Models\InvestSection;
use App\Models\InvestStat;
use Illuminate\Database\Seeder;

class InvestSeeder extends Seeder
{
    public function run(): void
    {
        InvestSection::updateOrCreate(['id' => 1], [
            'title' => 'This Is Where FlexFlow Comes In',
            'subtitle' => 'We replace complexity with intelligent digital solutions that simplify operations, improve visibility, and help your business grow with confidence.',
            'image' => null,
        ]);

        $stats = [
            ['Faster Processes', 100],
            ['Manual Work', -80],
            ['Data Accuracy', 98],
            ['Scalability Ready', 100],
        ];

        InvestStat::query()->delete();
        foreach ($stats as $i => [$label, $value]) {
            InvestStat::create(['label' => $label, 'value' => $value, 'sort' => $i]);
        }

        $features = [
            ['chart', 'Reduced Operational Costs', 'Automation eliminates paper waste, reduces labor hours, and minimizes costly errors across your daily operations.'],
            ['top', 'High-ROI Efficiency Gains', 'Businesses see instant productivity improvements through faster workflows, organized data, and automated reporting.'],
            ['cloud', 'Scalable Cloud Solutions', 'Lightweight, cloud-based tools allow rapid expansion without infrastructure costs, designed to grow with your business.'],
            ['shield', 'Improved Accuracy & Security', 'Real-time tracking, secure digital records, and automated checks reduce fraud, errors, and data loss risks.'],
            ['gear', 'Sustainable, Eco-Friendly Impact', 'Smart systems support a green business model by replacing paper-heavy workflows with digital efficiency.'],
        ];

        InvestFeature::query()->delete();
        foreach ($features as $i => [$icon, $title, $description]) {
            InvestFeature::create(['icon' => $icon, 'title' => $title, 'description' => $description, 'sort' => $i]);
        }
    }
}
