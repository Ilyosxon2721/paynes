<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\CommissionResource\Pages;
use App\Models\Commission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Комиссии';

    protected static ?string $pluralModelLabel = 'комиссии';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Настройки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Название')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->label('Тип комиссии')
                        ->options([
                            'fixed' => 'Фиксированная',
                            'percentage' => 'Процент',
                            'combined' => 'Комбинированная',
                        ])
                        ->required()
                        ->reactive(),

                    Forms\Components\TextInput::make('fixed_amount')
                        ->label('Фиксированная сумма')
                        ->numeric()
                        ->visible(fn ($get) => in_array($get('type'), ['fixed', 'combined']))
                        ->suffix('UZS'),

                    Forms\Components\TextInput::make('percentage')
                        ->label('Процент')
                        ->numeric()
                        ->visible(fn ($get) => in_array($get('type'), ['percentage', 'combined']))
                        ->suffix('%')
                        ->step(0.01),

                    Forms\Components\TextInput::make('min_amount')
                        ->label('Минимум')
                        ->numeric()
                        ->suffix('UZS'),

                    Forms\Components\TextInput::make('max_amount')
                        ->label('Максимум')
                        ->numeric()
                        ->suffix('UZS'),

                    Forms\Components\Select::make('status')
                        ->label('Статус')
                        ->options([
                            'active' => 'Активная',
                            'inactive' => 'Неактивная',
                        ])
                        ->default('active')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('type_label')->label('Тип'),
                Tables\Columns\TextColumn::make('fixed_amount')->label('Фиксированная')->suffix(' UZS'),
                Tables\Columns\TextColumn::make('percentage')->label('Процент')->suffix('%'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors(['success' => 'active', 'danger' => 'inactive'])
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'Активная' : 'Неактивная'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissions::route('/'),
            'create' => Pages\CreateCommission::route('/create'),
            'edit' => Pages\EditCommission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth()->user()->client_id);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('client_admin');
    }
}
