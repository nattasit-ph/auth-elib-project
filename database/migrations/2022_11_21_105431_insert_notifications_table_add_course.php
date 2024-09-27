<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Notification;

class InsertNotificationsTableAddCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function($table)
        {
            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reservation-book-requested',
            ],
            [
                'system' => 'belib',
                'slug' => 'reservation-book-requested',
                'title' => 'Requested Reservation Book',
                'weight' => 10,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reservation-book-approved',
            ],
            [
                'system' => 'belib',
                'slug' => 'reservation-book-approved',
                'title' => 'Approved Reservation Book',
                'weight' => 20,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reservation-book-cancelled',
            ],
            [
                'system' => 'belib',
                'slug' => 'reservation-book-cancelled',
                'title' => 'Cancelled Reservation Book',
                'weight' => 30,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reminder-book-tomorrow',
            ],
            [
                'system' => 'belib',
                'slug' => 'reminder-book-tomorrow',
                'title' => 'Tomorrow Due Date Book',
                'weight' => 40,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reminder-book-today',
            ],
            [
                'system' => 'belib',
                'slug' => 'reminder-book-today',
                'title' => 'Today Due Date Book',
                'weight' => 50,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'reminder-book-overdue',
            ],
            [
                'system' => 'belib',
                'slug' => 'reminder-book-overdue',
                'title' => 'Overdue Book',
                'weight' => 60,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'suggestion-book',
            ],
            [
                'system' => 'belib',
                'slug' => 'suggestion-book',
                'title' => 'Suggestion Book',
                'weight' => 70,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'learnext',
                'slug' => 'course-approved',
            ],
            [
                'system' => 'learnext',
                'slug' => 'course-approved',
                'title' => 'Approved Course',
                'weight' => 80,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'learnext',
                'slug' => 'reminder-course-expired',
            ],
            [
                'system' => 'learnext',
                'slug' => 'reminder-course-expired',
                'title' => 'Expired Course',
                'weight' => 90,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'learnext',
                'slug' => 'course-event',
            ],
            [
                'system' => 'learnext',
                'slug' => 'course-event',
                'title' => 'Course Event',
                'weight' => 100,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'learnext',
                'slug' => 'course-invited-student',
            ],
            [
                'system' => 'learnext',
                'slug' => 'course-invited-student',
                'title' => 'Invited Course',
                'weight' => 110,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'learnext',
                'slug' => 'course-invited-instructor',
            ],
            [
                'system' => 'learnext',
                'slug' => 'course-invited-instructor',
                'title' => 'Invited to be an instructor',
                'weight' => 120,
                'status' => 1,
            ]);

            Notification::firstOrCreate(
            [
                'system' => 'belib',
                'slug' => 'suggestion-article',
            ],
            [
                'system' => 'belib',
                'slug' => 'suggestion-article',
                'title' => 'Suggestion Article',
                'weight' => 130,
                'status' => 1,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
