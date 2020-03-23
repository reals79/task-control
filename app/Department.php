<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Department extends Model
{
    //
    use NodeTrait;

    public $timestamps = false;

    protected $fillable = [
        'name', 'company_id', 'parent_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
