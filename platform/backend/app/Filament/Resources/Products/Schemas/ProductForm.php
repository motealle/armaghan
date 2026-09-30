<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductAvailability;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subcategory_id')
                    ->relationship('subcategory', 'id')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name_fa')
                    ->required(),
                TextInput::make('name_ar'),
                TextInput::make('name_en'),
                TextInput::make('name_ku'),
                Select::make('availability')
                    ->options(ProductAvailability::class)
                    ->default('available')
                    ->required(),
                Toggle::make('active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
