<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'course_categories';

    public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

    public function courses()
   {
 		return $this->belongsToMany('App\Models\Course', 'ref_course_categories', 'category_id', 'course_id')
             ->using('App\Models\RefCourseCategory');
   }

}
