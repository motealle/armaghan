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
                    ->relationship('category', 'id')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name_fa')
                    ->required(),
                TextInput::make('name_ar'),
                TextInput::make('name_en'),
                TextInput::make('name_ku'),
                Toggle::make('active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
