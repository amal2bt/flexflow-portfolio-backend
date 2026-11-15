<?php

namespace Database\Seeders;

use App\Models\ProcessSection;
use App\Models\ProcessStep;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        ProcessSection::updateOrCreate(['id' => 1], [
            'title' => 'Our Proven Development Process',
            'text1' => 'Every FlexFlow project follows a structured, collaborative process built on transparency, innovation, and precision.',
            'text2' => 'From early planning to long-term maintenance, we combine design thinking, AI-driven insights, and agile development to ensure every solution we build is efficient, scalable, and sustainable.',
            'image' => null,
        ]);

        $steps = [
            ['Plan', 'We define business goals, objectives, and timelines, aligning technical requirements with your operational needs for a clear roadmap.'],
            ['Design', 'Our creative team transforms ideas into intuitive, user-focused designs that merge innovation, usability, and aesthetic appeal.'],
            ['Implement', 'We bring your solution to life with seamless coding, smart integrations, and agile iterations that ensure adaptability and efficiency.'],
            ['Test', 'Rigorous quality assurance ensures performance, reliability, and security across every platform before deployment.'],
            ['Deploy', 'We handle full rollout and installation, managing cloud setup, hardware connections, and seamless live transitions.'],
            ['Upkeep', 'Our commitment continues beyond delivery with continuous support, updates, and performance monitoring to sustain long-term impact.'],
        ];

        ProcessStep::query()->delete();
        foreach ($steps as $i => [$title, $description]) {
            ProcessStep::create(['title' => $title, 'description' => $description, 'sort' => $i]);
        }
    }
}
