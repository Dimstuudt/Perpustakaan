<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::create('cabinets', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // e.g., "C1"
        $table->string('name');           // cabinet name
        $table->text('description')->nullable();
        $table->timestamps();
    });

    // Add relation to racks
    Schema::table('racks', function (Blueprint $table) {
        $table->foreignId('cabinet_id')
              ->nullable()
              ->constrained('cabinets')
              ->cascadeOnDelete();
    });
}

public function down()
{
    Schema::table('racks', function (Blueprint $table) {
        $table->dropConstrainedForeignId('cabinet_id');
    });

    Schema::dropIfExists('cabinets');
}

};
