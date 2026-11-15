<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\TopSection;
use App\Models\VisionSection;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        TopSection::updateOrCreate(['id' => 1], [
            'title' => 'Stop Managing Paper. START Managing Your Business.',
            'subtitle' => 'Modernize your operations with secure, scalable ERP and automation solutions that save time, reduce costs, and give you complete visibility across your organization.',
            'image' => null,
        ]);

        AboutSection::updateOrCreate(['id' => 1], [
            'heading' => 'Who Are We?',
            'description' => 'At FlexFlow, we are dedicated to helping businesses modernize the way they operate. We design intelligent digital solutions that replace paperwork, manual processes, and disconnected systems with a secure, connected platform tailored to the unique needs of every organization.',
            'catchphrase' => 'Smart Technology. Real Impact.',
            'paragraph_2' => 'We combine local insight with international expertise to develop practical ERP systems, financial management platforms, HR and biometric solutions, hospitality systems, and custom business software that solve real operational challenges.',
            'paragraph_3' => 'Our mission goes beyond developing software. We build the digital foundation that enables businesses to operate more efficiently, make better decisions, and grow with confidence in an increasingly connected world.',
            'image' => null,
        ]);

        $vision = VisionSection::updateOrCreate(['id' => 1], [
            'vision_text' => 'At FlexFlow, we envision a future where businesses no longer have to rely on paperwork, repetitive manual processes, disconnected systems, or expensive, inflexible software from overseas. We believe organizations deserve technology that simplifies the way they work by automating everyday tasks, eliminating inefficiencies, and providing complete visibility across their operations, all while being backed by a team that is always within reach and understands their business.',
            'vision_text_2' => 'Our mission is to deliver world-class digital solutions with the speed, reliability, and personal support that only a local technology partner can provide. From implementation and staff training to continuous improvements and system upgrades, we work alongside our clients every step of the way, ensuring their technology evolves as their business grows.',
            'vision_text_3' => 'Every solution we develop is designed to be secure, scalable, and fully customizable, giving businesses the freedom to adapt their systems to changing needs rather than changing their operations to fit rigid software.',
            'image' => null,
        ]);

        $points = [
            'Providing Technology That Understands Your Business.',
            'Delivering Dedicated Local Support, Every Day.',
            'Building Solutions That Evolve As Your Business Grows.',
        ];

        $vision->points()->delete();
        foreach ($points as $i => $point) {
            $vision->points()->create(['point' => $point, 'sort' => $i]);
        }
    }
}
