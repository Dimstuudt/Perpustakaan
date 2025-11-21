<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  file: null,
})

const previewBooks = ref([])
const isDragging = ref(false)

// Computed: Valid category IDs
const validCategoryIds = computed(() => {
  return props.categories.map(cat => cat.id)
})

// Computed: Uncategorized category ID (cari kategori dengan nama "default")
const uncategorizedId = computed(() => {
  const uncatCategory = props.categories.find(cat =>
    cat.name.toLowerCase() === 'default'
  )
  return uncatCategory?.id || null
})

// Valid book types
const validTypes = ['physical', 'digital']

// Check if category is valid
function isCategoryValid(categoryId) {
  return validCategoryIds.value.includes(Number(categoryId))
}

// Check if type is valid
function isTypeValid(type) {
  return validTypes.includes(String(type).toLowerCase())
}

// Auto-fix invalid categories and types
function normalizeBooks() {
  if (!uncategorizedId.value) {
    Swal.fire({
      icon: 'error',
      title: 'Kategori Default Tidak Ditemukan',
      text: 'Pastikan ada kategori bernama "default" di sistem.',
      background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
      color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#000',
    })
    return
  }

  let fixedCategoryCount = 0
  let fixedTypeCount = 0

  previewBooks.value = previewBooks.value.map(book => {
    let updatedBook = { ...book }

    // Fix invalid category
    if (!isCategoryValid(book.category_id)) {
      fixedCategoryCount++
      updatedBook.category_id = uncategorizedId.value
    }

    // Fix invalid type
    if (!isTypeValid(book.type)) {
      fixedTypeCount++
      updatedBook.type = 'physical'
    }

    return updatedBook
  })

  const messages = []
  if (fixedCategoryCount > 0) {
    messages.push(`${fixedCategoryCount} buku dengan kategori invalid → kategori default`)
  }
  if (fixedTypeCount > 0) {
    messages.push(`${fixedTypeCount} buku dengan tipe invalid → physical`)
  }

  if (messages.length > 0) {
    Swal.fire({
      icon: 'success',
      title: 'Data Diperbaiki!',
      html: messages.join('<br>'),
      background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
      color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#000',
    })
  }
}

// Count invalid categories
const invalidCategoriesCount = computed(() => {
  return previewBooks.value.filter(book => !isCategoryValid(book.category_id)).length
})

// Count invalid types
const invalidTypesCount = computed(() => {
  return previewBooks.value.filter(book => !isTypeValid(book.type)).length
})

// Total invalid count
const totalInvalidCount = computed(() => {
  return invalidCategoriesCount.value + invalidTypesCount.value
})

function selectFile(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files?.length) {
    form.file = target.files[0]
    previewExcel(form.file)
  }
}

// Drag and drop handlers
function handleDragOver(event: DragEvent) {
  event.preventDefault()
  isDragging.value = true
}

function handleDragLeave(event: DragEvent) {
  event.preventDefault()
  isDragging.value = false
}

function handleDrop(event: DragEvent) {
  event.preventDefault()
  isDragging.value = false

  const files = event.dataTransfer?.files
  if (files?.length) {
    form.file = files[0]
    previewExcel(form.file)
  }
}

// Preview Excel (client-side read)
function previewExcel(file: File) {
  const reader = new FileReader()
  reader.onload = async (e) => {
    const data = new Uint8Array(e.target?.result as ArrayBuffer)
    const workbook = await import('xlsx').then(xlsx => xlsx.read(data, { type: 'array' }))
    const firstSheet = workbook.Sheets[workbook.SheetNames[0]]
    const jsonData = await import('xlsx').then(xlsx => xlsx.utils.sheet_to_json(firstSheet, { header: 1 }))

    // Skip header row
    previewBooks.value = jsonData.slice(1).map(row => ({
      isbn: row[0] || '',
      title: row[1] || '',
      author: row[2] || '',
      publisher: row[3] || '',
      year: row[4] || '',
      pages: row[5] || '',
      description: row[6] || '',
      category_id: row[7] || '',
      type: row[8] || 'physical',
      stock: row[9] || 0,
      fee: row[10] || 0,
    }))
  }
  reader.readAsArrayBuffer(file)
}

