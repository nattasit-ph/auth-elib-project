<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleFavorite extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'article_favorites';

    public function scopeMyOrg($query)
    {
        if (Auth::check())
            return $query->where('user_org_id', Auth::user()->user_org_id);
        else
            return $query->where('user_org_id', config('bookdose.default.user_org'));
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
