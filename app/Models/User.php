<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'is_active' => 'boolean',
        ];
    }

    // --- İLİŞKİLER (RELATIONS) ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Bir kullanıcının (eğer ustaysa) bir tane usta profili olur
    public function serviceProvider(): HasOne
    {
        return $this->hasOne(ServiceProvider::class);
    }

    public function provider(): HasOne
    {
        return $this->hasOne(ServiceProvider::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    // --- YARDIMCI METOTLAR (HELPER METHODS) ---

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1;
    }

    public function isCustomer(): bool
    {
        return (int) $this->role_id === 2;
    }

    public function isProvider(): bool
    {
        return (int) $this->role_id === 3;
    }
    public function homeRoute(): string
    {
        return match ((int) $this->role_id) {
            1 => 'admin.dashboard',       // Admin girerse buraya
            3 => 'provider.dashboard',    // Usta girerse buraya
            default => 'customer.dashboard', // Diğerleri (Müşteri) buraya
        };
    }
}