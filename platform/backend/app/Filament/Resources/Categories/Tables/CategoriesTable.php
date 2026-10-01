<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
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

                TextColumn::make('name_en')
                    ->label('نام انگلیسی')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('subcategories_count')
                    ->label('تعداد زیردسته')
                    ->counts('subcategories')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('وضعیت فعال'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
