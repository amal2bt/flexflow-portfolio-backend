<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            ['Jesse', 'sales@example.com', 'Jesse Bake', '2026-10-04 05:07:41'],
            ['Carrol Airey', 'carrol@freebizdata.org', 'We shut down... to build something much bigger', '2026-10-03 02:10:58'],
            ['Ladsobeda', 'contact@example.com', 'I promised.', '2026-10-02 05:07:41'],
            ['Ladsobeda', 'contact@example.com', 'I promised.', '2026-10-01 02:10:58'],
            ['Robertpek', 'contact@example.com', 'Hello, write about your prices', '2026-09-30 05:07:41'],
            ['Anshika Sharma', 'contact@example.com', 'Improve Your Search Engine Visibility', '2026-09-29 02:10:58'],
            ['Robertpek', 'contact@example.com', 'Aloha, writing about the price for reseller', '2026-09-28 05:07:41'],
            ['Robertpek', 'contact@example.com', 'Hello, write about your price', '2026-09-27 02:10:58'],
            ['WilliamWew', 'contact@example.com', 'MAKE EVERY ENTRANCE IN A 2026 ROLLS-ROYCE', '2026-09-26 05:07:41'],
            ['Joanna Riggs', 'contact@example.com', 'Video Promotion for your website?', '2026-09-25 02:10:58'],
        ];

        Message::query()->delete();
        foreach ($messages as [$name, $email, $subject, $created]) {
            Message::create([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'body' => null,
                'status' => 'pending',
                'created_at' => $created,
                'updated_at' => $created,
            ]);
        }

        $testimonials = [
            ['Mahmoud K.', 'Operations Manager', 'FlexFlow replaced our paper processes and gave us complete visibility across every branch.', 5, 'approved'],
            ['Aisha B.', 'Hotel Director', 'Reservations and billing are finally in one place. Our front desk runs twice as fast.', 5, 'approved'],
            ['Samuel T.', 'Retail Owner', 'Real-time inventory across all our shops changed the way we do business.', 5, 'approved'],
            ['Fatima S.', 'HR Lead', 'Attendance and payroll are accurate to the minute. No more disputes.', 4, 'approved'],
            ['Ibrahim M.', 'Factory Manager', 'Production tracking from raw materials to finished goods, all in one dashboard.', 5, 'approved'],
        ];

        Testimonial::query()->delete();
        foreach ($testimonials as [$name, $role, $body, $rating, $status]) {
            Testimonial::create(compact('name', 'role', 'body', 'rating', 'status'));
        }
    }
}
