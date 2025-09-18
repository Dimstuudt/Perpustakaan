<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  book: {
    id: number
    isbn: string
    title: string
    author: string
    publisher: string
    year: number | null
    pages: number | null
    description: string | null
    category_id: number | null
  }
  categories: { id: number; name: string }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Edit Buku', href: route('books.edit', props.book.id) },
]

const form = useForm({
  isbn: props.book.isbn || '',
  title: props.book.title || '',
  author: props.book.author || '',
  publisher: props.book.publisher || '',
  year: props.book.year || '',
  pages: props.book.pages || '',
  description: props.book.description || '',
  category_id: props.book.category_id || '',
  file: null as File | null,
})

const submit = () => {
  form.put(route('books.update', props.book.id))
}
</script>

<template>
  <Head title="Edit Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Edit Buku</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">ISBN</label>
          <input v-model="form.isbn" type="text" class="w-full border rounded p-2" />
          <span v-if="form.errors.isbn" class="text-red-500 text-sm">{{ form.errors.isbn }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium">Judul</label>
          <input v-model="form.title" type="text" class="w-full border rounded p-2" />
          <span v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium">Penulis</label>
          <input v-model="form.author" type="text" class="w-full border rounded p-2" />
        </div>

        <div>
          <label class="block text-sm font-medium">Penerbit</label>
          <input v-model="form.publisher" type="text" class="w-full border rounded p-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Tahun</label>
            <input v-model="form.year" type="number" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Jumlah Halaman</label>
            <input v-model="form.pages" type="number" class="w-full border rounded p-2" />
          </div>
        </div>

        <!-- Dropdown kategori -->
        <div>
          <label class="block text-sm font-medium">Kategori</label>
          <select v-model="form.category_id" class="w-full border rounded p-2">
            <option value="">-- Pilih Kategori --</option>
            <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <span v-if="form.errors.category_id" class="text-red-500 text-sm">{{ form.errors.category_id }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium">Deskripsi</label>
          <textarea v-model="form.description" class="w-full border rounded p-2"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium">Ganti File Buku (PDF/EPUB)</label>
          <input type="file" @change="form.file = $event.target.files[0]" class="w-full border rounded p-2" />
          <span v-if="form.errors.file" class="text-red-500 text-sm">{{ form.errors.file }}</span>
        </div>

        <div class="flex justify-end space-x-2">
          <Link href="/books" class="px-4 py-2 bg-gray-300 rounded">Batal</Link>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded" :disabled="form.processing">
            Update
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
