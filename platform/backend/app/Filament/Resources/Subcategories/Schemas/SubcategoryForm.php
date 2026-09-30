<?php

namespace App\Filament\Resources\Subcategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubcategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('دسته اصلی')
                    ->relationship('category', 'name_fa')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(16)
                    ->unique(ignoreRecord: true),
                TextInput::make('name_fa')
                    ->label('نام فارسی')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_ar')
                    ->label('نام عربی')
                    ->maxLength(255),
                TextInput::make('name_en')
                    ->label('نام انگلیسی')
                    ->maxLength(255),
                TextInput::make('name_ku')
                    ->label('نام کردی')
                    ->maxLength(255),
                Toggle::make('active')
                    ->label('فعال')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
