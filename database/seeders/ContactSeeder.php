<?php

namespace Database\Seeders;

use App\Models\ContactItem;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['pin', 'Address', 'Freetown, Sierra Leone'],
            ['phone', 'Call Us', '+232 33 623820'],
            ['mail', 'Email Us', 'hussien@flexflowsl.com'],
            ['whatsapp', 'Whatsapp', 'https://wa.me/23233623820'],
            ['facebook', 'Facebook', 'https://www.facebook.com/'],
            ['linkedin', 'Linkedin', 'https://www.linkedin.com/'],
            ['instagram', 'Instagram', 'https://www.instagram.com/'],
        ];

        ContactItem::query()->delete();
        foreach ($items as $i => [$icon, $label, $value]) {
            ContactItem::create(['icon' => $icon, 'label' => $label, 'value' => $value, 'sort' => $i]);
        }
    }
}
