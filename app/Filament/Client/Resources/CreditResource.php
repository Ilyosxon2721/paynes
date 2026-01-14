<?php
namespace App\Filament\Client\Resources;
use App\Filament\Client\Resources\CreditResource\Pages;
use App\Models\Credit;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CreditResource extends Resource {
    protected static ?string $model = Credit::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Кредиты';
    protected static ?string $pluralModelLabel = 'кредиты';
    protected static ?int $navigationSort = 22;

    public static function table(Table $table): Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('client_name')->label('Клиент')->searchable(),
            Tables\Columns\TextColumn::make('debit')->label('Дебет')->money('UZS', divideBy: 1),
            Tables\Columns\TextColumn::make('credit')->label('Кредит')->money('UZS', divideBy: 1),
            Tables\Columns\BadgeColumn::make('status')->label('Статус')
                ->colors(['warning' => 'pending', 'success' => 'confirmed', 'danger' => 'cancelled'])
                ->formatStateUsing(fn ($state) => match($state) {
                    'pending' => 'Ожидает', 'confirmed' => 'Подтвержден', 'cancelled' => 'Отменен', default => $state
                }),
            Tables\Columns\TextColumn::make('cashier.full_name')->label('Кассир')->toggleable(),
            Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array {
        return ['index' => Pages\ListCredits::route('/')];
    }

    public static function getEloquentQuery(): Builder {
        $user = auth()->user();
        $query = parent::getEloquentQuery();
        if ($user->hasRole('cashier')) $query->where('cashier_id', $user->id);
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            $cashierIds = \App\Models\User::whereIn('branch_id', $branchIds)->where('position', 'cashier')->pluck('id')->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }
        if ($user->hasRole('client_admin')) {
            $cashierIds = $user->client->users()->where('position', 'cashier')->pluck('id')->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }
        return $query;
    }

    public static function canCreate(): bool { return false; }
    public static function canViewAny(): bool { return auth()->user()->hasAnyRole(['client_admin', 'manager', 'cashier']); }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return auth()->user()->hasRole('client_admin'); }
}
