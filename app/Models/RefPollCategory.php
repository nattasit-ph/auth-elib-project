<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RefPollCategory extends Pivot
{
    protected $table = 'ref_poll_categories';
}
