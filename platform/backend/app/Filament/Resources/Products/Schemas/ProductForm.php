<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductAvailability;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('code')
                    ->label('کد محصول')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true),

                Select::make('subcategory_id')
                    ->label('زیردسته')
                    ->relationship('subcategory', 'name_fa')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name_fa')
                    ->label('نام داخلی فارسی')
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_en')->label('نام داخلی انگلیسی')->maxLength(255),
                TextInput::make('name_ar')->label('نام داخلی عربی')->maxLength(255),
                TextInput::make('name_ku')->label('نام داخلی کردی')->maxLength(255),

                SpatieMediaLibraryFileUpload::make('product_gallery')
                    ->label('تصاویر محصول')
                    ->collection(\App\Models\Product::MEDIA_COLLECTION)
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(8192)
                    ->rules(['dimensions:max_width=5000,max_height=5000'])
                    ->multiple()
                    ->maxFiles(6)
                    ->reorderable()
                    ->conversion('thumb')
                    ->helperText('حداکثر ۶ تصویر؛ ترتیب اولین تصویر، تصویر اصلی محصول است.')
                    ->columnSpanFull(),

                Select::make('availability')
                    ->label('وضعیت تولید/موجودی')
                    ->options([
                        ProductAvailability::Available->value => 'موجود',
                        ProductAvailability::Unavailable->value => 'ناموجود',
                        ProductAvailability::MadeToOrder->value => 'تولید‌پذیر',
                    ])
                    ->default(ProductAvailability::Available->value)
                    ->required(),

                TextInput::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->integer()
                    ->default(0)
                    ->required(),

                Toggle::make('active')
                    ->label('فعال')
                    ->default(true)
                    ->required(),
            ]);
    }
}
