<?php

namespace App\Filament\Resources\Customers\Tables;

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
            ->defaultSort('priority', 'desc')
            ->columns([
                TextColumn::make('company_name')->label('شرکت/مجموعه')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('whatsapp')->label('واتساپ')->searchable()->placeholder('—'),
                TextColumn::make('country_name')->label('کشور')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('user.email')->label('حساب متصل')->searchable()->toggleable()->placeholder('—'),
                TextColumn::make('priority')->label('اولویت')->sortable(),
                IconColumn::make('active')->label('فعال')->boolean(),
                IconColumn::make('direct_link_enabled')->label('لینک مستقیم')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('active')->label('وضعیت فعال'),
                TernaryFilter::make('direct_link_enabled')->label('لینک مستقیم'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
