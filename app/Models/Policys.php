<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policys extends Model
{
    use HasFactory;
    protected $table = 'policy_and_terms';
    protected $fillable = ['type'];

    public function scopePrivacy($query)
    {
        return $query->where('type', 1);
    }
    public function scopeTerms($query)
    {
        return $query->where('type', 2);
    }
    public function scopeCookie($query)
    {
        return $query->where('type', 3);
    }
}
