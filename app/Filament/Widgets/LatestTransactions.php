<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Activity'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BRL')
                    ->color(fn (string $state, Transaction $record): string => $record->type === 'income' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('date')
                    ->date(),
            ]);
    }
}
