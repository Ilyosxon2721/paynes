<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\RateResource\Pages;
use App\Models\Rate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Курсы валют';

    protected static ?string $modelLabel = 'курс валют';

    protected static ?string $pluralModelLabel = 'курсы валют';

    protected static ?string $navigationGroup = 'Финансы';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Курсы обмена')
                    ->description('Установите курсы покупки и продажи валюты')
                    ->schema([
                        Forms\Components\Select::make('currency_from')
                            ->label('Валюта (откуда)')
                            ->options([
                                'USD' => 'USD (Доллар США)',
                                'EUR' => 'EUR (Евро)',
                                'RUB' => 'RUB (Российский рубль)',
                            ])
                            ->default('USD')
                            ->required(),
                        Forms\Components\Select::make('currency_to')
                            ->label('Валюта (куда)')
                            ->options([
                                'UZS' => 'UZS (Узбекский сум)',
                            ])
                            ->default('UZS')
                            ->required(),
                        Forms\Components\TextInput::make('buy_rate')
                            ->label('Курс покупки')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->suffix('UZS')
                            ->helperText('По какому курсу покупаем валюту у клиента'),
                        Forms\Components\TextInput::make('sell_rate')
                            ->label('Курс продажи')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->suffix('UZS')
                            ->helperText('По какому курсу продаём валюту клиенту'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активный курс')
                            ->default(true)
                            ->helperText('Только активные курсы используются в операциях'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_from')
                    ->label('Валюта')
                    ->formatStateUsing(fn (string $state): string => $state . ' → UZS')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('buy_rate')
                    ->label('Покупка')
                    ->money('UZS')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('sell_rate')
                    ->label('Продажа')
                    ->money('UZS')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('spread')
                    ->label('Спред')
                    ->getStateUsing(fn (Rate $record): string =>
                        number_format($record->sell_rate - $record->buy_rate, 2) . ' UZS'
                    )
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('currency_from')
                    ->label('Валюта')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'RUB' => 'RUB',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            'edit' => Pages\EditRate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['client_admin', 'manager', 'cashier']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['client_admin', 'manager']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasAnyRole(['client_admin', 'manager']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasAnyRole(['client_admin', 'manager']);
    }
}
