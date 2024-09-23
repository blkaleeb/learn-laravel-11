<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('packages', function (Blueprint $table) {
      $table->unsignedBigInteger('category_id')->nullable()->after('name'); // Add the foreign key column
      $table->foreign('category_id')->references('id')->on('categories'); // Create the foreign key constraint
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('packages', function (Blueprint $table) {
      $table->dropColumn('category_id');
    });
  }
};
