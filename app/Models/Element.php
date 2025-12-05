<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level_max',
    ];

    public function bases()
    {
        return $this->belongsToMany(Base::class)
            ->withPivot('level')
            ->withTimestamps();
    }
}
