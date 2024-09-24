<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{

  protected int | string | array $columnSpan = 'full';

  protected static ?int $sort = 2;

  public function table(Table $table): Table
  {
    return $table
      ->query(
        TransactionResource::getEloquentQuery()
      )
      ->defaultPaginationPageOption(5)
      ->defaultSort('created_at', 'desc')
      ->columns([
        TextColumn::make('id')
          ->label('Order ID')
          ->searchable(),

        TextColumn::make('customer.name')
          ->searchable(),

        TextColumn::make('total')
          ->money('IDR'),

        TextColumn::make('status')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'new' => 'info',
            'processing' => 'warning',
            'ready' => 'warning',
            'finished' => 'success'
          })
          ->icon(fn(string $state): string => match ($state) {
            'new' => 'heroicon-m-sparkles',
            'processing' => 'heroicon-m-arrow-path',
            'ready' => 'heroicon-m-truck',
            'finished' => 'heroicon-m-check-badge',
          })
          ->sortable(),

        TextColumn::make('payment_method')
          ->sortable()
          ->searchable(),

        TextColumn::make('payment_status')
          ->sortable()
          ->badge()
          ->searchable(),

        TextColumn::make('created_at')
          ->label('Date')
          ->dateTime()
      ])
      ->actions(([
        Action::make('View Order')
          ->url(fn(Transaction $record): string => TransactionResource::getUrl('view', ['record' => $record]))
          ->icon('heroicon-m-eye')
      ]));
  }
}
