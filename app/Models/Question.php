<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['id','contexte', 'indice'];

    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }

    /**
     * Utilisateurs qui ont résolu cette question
     */
    public function resolvedByUsers()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('resolved_at');
    }
}
