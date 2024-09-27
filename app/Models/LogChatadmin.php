<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogChatadmin extends Model
{
	use HasFactory;
 	protected $guarded = [];

	 protected $casts = [
		'data' => 'array',
	];
	
    public function scopeIsRead($query)
    {
        return $query->where('is_read', 1);
    }
	
    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }
	
}
