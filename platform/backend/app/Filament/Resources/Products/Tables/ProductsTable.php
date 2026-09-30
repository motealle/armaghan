<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductAvailability;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subcategory.name_fa')
                    ->label('زیر‌دسته')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_fa')
                    ->label('نام محصول')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('availability')
                    ->label('موجودی')
                    ->badge()
                    ->sortable(),
                IconColumn::make('active')
                    ->label('فعال')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subcategory_id')
                    ->label('زیر‌دسته')
                    ->relationship('subcategory', 'name_fa')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('availability')
                    ->label('وضعیت موجودی')
                    ->options(ProductAvailability::class),
                TernaryFilter::make('active')
                    ->label('فعال بودن'),
            ])
            ->defaultSort('sort_order')
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
