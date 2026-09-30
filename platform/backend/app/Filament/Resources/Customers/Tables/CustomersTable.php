<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام مشتری')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label('شرکت')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->label('واتساپ / موبایل')
                    ->searchable(),
                TextColumn::make('country_name')
                    ->label('کشور')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('اولویت')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('active')
                    ->label('فعال')
                    ->boolean(),
                IconColumn::make('direct_link_enabled')
                    ->label('لینک مستقیم')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('فعال بودن'),
                TernaryFilter::make('direct_link_enabled')
                    ->label('لینک مستقیم'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
