<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{
  book: {
    id: number
    isbn: string
    title: string
    author: string | null
    publisher: string | null
    year: number | null
    pages: number | null
    description: string | null
    category_id: number | null
    cover_url?: string | null
    file_url?: string | null
  }
  categories: { id: number; name: string }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Preview Buku', href: route('books.preview', props.book.id) },
]
</script>

<template>
  <Head title="Preview Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-3xl mx-auto text-gray-900 dark:text-gray-200">
      <h1 class="text-2xl font-bold mb-6">Preview Buku</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cover buku -->
        <div class="col-span-1 flex flex-col items-center">
          <img
            :src="props.book.cover_url
              ? `/storage/${props.book.cover_url}`
              : '/images/book-dummy.png'"
            alt="Cover Buku"
            class="w-48 h-64 object-cover rounded shadow dark:shadow-gray-800"
          />
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Ukuran: 200 x 300 px
          </p>
        </div>

        <!-- Detail buku -->
        <div
          class="col-span-2 space-y-3 border rounded-lg p-4 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm"
        >
          <p><strong>ISBN:</strong> {{ props.book.isbn }}</p>
          <p><strong>Judul:</strong> {{ props.book.title }}</p>
          <p><strong>Penulis:</strong> {{ props.book.author || '-' }}</p>
          <p><strong>Penerbit:</strong> {{ props.book.publisher || '-' }}</p>
          <p><strong>Tahun:</strong> {{ props.book.year || '-' }}</p>
          <p><strong>Jumlah Halaman:</strong> {{ props.book.pages || '-' }}</p>
          <p>
            <strong>Kategori:</strong>
            {{ props.categories.find(c => c.id === props.book.category_id)?.name || '-' }}
          </p>
          <p>
            <strong>Deskripsi:</strong>
            {{ props.book.description || '-' }}
          </p>
        </div>
      </div>

      <!-- File preview -->
      <div
        class="mt-6 border rounded-lg p-4 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm"
      >
        <h2 class="text-lg font-semibold mb-3">Preview File Buku</h2>

        <div v-if="props.book.file_url">
          <iframe
            :src="props.book.file_url ? `/storage/${props.book.file_url}` : ''"
            class="w-full h-96 border rounded dark:border-gray-700"
          ></iframe>
        </div>

        <div class="text-gray-500 dark:text-gray-400 text-sm italic" v-else>
          Tidak ada file buku yang diunggah.
        </div>
      </div>

      <!-- Tombol navigasi -->
      <div class="mt-6 flex space-x-2">
        <Link
          :href="route('books.edit', props.book.id)"
          class="px-4 py-2 bg-gray-300 dark:bg-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600 transition"
        >
          Edit
        </Link>

        <Link
          href="/books"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Kembali ke Daftar Buku
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
