<?php

namespace App\Filament\Resources\Subcategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
            ->columns([
                TextColumn::make('category.name_fa')
                    ->label('دسته اصلی')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_fa')
                    ->label('نام')
                    ->searchable()
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
                SelectFilter::make('category_id')
                    ->label('دسته اصلی')
                    ->relationship('category', 'name_fa')
                    ->searchable()
                    ->preload(),
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
