<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
  protected static ?string $model = Transaction::class;

  protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Group::make()->schema([
          Section::make('Transaction Information')->schema([
            Select::make('customer_id')
              ->label('Customer')
              ->relationship('customer', 'name')
              ->searchable()
              ->preload()
              ->required()

          ])
        ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        //
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListTransactions::route('/'),
      'create' => Pages\CreateTransaction::route('/create'),
      'view' => Pages\ViewTransaction::route('/{record}'),
      'edit' => Pages\EditTransaction::route('/{record}/edit'),
    ];
  }
}
