<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasAccountArchive
{
    protected static function bootHasAccountArchive(): void
    {
        static::addGlobalScope('account_archive', function (Builder $query): void {
            $model = $query->getModel();
            $column = $model instanceof \App\Models\User ? 'user_id' : 'customer_id';
            $query->whereNotExists(function ($q) use ($model, $column): void {
                $q->selectRaw('1')->from('account_archives')
                    ->whereColumn('account_archives.'.$column, $model->getTable().'.id')
                    ->whereNotNull('deleted_at')->whereNull('restored_at');
            });
        });
        // All ordinary editing surfaces must respect an archived account.
        static::updating(function (Model $model): void {
            abort_unless($model->newQuery()->whereKey($model->getKey())->exists(), 409, 'Account is deleted.');
        });
        static::deleting(function (): void {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'account' => 'Use the backup-first recoverable deletion flow.',
            ]);
        });
    }
}
