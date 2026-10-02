<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\Customer;
use App\Services\CustomerMagicLinkService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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
                Action::make('magic_link')
                    ->label('لینک ورود')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->visible(fn (Customer $record): bool => $record->active && $record->direct_link_enabled)
                    ->modalHeading('ساخت لینک ورود مشتری')
                    ->modalDescription('لینک جدید، لینک فعال قبلی این مشتری را لغو می‌کند. خود لینک فقط همین یک‌بار در اعلان نمایش داده می‌شود.')
                    ->modalSubmitActionLabel('ساخت لینک')
                    ->schema([
                        Select::make('expires_in_hours')
                            ->label('اعتبار لینک')
                            ->options([
                                24 => '۲۴ ساعت',
                                72 => '۷۲ ساعت',
                                168 => '۷ روز',
                            ])
                            ->default(72)
                            ->required(),
                    ])
                    ->action(function (array $data, Customer $record): void {
                        $issued = app(CustomerMagicLinkService::class)->issue(
                            $record,
                            (int) ($data['expires_in_hours'] ?? 72),
                            auth()->id(),
                        );

                        Notification::make()
                            ->title('لینک ورود ساخته شد')
                            ->body($issued['url'])
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                Action::make('revoke_magic_link')
                    ->label('لغو لینک ورود')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (Customer $record): bool => $record->direct_link_enabled)
                    ->requiresConfirmation()
                    ->modalDescription('همه لینک‌های ورودِ استفاده‌نشده این مشتری لغو می‌شوند.')
                    ->action(function (Customer $record): void {
                        $count = app(CustomerMagicLinkService::class)->revoke($record, auth()->id());

                        Notification::make()
                            ->title($count > 0 ? 'لینک ورود لغو شد' : 'لینک فعالی وجود نداشت')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }
}
