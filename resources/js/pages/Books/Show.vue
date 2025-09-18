<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  book: Object,
})
</script>

<template>
  <Head :title="book.judul" />

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-2">{{ book.judul }}</h1>
    <p><strong>ISBN:</strong> {{ book.isbn }}</p>
    <p><strong>Penulis:</strong> {{ book.penulis }}</p>
    <p><strong>Penerbit:</strong> {{ book.penerbit }}</p>
    <p><strong>Kategori:</strong> {{ book.kategori }}</p>
    <p><strong>Tahun Terbit:</strong> {{ book.tahun_terbit }}</p>
    <p><strong>Stok:</strong> {{ book.stok }}</p>

    <div v-if="book.file_path" class="mt-4">
      <a :href="`/storage/${book.file_path}`" target="_blank" class="text-blue-600">Download File</a>
    </div>

    <Link :href="route('books.index')" class="mt-4 inline-block text-gray-600">← Kembali</Link>
  </div>
</template>
