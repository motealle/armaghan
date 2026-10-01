<?php

namespace App\Filament\Resources\Subcategories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubcategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_fa')
                    ->label('نام فارسی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_fa')
                    ->label('دسته‌بندی مادر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('تعداد محصول')
                    ->counts('products')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('دسته‌بندی مادر')
                    ->relationship('category', 'name_fa')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('active')
                    ->label('وضعیت فعال'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
