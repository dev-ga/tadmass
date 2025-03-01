<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Restriccion para acceso al panel administrativo
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // return str_ends_with($this->email, '@piedyadmin.com') && $this->hasVerifiedEmail();
        // return $this->hasVerifiedEmail();

        if ($panel->getId() === 'admin') {
            return str_contains($this->email, 'admin@tadmass.com') && $this->hasVerifiedEmail();
        } else if ($panel->getId() === 'employee') {
            return str_contains($this->email, 'employee@tadmass.com') && $this->hasVerifiedEmail();
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
        // dd($this->name);
        // return $this->name;
    }

    
}