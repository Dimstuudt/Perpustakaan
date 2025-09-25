<script setup lang="ts">
import { ref } from 'vue'

interface BookForm {
  isbn: string
  title: string
  author: string
  publisher: string
  year: string
  pages: string
  description: string
  category_id: string | number
  file: File | null               // File buku (PDF/EPUB)
  cover: File | null              // Sampul
  coverPreview: string
  ocrFile: File | null            // Gambar hasil kamera/file manager
  ocrPreview: string | null       // Preview gambar OCR
  ocrResult: string               // Hasil OCR
  loadingOCR: boolean             // Status OCR
  type: 'ebook' | 'physical'       // Tipe buku
}

const props = defineProps<{
  modelValue: BookForm
  categories: { id: number; name: string }[]
}>()

const emit = defineEmits(['update:modelValue'])

const update = (key: keyof BookForm, value: any) => {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

// 🔹 Upload cover buku
const handleCoverChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) {
    update('cover', file) // simpan file asli
    update('coverPreview', URL.createObjectURL(file)) // simpan preview
  } else {
    update('cover', null)
    update('coverPreview', '/images/dummy-cover.png')
  }
}

// 🔹 Upload file buku (PDF/EPUB)
const handleFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0] || null
  update('file', file)
}
</script>

<template>
  <div class="bg-white shadow-md rounded-xl p-6 space-y-6">
    <!-- Cover Buku -->
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Buku</label>
      <div class="flex items-start space-x-4">
        <div class="w-32 h-44 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center shadow">
          <img
            :src="props.modelValue.coverPreview || '/images/dummy-cover.png'"
            alt="Preview Cover"
            class="object-cover w-full h-full"
          />
        </div>
        <div class="flex-1">
          <input
            type="file"
            accept="image/*"
            @change="handleCoverChange"
            class="block text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-700"
          />
        </div>
      </div>
    </div>

    <!-- OCR Section -->
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil OCR</label>

      <div v-if="props.modelValue.ocrPreview" class="mb-3">
        <img
          :src="props.modelValue.ocrPreview"
          alt="Preview OCR"
          class="w-40 h-56 object-cover rounded-lg shadow border"
        />
      </div>

      <div v-if="props.modelValue.loadingOCR" class="text-blue-500 text-sm font-medium mb-2">
        ⏳ Sedang membaca teks...
      </div>

      <textarea
        v-if="props.modelValue.ocrResult || props.modelValue.loadingOCR"
        v-model="props.modelValue.ocrResult"
        rows="6"
        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"
      ></textarea>
    </div>

    <!-- Input Buku -->
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">ISBN</label>
        <input
          v-model="props.modelValue.isbn"
          type="text"
          class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
        <input
          v-model="props.modelValue.title"
          type="text"
          class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Penulis</label>
          <input
            v-model="props.modelValue.author"
            type="text"
            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Penerbit</label>
          <input
            v-model="props.modelValue.publisher"
            type="text"
            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
          <input
            v-model="props.modelValue.year"
            type="number"
            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Halaman</label>
          <input
            v-model="props.modelValue.pages"
            type="number"
            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
        </div>
      </div>

      <div class="flex space-x-4 mt-2">
  <label>
    <input type="radio" value="physical" v-model="props.modelValue.type" />
    Buku Fisik
  </label>
  <label>
    <input type="radio" value="ebook" v-model="props.modelValue.type" />
    Ebook
  </label>
</div>


      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
        <select
          v-model="props.modelValue.category_id"
          class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >
          <option value="">-- Pilih Kategori --</option>
          <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
        <textarea
          v-model="props.modelValue.description"
          rows="4"
          class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        ></textarea>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">File Buku (PDF/EPUB)</label>
        <input
          type="file"
          @change="handleFileChange"
          class="block text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-700"
        />
      </div>
    </div>
  </div>
</template>



