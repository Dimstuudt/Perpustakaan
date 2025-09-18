<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

// Props dari controller
const props = defineProps<{
  categories: { id: number; name: string }[]
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Tambah Buku', href: '/books/create' },
]

// Form state
const form = useForm({
  isbn: '',
  title: '',
  author: '',
  publisher: '',
  year: '',
  pages: '',
  description: '',
  category_id: '',
  file: null as File | null,
  cover: null as File | null,
})

// Cover preview reactive
const defaultCover = '/images/dummy-cover.png' // siapkan di public/images
const coverPreview = ref<string>(defaultCover)

watch(
  () => form.cover,
  (newCover) => {
    if (newCover) {
      coverPreview.value = URL.createObjectURL(newCover)
    } else {
      coverPreview.value = defaultCover
    }
  }
)

// Handle upload cover
const handleCoverChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) {
    form.cover = file
  }
}

// Submit form
const submit = () => {
  form.post(route('books.store'))
}
</script>

<template>
  <Head title="Tambah Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Tambah Buku Baru</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Cover Buku -->
        <div>
          <label class="block text-sm font-medium mb-2">Cover Buku</label>
          <div class="flex items-start space-x-4">
            <!-- Preview -->
            <div
              class="w-32 h-44 border rounded overflow-hidden bg-gray-50 flex items-center justify-center shadow"
            >
              <img
                :src="coverPreview"
                alt="Preview Cover"
                class="object-cover w-full h-full"
              />
            </div>
            <!-- Input -->
            <div class="flex-1">
              <input
                type="file"
                accept="image/*"
                @change="handleCoverChange"
                class="w-full border rounded p-2"
              />
              <p v-if="form.errors.cover" class="text-red-500 text-sm mt-1">
                {{ form.errors.cover }}
              </p>
              <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, max 2MB</p>
            </div>
          </div>
        </div>

        <!-- ISBN -->
        <div>
          <label class="block text-sm font-medium">ISBN</label>
          <input
            v-model="form.isbn"
            type="text"
            class="w-full border rounded p-2"
          />
          <p v-if="form.errors.isbn" class="text-red-500 text-sm">
            {{ form.errors.isbn }}
          </p>
        </div>

        <!-- Judul -->
        <div>
          <label class="block text-sm font-medium">Judul</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full border rounded p-2"
          />
          <p v-if="form.errors.title" class="text-red-500 text-sm">
            {{ form.errors.title }}
          </p>
        </div>

        <!-- Penulis & Penerbit -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Penulis</label>
            <input
              v-model="form.author"
              type="text"
              class="w-full border rounded p-2"
            />
          </div>
          <div>
            <label class="block text-sm font-medium">Penerbit</label>
            <input
              v-model="form.publisher"
              type="text"
              class="w-full border rounded p-2"
            />
          </div>
        </div>

        <!-- Tahun & Halaman -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Tahun</label>
            <input
              v-model="form.year"
              type="number"
              class="w-full border rounded p-2"
            />
          </div>
          <div>
            <label class="block text-sm font-medium">Jumlah Halaman</label>
            <input
              v-model="form.pages"
              type="number"
              class="w-full border rounded p-2"
            />
          </div>
        </div>

        <!-- Kategori -->
        <div>
          <label class="block text-sm font-medium">Kategori</label>
          <select v-model="form.category_id" class="w-full border rounded p-2">
            <option value="">-- Pilih Kategori --</option>
            <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <p v-if="form.errors.category_id" class="text-red-500 text-sm">
            {{ form.errors.category_id }}
          </p>
        </div>

        <!-- Deskripsi -->
        <div>
          <label class="block text-sm font-medium">Deskripsi</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full border rounded p-2"
          ></textarea>
        </div>

        <!-- File Buku -->
        <div>
          <label class="block text-sm font-medium">File Buku (PDF/EPUB)</label>
          <input
            type="file"
            @change="form.file = $event.target.files[0]"
            class="w-full border rounded p-2"
          />
          <p v-if="form.errors.file" class="text-red-500 text-sm">
            {{ form.errors.file }}
          </p>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-2">
          <Link href="/books" class="px-4 py-2 bg-gray-300 rounded">Batal</Link>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded"
            :disabled="form.processing"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
