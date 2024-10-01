<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;

class TransactionResource extends Resource
{
  protected static ?string $model = Transaction::class;

  protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

  protected static ?int $navigationSort = 5;

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
              ->suffixAction(
                Action::make('newCustomer')
                  ->label('New Customer')
                  ->icon('heroicon-m-user-plus')
                  ->form([
                    Forms\Components\TextInput::make('name')
                      ->required(),
                    Forms\Components\TextInput::make('email')
                      ->label('Email Address')
                      ->maxlength(255)
                      ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                      ->tel()
                      ->unique(ignoreRecord: true)
                      ->required()
                  ])
                  ->action(function (array $data) {
                    Customer::create($data);
                    Notification::make()
                      ->title('Customer Created')
                      ->body('The new customer has been successfully added.')
                      ->success()
                      ->send();
                  })
              ),

            Select::make('payment_method')
              ->options([
                'cash' => 'Cash',
                'transfer' => 'Transfer',
                'qris' => 'QRIS',
              ])
              ->required(),

            Select::make('payment_status')
              ->options([
                'unpaid' => 'Unpaid',
                'paid' => 'Paid',
              ])
              ->required(),

            ToggleButtons::make('status')
              ->inline()
              ->default('new')
              ->required()
              ->options([
                'new' => 'New',
                'processing' => 'Processing',
                'ready' => 'Ready',
                'finished' => 'Finished',
              ])
              ->colors([
                'new' => 'info',
                'processing' => 'warning',
                'ready' => 'warning',
                'finished' => 'success',
              ])
              ->icons([
                'new' => 'heroicon-m-sparkles',
                'processing' => 'heroicon-m-arrow-path',
                'ready' => 'heroicon-m-truck',
                'finished' => 'heroicon-m-check-badge',
              ]),

            Textarea::make('notes')
              ->columnSpanFull()
          ])->columns(2),

          Section::make('Transction Items')
            ->schema([
              Repeater::make('transactiondetails')
                ->label('Cart')
                ->relationship()
                ->schema([
                  Select::make('package_id')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                      'sm' => 12,
                      'md' => 4
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set) => $set('quantity', Package::find($state)?->minimum_weight ?? 0))
                    ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Package::find($state)?->price ?? 0))
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', Package::find($state)?->price * $get('quantity') ?? 0)),
                  TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->step(0.5)
                    ->minValue(fn($get) => Package::find($get('package_id'))?->minimum_weight ?? 0)
                    ->default(0)
                    ->columnSpan([
                      'sm' => 12,
                      'md' => 2
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),
                  TextInput::make('unit_amount')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan([
                      'sm' => 12,
                      'md' => 3
                    ]),
                  TextInput::make('total_amount')
                    ->numeric()
                    ->required()
                    ->dehydrated()
                    ->columnSpan([
                      'sm' => 12,
                      'md' => 3
                    ]),
                ])->columns(12),

              TextInput::make('shipping_amount')
                ->numeric()
                ->default(0)
                ->required()
                ->columnSpan(1),

              Placeholder::make('grand_total_placeholder')
                ->label('Grand Total')
                ->content(function (Get $get, Set $set) {
                  $total = 0;
                  if (!$repeaters = $get('transactiondetails')) {
                    return $total;
                  }
                  foreach ($repeaters as $key => $repeater) {
                    $total += $get("transactiondetails.{$key}.total_amount");
                  }

                  $total += $get("shipping_amount");
                  $set('total', $total);
                  return Number::currency($total, 'IDR');
                }),

              Hidden::make('total')
                ->default(0)
            ])
        ])->columnSpanFull()
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('customer.name')
          ->sortable()
          ->searchable(),
        TextColumn::make('total')
          ->numeric()
          ->sortable()
          ->money('IDR'),
        TextColumn::make('payment_method')
          ->searchable()
          ->sortable(),
        TextColumn::make('payment_status')
          ->searchable()
          ->sortable(),

        SelectColumn::make('status')
          ->options([
            'new' => 'New',
            'processing' => 'Processing',
            'ready' => 'Ready',
            'finished' => 'Finished',
          ])
          ->searchable(),

        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true)
      ])
      ->filters([
        //
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ])
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }

  public static function getNavigationBadgeColor(): string|array|null
  {
    return static::getModel()::count() > 10 ? 'danger' : 'success';
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
