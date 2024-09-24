<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class PackageResource extends Resource
{
  protected static ?string $model = Package::class;

  protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

  protected static ?string $recordTitleAttribute = 'name';

  protected static ?int $navigationSort = 4;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Choose category')->schema([
          Select::make('category_id')
            ->required()
            ->searchable()
            ->preload()
            ->relationship('category', 'name')

        ]),
        Section::make('Package information')->schema([
          Grid::make()
            ->schema([
              TextInput::make('name')
                ->required()
                ->maxLength(255),
              TextInput::make('price')
                ->numeric()
                ->required()
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->prefix('Rp'),
              TextInput::make('minimum_weight')
                ->required(),
              TextInput::make('duration')
                ->required()
            ]),
        ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->searchable(),
        Tables\Columns\TextColumn::make('category.name')
          ->searchable(),
        Tables\Columns\TextColumn::make('price')
          ->money('IDR')
          ->sortable(),
        Tables\Columns\TextColumn::make('minimum_weight')
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('duration')
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        SelectFilter::make('category')
          ->relationship('category', 'name')
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make()
        ]),
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
      'index' => Pages\ListPackages::route('/'),
      'create' => Pages\CreatePackage::route('/create'),
      'edit' => Pages\EditPackage::route('/{record}/edit'),
    ];
  }
}
