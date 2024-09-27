<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
	use HasFactory;
	protected $guarded = [];
    
	public function scopeActive($query) 
	{
   		return $query->where('status', 1);
	}

	public function scopeMyOrg($query) 
	{
		if (Auth::check())
			return $query->where('user_org_id', Auth::user()->user_org_id);
		else
			return $query->where('user_org_id', env('DEFAULT_USER_ORG_ID', 1));
	}

	public function articles()
	{
		return $this->belongsToMany('App\Models\Article', 'ref_article_categories', 'article_category_id', 'article_id')
	  		->using('App\Models\RefArticleCategory');
	}

	public function children(){
		return $this->hasMany( 'App\Models\ArticleCategory', 'parent_id', 'id' );
	}
	
	public function parent(){
		return $this->hasOne( 'App\Models\ArticleCategory', 'id', 'parent_id' );
	}
	
	public function group(){
		return $this->hasOne( 'App\Models\ArticleGroup', 'id', 'group_id' );
	}

	public function scopeChild($query) 
	{
   		return $query->whereNotNull('parent_id')->whereNotNull('group_id');
	}

	public function scopeParent($query) 
	{
   		return $query->whereNull('parent_id');
	}
	
}
