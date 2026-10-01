<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(16)
                    ->unique(ignoreRecord: true),

                TextInput::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->integer()
                    ->default(0)
                    ->required(),

                TextInput::make('name_fa')
                    ->label('نام فارسی')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_en')
                    ->label('نام انگلیسی')
                    ->maxLength(255),

                TextInput::make('name_ar')
                    ->label('نام عربی')
                    ->maxLength(255),

                TextInput::make('name_ku')
                    ->label('نام کردی')
                    ->maxLength(255),

                Toggle::make('active')
                    ->label('فعال')
                    ->default(true)
                    ->required(),
            ]);
    }
}
