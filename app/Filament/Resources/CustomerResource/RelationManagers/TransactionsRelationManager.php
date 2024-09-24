<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionsRelationManager extends RelationManager
{
  protected static string $relationship = 'transactions';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        //
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        TextColumn::make('id')
          ->label('Order ID')
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
      ->filters([
        //
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
      ])
      ->actions([
        Action::make('View Order')
          ->url(fn(Transaction $record): string => TransactionResource::getUrl('view', ['record' => $record]))
          ->color('info')
          ->icon('heroicon-o-eye'),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
