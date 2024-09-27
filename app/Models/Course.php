<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'courses';

    public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

    public function categories()
    {
        return $this->belongsToMany('App\Models\CourseCategory', 'ref_course_categories', 'course_id', 'category_id')
        		->using('App\Models\RefCourseCategory');
    }

}
