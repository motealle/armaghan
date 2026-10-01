<?php

namespace App\Filament\Resources\Subcategories;

use App\Filament\Resources\Subcategories\Pages\CreateSubcategory;
use App\Filament\Resources\Subcategories\Pages\EditSubcategory;
use App\Filament\Resources\Subcategories\Pages\ListSubcategories;
use App\Filament\Resources\Subcategories\Schemas\SubcategoryForm;
use App\Filament\Resources\Subcategories\Tables\SubcategoriesTable;
use App\Models\Subcategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubcategoryResource extends Resource
{
    protected static ?string $model = Subcategory::class;

    protected static ?string $recordTitleAttribute = 'name_fa';

    protected static ?string $modelLabel = 'زیردسته';

    protected static ?string $pluralModelLabel = 'زیردسته‌ها';

    protected static ?string $navigationLabel = 'زیردسته‌ها';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return SubcategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubcategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubcategories::route('/'),
            'create' => CreateSubcategory::route('/create'),
            'edit' => EditSubcategory::route('/{record}/edit'),
        ];
    }
}
