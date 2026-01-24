<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Пользователи';

    protected static ?string $modelLabel = 'пользователь';

    protected static ?string $pluralModelLabel = 'пользователи';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('login')
                            ->label('Логин')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('full_name')
                            ->label('ФИО')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Безопасность')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable(),
                    ]),

                Forms\Components\Section::make('Настройки')
                    ->schema([
                        Forms\Components\Toggle::make('is_client_admin')
                            ->label('Администратор компании')
                            ->helperText('Может войти в личный кабинет и управлять филиалами/сотрудниками')
                            ->default(false),
                        Forms\Components\Select::make('position')
                            ->label('Должность')
                            ->options([
                                'manager' => 'Менеджер',
                                'cashier' => 'Кассир',
                            ])
                            ->default('manager')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активный',
                                'blocked' => 'Заблокирован',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('login')
                    ->label('Логин')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_client_admin')
                    ->label('Админ')
                    ->boolean(),
                Tables\Columns\BadgeColumn::make('position')
                    ->label('Должность')
                    ->colors([
                        'success' => 'manager',
                        'primary' => 'cashier',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manager' => 'Менеджер',
                        'cashier' => 'Кассир',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'blocked',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активный',
                        'blocked' => 'Заблокирован',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активный',
                        'blocked' => 'Заблокирован',
                    ]),
                Tables\Filters\TernaryFilter::make('is_client_admin')
                    ->label('Администратор'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить пользователя'),
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
}
