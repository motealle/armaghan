<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام مشتری')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255),
                TextInput::make('whatsapp')
                    ->label('واتساپ / موبایل')
                    ->tel()
                    ->maxLength(64),
                TextInput::make('company_name')
                    ->label('نام شرکت')
                    ->maxLength(255),
                TextInput::make('country_name')
                    ->label('کشور')
                    ->maxLength(255),
                TextInput::make('country_code')
                    ->label('کد کشور')
                    ->maxLength(2),
                TextInput::make('priority')
                    ->label('اولویت')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->default(0),
                Toggle::make('active')
                    ->label('فعال')
                    ->default(true),
                Toggle::make('direct_link_enabled')
                    ->label('اجازه لینک ورود مستقیم')
                    ->default(true),
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
