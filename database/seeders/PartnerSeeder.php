<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['LUMORA', 'SPA · WELLNESS · BEAUTY', '♢', 'lumora', '/images/partners/lumora.png'],
            ['TAS', 'TECHNOLOGY & SOLUTIONS', '△', 'tas', '/images/partners/tas.png'],
            ['HEMA', 'HOSPITALITY GROUP', '▥', 'hema', '/images/partners/hema.png'],
            ['HILLSIDE', 'DEVELOPMENT GROUP', '⌃', 'hillside', '/images/partners/hillside.png'],
            ['IBRAHIM MOHSEN & SONS', 'IMS COMPANY', 'IMS', 'ibrahim', '/images/partners/ibrahim.png'],
        ];

        Partner::query()->delete();
        foreach ($partners as $i => [$name, $sub, $mark, $cls, $logo]) {
            Partner::create(compact('name', 'sub', 'mark', 'cls', 'logo') + ['sort' => $i]);
        }
    }
}
