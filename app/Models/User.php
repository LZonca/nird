<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'base_id',
        'year',
        'position',
        'funds',
        'last_upgrade_year'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id', 'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function base()
    {
        return $this->belongsTo(Base::class);
    }

    /**
     * Questions résolues par l'utilisateur
     */
    public function resolvedQuestions()
    {
        return $this->belongsToMany(Question::class)
            ->withTimestamps()
            ->withPivot('resolved_at');
    }

    /**
     * Vérifier si une question a été résolue
     */
    public function hasResolvedQuestion($questionId): bool
    {
        return $this->resolvedQuestions()->where('question_id', $questionId)->exists();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
