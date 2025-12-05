<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected $fillable = [
        'id',
        'name',
        'created_by',
        'updated_by'
    ];

    public function user(){
        return $this->hasOne(User::class, 'base_id');
    }

    public function elements()
    {
        return $this->belongsToMany(Element::class)
            ->withPivot('level')
            ->withTimestamps();
    }

}
