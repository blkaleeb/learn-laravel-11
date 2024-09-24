<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStats extends BaseWidget
{
  protected function getStats(): array
  {
    return [
      Stat::make('New Orders', Transaction::where('status', 'new')->count()),
      Stat::make('Processing Laundry', Transaction::where('status', 'processing')->count()),
      Stat::make('Ready to pick', Transaction::where('status', 'ready')->count()),
      Stat::make('Finished', Transaction::where('status', 'finished')->count())
    ];
  }
}
