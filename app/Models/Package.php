<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'price', 'minimum_weight', 'duration', 'category'];

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
