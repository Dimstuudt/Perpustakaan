<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'

const props = defineProps({
  book: Object,
  categories: Array, // daftar kategori untuk dropdown
})

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Edit Buku', href: route('books.edit', props.book.id) },
]

const form = useForm({
  isbn: props.book.isbn,
  title: props.book.title,
  author: props.book.author,
  publisher: props.book.publisher,
  year: props.book.year,
  pages: props.book.pages,
  category_id: props.book.category_id,
  file_path: null, // upload file baru
})

const submit = () => {
  form.post(route('books.update', props.book.id), { method: 'put' })
}
</script>

<template>
  <Head title="Edit Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-xl">
      <h1 class="text-xl font-bold mb-4">Edit Buku</h1>

      <form @submit.prevent="submit" class="space-y-3">
        <div>
          <label class="block mb-1">ISBN</label>
          <input v-model="form.isbn" type="text" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Judul</label>
          <input v-model="form.title" type="text" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Penulis</label>
          <input v-model="form.author" type="text" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Penerbit</label>
          <input v-model="form.publisher" type="text" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Tahun Terbit</label>
          <input v-model="form.year" type="number" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Jumlah Halaman</label>
          <input v-model="form.pages" type="number" class="border p-2 w-full" />
        </div>

        <div>
          <label class="block mb-1">Kategori</label>
          <select v-model="form.category_id" class="border p-2 w-full">
            <option value="">-- Pilih Kategori --</option>
            <option
              v-for="cat in props.categories"
              :key="cat.id"
              :value="cat.id"
            >
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block mb-1">File (PDF/EPUB)</label>
          <input type="file" @change="e => form.file_path = e.target.files[0]" />
        </div>

        <div class="flex items-center space-x-2 mt-4">
          <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">
            Update
          </button>
          <Link :href="route('books.index')" class="text-gray-600">Batal</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
