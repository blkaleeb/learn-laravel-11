<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  use HasFactory;
  protected $fillable = [
    'customer_id',
    'status',
    'shipping_amount',
    'payment_method',
    'payment_status',
    'total',
    'notes'
  ];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function transactiondetails()
  {
    return $this->hasMany(TransactionDetail::class);
  }
}
