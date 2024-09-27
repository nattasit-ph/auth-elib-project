<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ReferenceLinks extends Model
{
   use HasFactory;
 	protected $guarded = [];
 	
	protected $casts = [
		'group' => 'array',
      'category' => 'array',
      'description' => 'array',
	];
}

