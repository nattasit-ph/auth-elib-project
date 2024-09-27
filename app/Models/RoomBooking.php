<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
      return $query->where('status', 1);
    }
    
    public function room()
    {
      return $this->belongsTo('App\Models\Room');
    }

    public function room_galleries()
    {
      return $this->hasMany('App\Models\RoomGallery','room_id', 'room_id');
    }

    public function user()
    {
      return $this->belongsTo('App\Models\User');
    }

    public function scopeUpcoming($query)
    {
      return $query->where('start_datetime', '>=', date('Y-m-d H:i:s'));
    }

    public function scopeHistory($query)
    {
      return $query->where('start_datetime', '<', date('Y-m-d H:i:s'));
    }
}

