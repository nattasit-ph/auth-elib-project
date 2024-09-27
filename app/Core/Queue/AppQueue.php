<?php

namespace App\Core\Queue;

use Illuminate\Support\Str;

class AppQueue
{
    public const Default = 'low';
    public const High = 'high';

    public static function getQWithPrefix($q_name)
    {
        return config('bookdose.app.code'). '_' . config('app.env') . '_'.$q_name;
    }

    public static function getQueuesArray()
    {
        return [
            config('bookdose.app.code'). '_' . config('app.env') . '_' . AppQueue::Default,
            config('bookdose.app.code'). '_' . config('app.env') . '_' . AppQueue::High
        ];
    }
}
