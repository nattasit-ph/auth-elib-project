<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetPassword;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'accessible_at' => 'datetime',
        'expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'data_info' => 'array',
        'data_contact' => 'array',
        'data_setting' => 'array',
    ];

    /**
    * Send the password reset notification.
    *
    * @param  string  $token
    * @return void
    */
   public function sendPasswordResetNotification($token)
   {
       $this->notify(new ResetPassword($token));
   }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'user_org_id' => $this->user_org_id,
            'user_group_id' => $this->user_group_id,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scroptExpired($query, $date=NULL)
    {
        $date = $date ?? Carbon::now();
        return $query->where(function ($q) use ($date) {
            $q->whereNotNull('expires_at')->where('expires_at', '<=', $date);
        });
    }

    public function scopeNotExpired($query, $date=NULL)
    {
        $date = $date ?? Carbon::now();
        return  $query->where(function ($q) use ($date) {
            $q->whereNull('expires_at')->orWhere(function ($q) use ($date) {
                $q->whereNotNull('expires_at')->where('expires_at', '>', $date);
            });
        });
    }

    public function event_joins()
    {
        return $this->hasMany('App\Models\EventJoin');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\UserGroup', 'user_group_id', 'id');
    }

    public function shelfExpire()
    {
        return $this->hasMany('App\Models\Shelf')->with('product')->where('expiration_date', '<', date("Y-m-d"))->where('returned_date', null);
    }

    public function readyReserve()
    {
        return $this->hasMany('App\Models\ShelfReserve')->with('product', 'user.group', 'productMain')->where('status', 2);
    }

    public function org()
    {
        return $this->belongsTo('App\Models\UserOrg', 'user_org_id', 'id');
    }

    public function scopeMySelf($query)
    {
        if (Auth::check()) {
            return $query->where('id', Auth::user()->id);
        } else {
            return $query->where('id', null);
        }
    }

    public function scopeMyOrg($query)
    {
        return $query->where('user_org_id', Auth::user()->user_org_id);
    }

    public function scopeOfOrg($query, $user_org_id)
    {
        return $query->where('user_org_id', $user_org_id);
    }

    public function reserveRoom()
    {
        return $this->hasMany('App\Models\RoomBooking')->with('room', 'room_galleries')->where('start_datetime', '>=', date("Y-m-d H:i:s"));
    }

    public function lastLogin()
    {
        return $this->hasOne('App\Models\LoginHistory')->where('status', 'authen-success')->orderBy('created_at', 'desc');
    }

    public function user_groups_learnext()
    {
        return $this->belongsToMany('App\Models\UserGroup', 'ref_user_group_users_learnext', 'user_id', 'user_group_id')
            ->using('App\Models\RefUserGroupUser');
    }
}
