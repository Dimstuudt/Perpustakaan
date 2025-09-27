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
    Schema::create('racks', function (Blueprint $table) {
        $table->id();
        $table->string('name');        // nama rak, misal "Rak A"
        $table->string('code')->nullable(); // optional: kode/rfid/dsb
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('racks');
}

};
