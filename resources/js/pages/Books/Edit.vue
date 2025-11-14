<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import Swal from 'sweetalert2' // 🔥 Import SweetAlert2
import { computed } from 'vue'


const props = defineProps<{
  book: {
    id: number
    isbn: string | null
    title: string | null
    author: string | null
    publisher: string | null
    year: number | null
    pages: number | null
    description: string | null
    category_id: number | null
    cover_url?: string | null
    file_url?: string | null
    type?: string | null
    stock?: number | null

    // 🔥 WAJIB ADA (fix masalah rak & fee)
    rack_id: number | null
    fee: number | null
  }

  categories: { id: number; name: string }[]
  racks: { id: number; name: string; cabinet: { id: number; name: string } }[]
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Edit Buku', href: route('books.edit', props.book.id) },
]

// Form state
const form = useForm({
  _method: 'POST',
  isbn: props.book.isbn,
  title: props.book.title,
  author: props.book.author,
  publisher: props.book.publisher,
  year: props.book.year,
  pages: props.book.pages,
  description: props.book.description,
  category_id: props.book.category_id,
  file: null,
  cover: null,
  type: (props.book.type ?? 'physical') as BookType, // default fallback
  stock: props.book.stock ?? 0,
  fee: props.book.fee ?? 0,
  rack_id: props.book.rack_id ?? null,

})

// Cover preview
const defaultCover = '/images/dummy-cover.png'
const coverPreview = ref<string>(props.book.cover_url ?? defaultCover)

watch(
  () => form.cover,
  (newCover) => {
    if (newCover) {
      coverPreview.value = URL.createObjectURL(newCover)
    } else {
      coverPreview.value = props.book.cover_url ?? defaultCover
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

// ✅ Submit form dengan SweetAlert konfirmasi & notifikasi
const submit = () => {
  Swal.fire({
    title: 'Konfirmasi',
    text: 'Yakin ingin update data buku ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Update',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      form.post(route('books.update', props.book.id), {
        forceFormData: true,
        onError: (errors) => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal Update',
            text: Object.values(errors).join(', '),
          })
        },
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Buku berhasil diupdate 🎉',
            timer: 2000,
            showConfirmButton: false,
          })
        },
      })
    }
  })
}

// ================= OCR =================
import Tesseract from 'tesseract.js'

const ocrResult = ref('')
const loadingOCR = ref(false)

const handleOCR = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return

  loadingOCR.value = true
  ocrResult.value = '⏳ Sedang membaca teks...'

  Tesseract.recognize(file, 'eng+ind', { logger: (m) => console.log(m) })
    .then(({ data: { text } }) => {
      ocrResult.value = text
      parseToForm(text) // otomatis isi form
      loadingOCR.value = false

      Swal.fire({
        icon: 'success',
        title: 'OCR Selesai',
        text: 'Data berhasil dibaca dari gambar 📖',
        timer: 2000,
        showConfirmButton: false,
      })
    })
    .catch((err) => {
      ocrResult.value = 'Gagal OCR: ' + err.message
      loadingOCR.value = false

      Swal.fire({
        icon: 'error',
        title: 'OCR Error',
        text: err.message,
      })
    })
}

const parseToForm = (text: string) => {
  const lines = text.split('\n').map((l) => l.trim()).filter((l) => l)

  lines.forEach((line, index) => {
    if (/isbn/i.test(line)) {
      form.isbn = line.replace(/isbn[: ]?/i, '').trim()
    } else if (/judul/i.test(line)) {
      form.title = line.replace(/judul[: ]?/i, '').trim()
    } else if (/penulis/i.test(line)) {
      form.author = line.replace(/penulis[: ]?/i, '').trim()
    } else if (/penerbit/i.test(line)) {
      form.publisher = line.replace(/penerbit[: ]?/i, '').trim()
    } else if (/tahun/i.test(line)) {
      form.year = line.replace(/tahun[: ]?/i, '').trim()
    } else if (/halaman/i.test(line)) {
      form.pages = line.replace(/halaman[: ]?/i, '').trim()
    } else if (!form.title && index === 0) {
      // fallback: baris pertama dianggap judul
      form.title = line.trim()
    }
  })

  // ==== Deskripsi multi-line ====
  const descMatch = text.match(/Deskripsi[:\s]+([\s\S]+)/i)
  if (descMatch) {
    form.description = descMatch[1].trim()
  }
}

