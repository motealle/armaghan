<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'password_configured_at'])]
class AdminAuthState extends Model
{
    protected function casts(): array
    {
        return ['password_configured_at' => 'datetime'];
    }
}
