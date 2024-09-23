<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'transaction_id',
    'package_id',
    'quantity',
    'unit_amount',
    'total_amount'
  ];

  public function transaction()
  {
    return $this->belongsto(Transaction::class);
  }
  public function package()
  {
    return $this->belongsto(Package::class);
  }
}