function submit() {
  const isDark = document.documentElement.classList.contains('dark')

  if (totalInvalidCount.value > 0 && uncategorizedId.value) {
    const messages = []
    if (invalidCategoriesCount.value > 0) {
      messages.push(`<strong>${invalidCategoriesCount.value}</strong> buku dengan kategori tidak valid`)
    }
    if (invalidTypesCount.value > 0) {
      messages.push(`<strong>${invalidTypesCount.value}</strong> buku dengan tipe tidak valid`)
    }

    Swal.fire({
      icon: 'warning',
      title: 'Data Tidak Valid Ditemukan!',
      html: `Terdapat:<br>${messages.join('<br>')}<br><br>Pilih tindakan:`,
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Perbaiki Otomatis',
      denyButtonText: 'Import Apa Adanya',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#3085d6',
      denyButtonColor: '#d33',
      background: isDark ? '#1f2937' : '#fff',
      color: isDark ? '#f3f4f6' : '#000',
    }).then((result) => {
      if (result.isConfirmed) {
        normalizeBooks()
        setTimeout(() => processImport(), 500)
      } else if (result.isDenied) {
        processImport()
      }
    })
  } else if (totalInvalidCount.value > 0 && !uncategorizedId.value) {
    Swal.fire({
      icon: 'error',
      title: 'Kategori Default Tidak Ada',
      html: 'Tidak dapat melanjutkan import karena:<br>• Ada data invalid<br>• Tidak ada kategori default "default"',
      background: isDark ? '#1f2937' : '#fff',
      color: isDark ? '#f3f4f6' : '#000',
    })
  } else {
    processImport()
  }
}

function processImport() {
  const isDark = document.documentElement.classList.contains('dark')

  form.post(route('books.import.store'), {
    onSuccess: (page) => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: page.props.flash.success || 'Data berhasil diimport',
        background: isDark ? '#1f2937' : '#fff',
        color: isDark ? '#f3f4f6' : '#000',
      })
      form.reset()
      previewBooks.value = []
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: errors.file || 'Terjadi kesalahan saat mengimport',
        background: isDark ? '#1f2937' : '#fff',
        color: isDark ? '#f3f4f6' : '#000',
      })
    }
  })
}

