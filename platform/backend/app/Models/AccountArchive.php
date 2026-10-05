<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountArchive extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $hidden = ['snapshot', 'receipt_hash', 'bundle_hash', 'backup_hash'];

    protected function casts(): array
    {
        return ['snapshot' => 'encrypted:array', 'expires_at' => 'datetime',
            'deleted_at' => 'datetime', 'restored_at' => 'datetime'];
    }
}
