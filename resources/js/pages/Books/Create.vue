<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import BookForm from '@/components/BookForm.vue'
import useBookForms from '@/composables/useBookForms'
import { type BreadcrumbItem } from '@/types'
import { ref } from 'vue'

// Props kategori
const props = defineProps<{
  categories: { id: number; name: string }[]
}>()

// Breadcrumb
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Tambah Buku', href: '/books/create' },
]

// Import composable
const {
  forms,
  activeIndex,
  MAX_FORMS,
  addFormManual,
  handleMultipleFiles,
  processAllOCR,
  nextForm,
  prevForm,
  submitAll,
  addFileToQueue, // buat OCR
} = useBookForms()

// Kamera
const video = ref<HTMLVideoElement | null>(null)
const canvas = ref<HTMLCanvasElement | null>(null)
const showCamera = ref(false)

async function openCamera() {
  showCamera.value = true
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: "environment" },
    })
    if (video.value) video.value.srcObject = stream
  } catch (err) {
    console.error("Gagal akses kamera:", err)
    showCamera.value = false
  }
}

function takePhoto() {
  if (!video.value || !canvas.value) return
  const ctx = canvas.value.getContext('2d')
  canvas.value.width = video.value.videoWidth
  canvas.value.height = video.value.videoHeight
  ctx?.drawImage(video.value, 0, 0)
  canvas.value.toBlob(blob => {
    if (blob) {
      const file = new File([blob], `photo-${Date.now()}.png`, { type: "image/png" })
      addFileToQueue(file) // masukin ke OCR file
      showCamera.value = false
      const tracks = (video.value?.srcObject as MediaStream)?.getTracks()
      tracks?.forEach(t => t.stop())
    }
  })
}
</script>

<template>
  <Head title="Tambah Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-5xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="text-3xl font-bold text-gray-800">📚 Tambah Buku</h1>
        <span v-if="forms.length" class="text-sm text-gray-500">
          Total Form: {{ forms.length }}/{{ MAX_FORMS }}
        </span>
        <!-- Tombol Import Excel -->
        <Link
          href="/books/import"
          class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow"
        >
          📥 Import dari Excel
        </Link>
      </div>

      <!-- Aksi Tambah -->
      <div class="bg-white shadow rounded-xl p-5 flex flex-wrap gap-4">
        <button
          @click="addFormManual"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 transition text-white rounded-lg shadow disabled:opacity-50"
          :disabled="forms.length >= MAX_FORMS"
        >
          + Form Manual
        </button>

        <!-- Upload dari file manager (OCR) -->
        <label
          class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg shadow cursor-pointer disabled:opacity-50"
        >
          <input
            type="file"
            accept="image/*"
            multiple
            class="hidden"
            :disabled="forms.length >= MAX_FORMS"
            @change="handleMultipleFiles"
          />
          📂 Pilih dari File (OCR)
        </label>

        <!-- Kamera (OCR) -->
        <button
          @click="openCamera"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-700 transition text-white rounded-lg shadow disabled:opacity-50"
          :disabled="forms.length >= MAX_FORMS"
        >
          📷 Ambil Foto (OCR)
        </button>

        <!-- Tombol proses OCR semua -->
        <button
          @click="processAllOCR"
          class="px-4 py-2 bg-orange-500 hover:bg-orange-600 transition text-white rounded-lg shadow disabled:opacity-50"
          :disabled="!forms.length"
        >
          🔍 Proses OCR Semua
        </button>
      </div>

      <!-- Kamera Modal -->
      <div
        v-if="showCamera"
        class="fixed inset-0 bg-black bg-opacity-80 flex flex-col items-center justify-center z-50"
      >
        <video ref="video" autoplay playsinline class="w-full max-w-md rounded-lg shadow-lg"></video>
        <canvas ref="canvas" class="hidden"></canvas>
        <div class="mt-4 flex gap-4">
          <button @click="takePhoto" class="px-6 py-2 bg-green-600 text-white rounded-lg">
            📸 Ambil
          </button>
          <button @click="showCamera=false" class="px-6 py-2 bg-gray-500 text-white rounded-lg">
            ❌ Batal
          </button>
        </div>
      </div>

      <!-- Navigasi Slide -->
      <div
        v-if="forms.length"
        class="bg-gray-50 border rounded-xl p-4 flex items-center justify-between"
      >
        <button
          @click="prevForm"
          :disabled="activeIndex === 0"
          class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg disabled:opacity-50"
        >
          ◀ Prev
        </button>
        <span class="font-medium text-gray-700">
          Buku <span class="text-blue-600">{{ activeIndex + 1 }}</span> dari {{ forms.length }}
        </span>
        <button
          @click="nextForm"
          :disabled="activeIndex === forms.length - 1"
          class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg disabled:opacity-50"
        >
          Next ▶
        </button>
      </div>

      <!-- Form Aktif -->
      <div v-if="forms.length" class="bg-white shadow rounded-xl p-6">
        <BookForm v-model="forms[activeIndex]" :categories="props.categories" />
      </div>

      <!-- Submit Semua -->
      <div v-if="forms.length" class="flex justify-end">
        <button
          @click="submitAll"
          class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 transition text-white rounded-lg shadow"
        >
          💾 Simpan Semua Buku
        </button>
      </div>
    </div>
  </AppLayout>
</template>
