<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::updating(function (User $user): void {
            // Filament's profile form is another write surface. An administrator
            // cannot claim a reserved owner identity by changing its mailbox.
            if ($user->isDirty('email') && ($user->getRawOriginal('role') === UserRole::Admin->value || $user->role === UserRole::Admin)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'Administrator email is immutable. Use the verified identity account.']);
            }
        });
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function isActiveAdmin(): bool
    {
        return $this->active && $this->role === UserRole::Admin;
    }

    public function isPrimaryOwner(): bool
    {
        return $this->isActiveAdmin() && $this->email_verified_at !== null
            && strtolower($this->email) === config('owner-access.primary_owner_email');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->isActiveAdmin();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }
}
