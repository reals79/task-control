<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'company_id', 'department_id', 'phone', 'position', 'comment', 'last_visited_at', 'activated',
    ];
    protected $appends = [
        'department_name',
        'company_name',
        'role_titles',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activated' => 'boolean',
    ];

    protected $dates = [
        'last_visited_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = $value;
        }
    }

    public function getDepartmentNameAttribute()
    {
        return ($this->department) ? $this->department->name : '';
    }

    public function getCompanyNameAttribute()
    {
        return ($this->company) ? $this->company->name : '';
    }

    public function getRoleTitlesAttribute()
    {
        return implode(", ", $this->roles->pluck('title')->toArray());
    }
}