// rack assignment
const selectedCabinet = computed(() => {
  const rack = props.racks.find(r => r.id === form.rack_id)
  return rack ? rack.cabinet : null
})

</script>


<template>
  <Head title="Edit Buku" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Edit Buku</h1>

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

          <!-- OCR Gambar Buku -->
<div>
  <label class="block text-sm font-medium mb-2">Scan OCR (opsional)</label>
  <input
    type="file"
    accept="image/*"
    @change="handleOCR"
    class="w-full border rounded p-2"
  />
  <div v-if="loadingOCR" class="text-blue-500 mt-2">Membaca teks dari gambar...</div>
  <pre v-if="ocrResult" class="bg-gray-100 p-2 mt-2 rounded text-xs">{{ ocrResult }}</pre>
</div>

        <!-- ISBN -->
        <div>
          <label class="block text-sm font-medium">ISBN</label>
          <input v-model="form.isbn" type="text" class="w-full border rounded p-2" />
          <p v-if="form.errors.isbn" class="text-red-500 text-sm">
            {{ form.errors.isbn }}
          </p>
        </div>

        <!-- Judul -->
        <div>
          <label class="block text-sm font-medium">Judul</label>
          <input v-model="form.title" type="text" class="w-full border rounded p-2" />
          <p v-if="form.errors.title" class="text-red-500 text-sm">
            {{ form.errors.title }}
          </p>
        </div>

        <!-- Penulis & Penerbit -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Penulis</label>
            <input v-model="form.author" type="text" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Penerbit</label>
            <input v-model="form.publisher" type="text" class="w-full border rounded p-2" />
          </div>
        </div>

        <!-- Tahun & Halaman -->
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
<!-- Pilih Rak -->
<div>
  <label class="block text-sm font-medium">Rak Buku</label>

  <select v-model="form.rack_id" class="w-full border rounded p-2">
    <option disabled value="">-- Pilih Rak --</option>

    <option v-for="rack in props.racks" :key="rack.id" :value="rack.id">
      {{ rack.name }} - Cabinet: {{ rack.cabinet.name }}
    </option>
  </select>

  <p v-if="form.errors.rack_id" class="text-red-500 text-sm">
    {{ form.errors.rack_id }}
  </p>
</div>

<!-- Info cabinet kecil, ga perlu card -->
<p v-if="selectedCabinet" class="text-xs text-gray-500 mt-1">
  Cabinet otomatis: <strong>{{ selectedCabinet.name }}</strong>
</p>


        <!-- Stok Buku -->
<div>
  <label class="block text-sm font-medium">Stok</label>
  <input
    v-model="form.stock"
    type="number"
    min="0"
    class="w-full border rounded p-2"
  />
  <p v-if="form.errors.stock" class="text-red-500 text-sm">
    {{ form.errors.stock }}
  </p>
</div>

<!-- Fee Buku -->
<div>
  <label class="block text-sm font-medium">Fee</label>
  <input
    v-model="form.fee"
    type="number"
    min="0"
    class="w-full border rounded p-2"
  />
  <p v-if="form.errors.fee" class="text-red-500 text-sm">{{ form.errors.fee }}</p>
</div>


<!-- Tipe Buku -->
 <div class="flex space-x-4 mt-2">
  <label>
    <input type="radio" value="physical" v-model="form.type" /> Buku Fisik
  </label>
  <label>
    <input type="radio" value="ebook" v-model="form.type" /> Ebook
  </label>
</div>


        <!-- Deskripsi -->
        <div>
          <label class="block text-sm font-medium">Deskripsi</label>
          <textarea v-model="form.description" rows="4" class="w-full border rounded p-2"></textarea>
        </div>

        <!-- File Buku -->
        <div>
          <label class="block text-sm font-medium">Ganti File Buku (PDF/EPUB)</label>
          <input type="file" @change="form.file = $event.target.files[0]" class="w-full border rounded p-2" />
          <p v-if="form.errors.file" class="text-red-500 text-sm">{{ form.errors.file }}</p>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-2">
          <Link href="/books" class="px-4 py-2 bg-gray-300 rounded">Batal</Link>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded"
            :disabled="form.processing"
          >
            Update
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
