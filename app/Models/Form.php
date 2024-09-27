<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $guarded = [];

   public function scopeActive($query)
	{
      return $query->where('status', 1);
   }

	public function fields()
	{
		return $this->hasMany('App\Models\FormField');
	}

	public function submissions()
	{
		return $this->hasMany('App\Models\FormSubmission');
	}

    public function systemBelib()
    {
        return $this->hasOne(FormSystem::class, 'form_id', 'id')->where('system', 'belib');
    }
    public function systemKm()
    {
        return $this->hasOne(FormSystem::class, 'form_id', 'id')->where('system', 'km');
    }
    public function systemLearnext()
    {
        return $this->hasOne(FormSystem::class, 'form_id', 'id')->where('system', 'learnext');
    }


}
