<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Notification::create([
            'system' => 'belib',
            'slug' => 'reservation-book-requested',
            'title' => 'Requested Reservation Book',
            'weight' => 10,
            'status' => 1,
        ]);

        Notification::create([
            'system' => 'belib',
            'slug' => 'reservation-book-approved',
            'title' => 'Approved Reservation Book',
            'weight' => 20,
            'status' => 1,
        ]);

        Notification::create([
            'system' => 'belib',
            'slug' => 'reservation-book-cancelled',
            'title' => 'Cancelled Reservation Book',
            'weight' => 30,
            'status' => 1,
        ]);

        Notification::create([
            'system' => 'belib',
            'slug' => 'reminder-book-tomorrow',
            'title' => 'Tomorrow Due Date Book',
            'weight' => 40,
            'status' => 1,
        ]);

        Notification::create([
            'system' => 'belib',
            'slug' => 'reminder-book-today',
            'title' => 'Today Due Date Book',
            'weight' => 50,
            'status' => 1,
        ]);

        Notification::create([
            'system' => 'belib',
            'slug' => 'reminder-book-overdue',
            'title' => 'Overdue Book',
            'weight' => 60,
            'status' => 1,
        ]);

    	Notification::create([
            'system' => 'belib',
            'slug' => 'suggestion-book',
            'title' => 'Suggestion Book',
            'weight' => 70,
            'status' => 1,
        ]);
    }
}
