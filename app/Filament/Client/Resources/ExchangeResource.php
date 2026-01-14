<?php
namespace App\Filament\Client\Resources;
use App\Filament\Client\Resources\ExchangeResource\Pages;
use App\Models\Exchange;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExchangeResource extends Resource {
    protected static ?string $model = Exchange::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Обмен валют';
    protected static ?string $pluralModelLabel = 'обмены валют';
    protected static ?int $navigationSort = 21;

    public static function table(Table $table): Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\BadgeColumn::make('type')->label('Тип')
                ->colors(['success' => 'buy', 'warning' => 'sell'])
                ->formatStateUsing(fn ($state) => $state === 'buy' ? 'Покупка' : 'Продажа'),
            Tables\Columns\TextColumn::make('uzs_amount')->label('UZS')->money('UZS', divideBy: 1),
            Tables\Columns\TextColumn::make('usd_amount')->label('USD')->money('USD', divideBy: 1),
            Tables\Columns\TextColumn::make('rate')->label('Курс')->numeric(2),
            Tables\Columns\TextColumn::make('cashier.full_name')->label('Кассир')->toggleable(),
            Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array {
        return ['index' => Pages\ListExchanges::route('/')];
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