function downloadTemplate() {
  const link = document.createElement('a')
  link.href = '/TemplateBooksEmpty.xlsx'
  link.download = 'TemplateBooksEmpty.xlsx'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

function clearFile() {
  form.file = null
  previewBooks.value = []
  const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement
  if (fileInput) fileInput.value = ''
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-blue-600 dark:bg-blue-500 rounded-lg shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Import Buku</h1>
              <p class="text-gray-600 dark:text-gray-400">Upload file Excel untuk menambahkan buku secara massal</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column: Upload & Actions -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Upload Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Upload File Excel
              </h2>

              <!-- Drag & Drop Zone -->
              <div
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
                :class="[
                  'border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200',
                  isDragging
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                    : 'border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-gray-50 dark:hover:bg-gray-700/50'
                ]"
              >
                <input
                  type="file"
                  @change="selectFile"
                  accept=".xlsx,.xls,.csv"
                  class="hidden"
                  id="fileInput"
                />

                <label for="fileInput" class="cursor-pointer block">
                  <div class="flex flex-col items-center gap-3">
                    <div class="p-4 bg-blue-100 dark:bg-blue-900/50 rounded-full">
                      <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ form.file ? form.file.name : 'Klik untuk pilih file atau drag & drop' }}
                      </p>
                      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Format: .xlsx, .xls, .csv (Max 10MB)
                      </p>
                    </div>
                  </div>
                </label>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-3 mt-6">
                <button
                  @click="submit"
                  :disabled="form.processing || !form.file"
                  class="flex-1 min-w-[200px] px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                  </svg>
                  {{ form.processing ? 'Mengimport...' : 'Import Sekarang' }}
                </button>

                <button
                  v-if="form.file"
                  @click="clearFile"
                  class="px-6 py-3 bg-red-600 dark:bg-red-500 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-red-700 dark:hover:bg-red-600 transition-all duration-200 flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Clear
                </button>
              </div>

              <!-- Warning for invalid categories -->
              <div v-if="invalidCategoriesCount > 0" class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                <div class="flex items-start gap-3 mb-3">
                  <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                  <div class="flex-1">
                    <p class="font-semibold text-amber-800 dark:text-amber-300">Kategori Tidak Valid!</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                      Terdapat <strong>{{ invalidCategoriesCount }}</strong> buku dengan kategori tidak valid (ditandai merah)
                    </p>
                  </div>
                </div>

                <button
                  v-if="uncategorizedId"
                  @click="normalizeBooks"
                  class="w-full px-4 py-2 bg-amber-600 dark:bg-amber-500 text-white rounded-lg font-semibold hover:bg-amber-700 dark:hover:bg-amber-600 transition-all flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  Perbaiki Otomatis ke Kategori Default
                </button>

                <div v-else class="text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-700">
                  <strong>⚠️ Perhatian:</strong> Tidak ada kategori "default" di sistem. Silakan buat kategori tersebut terlebih dahulu.
                </div>
              </div>
            </div>

            <!-- Preview Excel -->
            <div v-if="previewBooks.length" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Preview Data Excel
                </h2>
                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 rounded-full text-sm font-semibold">
                  {{ previewBooks.length }} buku
                </span>
              </div>

              <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                  <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-200 dark:border-gray-600">
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">ISBN</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Judul</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Penulis</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Penerbit</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Tahun</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Halaman</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Kategori ID</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Tipe</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Stok</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Biaya</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(book, i) in previewBooks"
                      :key="i"
                      class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                      <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ book.isbn }}</td>
                      <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">{{ book.title }}</td>
                      <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.author }}</td>
                      <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.publisher }}</td>
                      <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.year }}</td>
                      <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.pages }}</td>
                      <td
                        :class="[
                          'px-4 py-3 font-semibold',
                          isCategoryValid(book.category_id)
                            ? 'text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20'
                            : 'text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20'
                        ]"
                      >
                        <div class="flex items-center gap-2">
                          <svg
                            v-if="!isCategoryValid(book.category_id)"
                            class="w-4 h-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                          >
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                          </svg>
                          <svg
                            v-else
                            class="w-4 h-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                          >
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                          </svg>
                          {{ book.category_id || '-' }}
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <span :class="[
                          'px-2 py-1 rounded-full text-xs font-semibold',
                          book.type === 'physical'
                            ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300'
                            : 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300'
                        ]">
                          {{ book.type }}
                        </span>
                      </td>
                      <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.stock }}</td>
                      <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">Rp {{ Number(book.fee).toLocaleString('id-ID') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Categories & Template -->
          <div class="space-y-6">
            <!-- Download Template Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-white/20 rounded-lg">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <h2 class="text-lg font-semibold">Template Excel</h2>
              </div>
              <p class="text-indigo-100 dark:text-indigo-200 text-sm mb-4">
                Download template kosong untuk memudahkan input data buku Anda
              </p>
              <button
                @click="downloadTemplate"
                class="w-full px-4 py-3 bg-white text-indigo-600 dark:text-indigo-700 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-indigo-50 transition-all duration-200 flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Template
              </button>
            </div>

            <!-- Categories Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                  <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Daftar Kategori</h2>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Gunakan ID ini untuk kolom Category ID</p>
                </div>
              </div>

              <!-- Default Category Notice -->
              <div v-if="uncategorizedId" class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-sm text-blue-800 dark:text-blue-300">
                    <strong>Kategori Default ID: {{ uncategorizedId }}</strong><br>
                    <span class="text-xs">Buku dengan kategori invalid akan masuk ke sini</span>
                  </p>
                </div>
              </div>

              <div v-if="props.categories.length" class="max-h-[600px] overflow-y-auto">
                <div class="space-y-2">
                  <div
                    v-for="cat in props.categories"
                    :key="cat.id"
                    :class="[
                      'flex items-center justify-between p-3 rounded-lg transition-colors border',
                      cat.id === uncategorizedId
                        ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700 ring-2 ring-blue-200 dark:ring-blue-800'
                        : 'border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'
                    ]"
                  >
                    <div class="flex items-center gap-2">
                      <svg
                        v-if="cat.id === uncategorizedId"
                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                      </svg>
                      <span :class="[
                        'font-medium',
                        cat.id === uncategorizedId ? 'text-blue-800 dark:text-blue-300' : 'text-gray-700 dark:text-gray-200'
                      ]">
                        {{ cat.name }}
                        <span v-if="cat.id === uncategorizedId" class="text-xs">(Default)</span>
                      </span>
                    </div>
                    <span :class="[
                      'px-3 py-1 rounded-full text-sm font-bold',
                      cat.id === uncategorizedId
                        ? 'bg-blue-600 dark:bg-blue-500 text-white'
                        : 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300'
                    ]">
                      ID: {{ cat.id }}
                    </span>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p>Belum ada kategori</p>
              </div>
            </div>

            <!-- Info Card -->
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-700 p-6">
              <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <h3 class="font-semibold text-amber-900 dark:text-amber-300 mb-2">Tips Import</h3>
                  <ul class="text-sm text-amber-800 dark:text-amber-400 space-y-1">
                    <li>• Pastikan format file sesuai template</li>
                    <li>• Category ID harus sesuai daftar</li>
                    <li>• Periksa preview sebelum import</li>
                    <li>• Kategori invalid otomatis masuk ke "default"</li>
                    <li>• Buat kategori "default" sebagai default</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
