<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Widgets\TransactionStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
  protected static string $resource = TransactionResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }

  protected function getHeaderWidgets(): array
  {
    return [
      TransactionStats::class
    ];
  }

  public function getTabs(): array
  {
    return [
      null => Tab::make('All'),
      'new' => Tab::make()->query(fn($query) => $query->where('status', 'new')),
      'processing' => Tab::make()->query(fn($query) => $query->where('status', 'processing')),
      'ready' => Tab::make()->query(fn($query) => $query->where('status', 'ready')),
      'finished' => Tab::make()->query(fn($query) => $query->where('status', 'finished')),
    ];
  }
}
