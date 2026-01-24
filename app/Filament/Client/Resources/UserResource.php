<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Сотрудники';

    protected static ?string $modelLabel = 'сотрудник';

    protected static ?string $pluralModelLabel = 'сотрудники';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $clientId = auth()->user()->client_id;

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

                Forms\Components\Section::make('Должность и филиал')
                    ->schema([
                        Forms\Components\Select::make('position')
                            ->label('Должность')
                            ->options([
                                'manager' => 'Менеджер',
                                'cashier' => 'Кассир',
                            ])
                            ->default('cashier')
                            ->required()
                            ->helperText('Менеджер управляет филиалами, кассир работает с клиентами'),
                        Forms\Components\Select::make('branch_id')
                            ->label('Филиал')
                            ->relationship(
                                'branchRelation',
                                'name',
                                fn (Builder $query) => $query->where('client_id', $clientId)
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активный',
                                'blocked' => 'Заблокирован',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\TextInput::make('reward_percentage')
                            ->label('Процент вознаграждения')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
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
                Tables\Columns\TextColumn::make('login')
                    ->label('Логин')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branchRelation.name')
                    ->label('Филиал')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('position')
                    ->label('Должность')
                    ->colors([
                        'primary' => 'cashier',
                        'success' => 'manager',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cashier' => 'Кассир',
                        'manager' => 'Менеджер',
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
                Tables\Columns\TextColumn::make('reward_percentage')
                    ->label('% вознаграждения')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y')
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
                Tables\Filters\SelectFilter::make('position')
                    ->label('Должность')
                    ->options([
                        'cashier' => 'Кассир',
                        'manager' => 'Менеджер',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Филиал')
                    ->relationship('branchRelation', 'name', fn (Builder $query) => $query->where('client_id', auth()->user()->client_id)),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery()
            ->where('client_id', $user->client_id)
            ->where('is_client_admin', false); // Не показываем админов клиента

        // Менеджер видит только кассиров своих филиалов
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            $query->where('position', 'cashier')
                ->whereIn('branch_id', $branchIds);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        // Только client_admin и manager могут создавать пользователей
        if (!$user->hasAnyRole(['client_admin', 'manager'])) {
            return false;
        }

        // Проверяем лимиты подписки
        if ($user->hasRole('client_admin')) {
            if (!$user->client || !$user->client->canAddUser()) {
                return false;
            }
        }

        return true;
    }

    public static function canViewAny(): bool
    {
        // client_admin и manager могут просматривать пользователей
        return auth()->user()->hasAnyRole(['client_admin', 'manager']);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        // client_admin может редактировать всех
        if ($user->hasRole('client_admin')) {
            return true;
        }

        // Менеджер может редактировать только кассиров своих филиалов
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            return $record->position === 'cashier' && in_array($record->branch_id, $branchIds);
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        // Используем те же правила что и для редактирования
        return static::canEdit($record);
    }
}
