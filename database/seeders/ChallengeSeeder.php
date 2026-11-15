<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            ['Still Managing Your Business on Paper?', 'Paper records, notebooks, and spreadsheets slow your business, create costly mistakes, and make finding important information difficult.'],
            ['Managing Multiple Branches?', 'Keeping track of several stores, offices, hotels, or business locations becomes difficult without one centralized platform connecting everything in real time.'],
            ['Hotel Reservations Becoming Difficult to Manage?', 'Double bookings, manual reservations, guest records, room availability, and billing become increasingly difficult to manage as your hotel grows.'],
            ['Struggling to Approve Requests Efficiently?', 'Purchase requests, expenses, and approvals are delayed because they rely on paperwork, phone calls, or WhatsApp messages.'],
            ['Production Difficult to Track?', 'Monitor your entire manufacturing process; from raw materials and production to finished goods, wastage, and delivery, all from one integrated ERP system.'],
            ['Retail Sales Difficult to Monitor?', 'Gain real-time visibility into sales, inventory, cash flow, and performance across every branch or franchise from a single dashboard.'],
            ['Restaurant Orders Getting Lost or Delayed?', 'Poor communication between waiters, the kitchen, cashier, and management leads to delays, incorrect orders, and unhappy customers.'],
            ['Too Much Time Spent on Repetitive Manual Work?', 'Repetitive administrative tasks consume valuable hours that could be automated, allowing your team to focus on growing the business.'],
            ['Losing Control of Inventory?', 'Stock shortages, overstocking, and inaccurate inventory records impact profitability, customer satisfaction, and operational efficiency.'],
            ['No Clear Business Reports?', 'Without real-time dashboards and reports, business decisions rely on guesswork instead of accurate, up-to-date information.'],
            ['Business Data at Risk?', 'Paper files, outdated software, and local servers leave your business vulnerable to data loss, downtime, and unauthorized access.'],
            ['No Visibility Into Staff Attendance?', 'Without automated attendance tracking, it is difficult to monitor punctuality, overtime, working hours, absenteeism, and payroll accurately.'],
        ];

        Challenge::query()->delete();
        foreach ($challenges as $i => [$title, $description]) {
            Challenge::create(['title' => $title, 'description' => $description, 'sort' => $i]);
        }
    }
}
