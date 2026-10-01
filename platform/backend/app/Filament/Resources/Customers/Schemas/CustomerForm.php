<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('company_name')->label('نام شرکت/مجموعه')->maxLength(255),
                TextInput::make('whatsapp')->label('واتساپ')->maxLength(64),
                TextInput::make('country_code')->label('کد کشور')->maxLength(2),
                TextInput::make('country_name')->label('نام کشور')->maxLength(255),
                TextInput::make('priority')
                    ->label('اولویت')
                    ->integer()
                    ->minValue(0)
                    ->maxValue(255)
                    ->default(0)
                    ->required(),
                Toggle::make('active')->label('فعال')->default(true)->required(),
                Toggle::make('direct_link_enabled')->label('لینک مستقیم فعال')->default(true)->required(),
                Textarea::make('notes')->label('یادداشت داخلی')->rows(5)->columnSpanFull(),
            ]);
    }
}
