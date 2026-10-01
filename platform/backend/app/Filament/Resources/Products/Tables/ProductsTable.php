<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductAvailability;
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
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name_fa')->label('نام داخلی')->searchable()->sortable(),
                TextColumn::make('subcategory.name_fa')->label('زیردسته')->searchable()->sortable(),
                TextColumn::make('subcategory.category.name_fa')->label('دسته‌بندی')->searchable(),
                TextColumn::make('availability')
                    ->label('وضعیت')
                    ->formatStateUsing(fn ($state): string => match ($state instanceof ProductAvailability ? $state : ProductAvailability::from($state)) {
                        ProductAvailability::Available => 'موجود',
                        ProductAvailability::Unavailable => 'ناموجود',
                        ProductAvailability::MadeToOrder => 'تولید‌پذیر',
                    }),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                IconColumn::make('active')->label('فعال')->boolean(),
            ])
            ->filters([
                SelectFilter::make('subcategory_id')
                    ->label('زیردسته')
                    ->relationship('subcategory', 'name_fa')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('availability')
                    ->label('وضعیت')
                    ->options([
                        ProductAvailability::Available->value => 'موجود',
                        ProductAvailability::Unavailable->value => 'ناموجود',
                        ProductAvailability::MadeToOrder->value => 'تولید‌پذیر',
                    ]),

                TernaryFilter::make('active')->label('وضعیت فعال'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
