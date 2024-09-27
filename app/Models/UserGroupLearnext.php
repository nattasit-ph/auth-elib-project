<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupLearnext extends Model
{
 	use HasFactory;
 	protected $guarded = [];
 	protected $table = 'user_groups_learnext';
    protected $casts = [
        'data_policies' => 'array',
    ];

	public function scopeActive($query) 
	{
      return $query->where('status', 1);
   }

    public function categories()
    {
        return $this->belongsToMany('App\Models\CourseCategory', 'ref_category_user_groups', 'user_group_id', 'category_id')
        		->using('App\Models\RefCategoryUserGroup');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'ref_user_group_users_learnext', 'user_group_id', 'user_id')
        		->using('App\Models\RefUserGroupUser');
    }

}
