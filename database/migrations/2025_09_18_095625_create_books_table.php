<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();              // ISBN unik
            $table->string('title');                      // Judul buku
            $table->string('author');                     // Penulis
            $table->string('publisher')->nullable();      // Penerbit
            $table->year('year')->nullable();             // Tahun terbit
            $table->integer('pages')->nullable();         // Jumlah halaman
            $table->enum('type', ['ebook', 'physical']);  // Jenis buku
            $table->foreignId('category_id')              // Relasi kategori
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->text('description')->nullable();      // Deskripsi
            $table->string('file_path')->nullable();      // Path file (ebook)
            $table->string('cover_path')->nullable();     // Path cover
            $table->integer('stock')->default(0);         // Stok buku
            $table->softDeletes();                        // Soft delete
            $table->timestamps();                         // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
