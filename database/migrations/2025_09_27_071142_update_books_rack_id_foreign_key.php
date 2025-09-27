<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['rack_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            // Tambahkan foreign key baru dengan nullOnDelete
            $table->foreign('rack_id')
                ->references('id')
                ->on('racks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Rollback ke cascadeOnDelete
            $table->dropForeign(['rack_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->foreign('rack_id')
                ->references('id')
                ->on('racks')
                ->cascadeOnDelete();
        });
    }
};
