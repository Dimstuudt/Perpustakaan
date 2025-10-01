<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch } from 'vue'

interface Book {
  id: number
  isbn: string
  title: string
  author: string
  publisher: string | null
  year: number | null
  pages: number | null
  type: string | null
  category: { id: number; name: string } | null
  description: string | null
  cover_url: string | null
  stock: number
  fee: number
}

interface RelatedBook {
  id: number
  title: string
  author: string
  cover_url: string | null
}

// Props dari backend
const props = defineProps<{
  book: Book
  relatedBooks?: RelatedBook[]
  hasPendingLoan: boolean
}>()

// Cek apakah user login
const page = usePage()
const isLoggedIn = !!page.props.auth?.user

// Watch flash message
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.message) Swal.fire('Berhasil', flash.message, 'success')
    if (flash?.error) Swal.fire('Gagal', flash.error, 'error')
  },
  { immediate: true, deep: true }
)

// Function pinjam buku
const pinjamBuku = () => {
  if (!isLoggedIn) {
    router.get('/login')
    return
  }

  if (props.hasPendingLoan) {
    Swal.fire({
      icon: 'info',
      title: 'Info',
      text: 'Anda sudah memiliki peminjaman yang belum selesai. Cek status di dashboard.',
    })
    return
  }

  if (props.book.stock <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Habis',
      text: 'Buku ini sedang habis, tidak bisa dipinjam.',
    })
    return
  }

  const feeText = props.book.fee && props.book.fee > 0
    ? `Rp ${props.book.fee.toLocaleString()}`
    : 'GRATIS'

  const feeClass = props.book.fee && props.book.fee > 0
    ? 'bg-green-100 text-green-800'
    : 'bg-green-600 text-white'

  Swal.fire({
    title: `<strong>Ajukan Peminjaman?</strong>`,
    html: `
      <div style="text-align: left; margin-top: 10px;">
        <p><strong>Buku:</strong> ${props.book.title}</p>
        <p><strong>Penulis:</strong> ${props.book.author}</p>
        <p><strong>Kategori:</strong> ${props.book.category?.name ?? '-'}</p>
        <p><strong>Fee:</strong>
          <span style="
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            ${props.book.fee && props.book.fee > 0
              ? 'background-color: #DCFCE7; color: #166534;'
              : 'background-color: #16A34A; color: white;'
            }
          ">
            ${feeText}
          </span>
        </p>
        <p><strong>Batas pengembalian:</strong> 7 hari. Jika terlambat akan dikenakan denda.</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, pinjam!',
    cancelButtonText: 'Batal',
    width: '450px',
    showCloseButton: true,
    focusConfirm: false,
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('user.loans.store'), { book_id: props.book.id }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Cek status di dashboard.',
            timer: 2000,
            showConfirmButton: false,
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat meminjam buku.',
          })
        }
      })
    }
  })
}

</script>

<template>
  <PublicLayout>
    <div class="max-w-5xl mx-auto px-6 py-10">
      <!-- Card Buku -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3">
          <!-- Cover -->
          <div class="flex items-center justify-center bg-gray-100 dark:bg-gray-700 p-4">
            <img
              :src="props.book.cover_url ?? 'https://via.placeholder.com/300x450'"
              alt="cover"
              class="w-[250px] h-[350px] object-cover rounded"
            />
          </div>

          <!-- Info -->
          <div class="md:col-span-2 p-6 flex flex-col">
            <h1 class="text-2xl font-bold">{{ props.book.title }}</h1>
            <p class="text-sm text-gray-500 mt-1">oleh {{ props.book.author }}</p>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mt-3">
              <span
                v-if="props.book.category"
                class="inline-block bg-sky-100 text-sky-700 text-xs px-3 py-1 rounded-full"
              >
                {{ props.book.category.name }}
              </span>

              <span
                v-if="props.book.type"
                :class="[
                  'inline-block text-xs px-3 py-1 rounded-full',
                  props.book.type === 'ebook' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'
                ]"
              >
                {{ props.book.type }}
              </span>

          <span
  class="inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full"
>
  {{ props.book.fee && props.book.fee > 0 ? 'Rp ' + props.book.fee.toLocaleString() : 'GRATIS' }}
</span>


            </div>

            <!-- Detail Buku -->
            <div class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-300">
              <p><strong>ISBN:</strong> {{ props.book.isbn }}</p>
              <p v-if="props.book.publisher"><strong>Penerbit:</strong> {{ props.book.publisher }}</p>
              <p v-if="props.book.year"><strong>Tahun:</strong> {{ props.book.year }}</p>
              <p v-if="props.book.pages"><strong>Halaman:</strong> {{ props.book.pages }} hlm</p>
              <p><strong>Stok:</strong> {{ props.book.stock > 0 ? props.book.stock + ' tersedia' : 'Habis' }}</p>
            </div>

            <!-- Deskripsi -->
            <div class="mt-5 text-gray-700 dark:text-gray-300 leading-relaxed text-sm whitespace-pre-line">
              {{ props.book.description ?? 'Belum ada deskripsi untuk buku ini.' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol Aksi -->
      <div class="mt-6 flex gap-3">
        <a
          href="/welcome"
          class="bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600"
        >
          ← Kembali
        </a>
        <button
          class="bg-sky-600 text-white px-4 py-2 rounded-md shadow hover:bg-sky-700 transition"
          :disabled="props.book.stock <= 0"
          @click="pinjamBuku"
        >
          📖 Pinjam Buku
        </button>
      </div>

      <!-- Buku Terkait -->
      <div v-if="props.relatedBooks && props.relatedBooks.length" class="mt-10">
        <h2 class="text-xl font-semibold mb-4">Buku Terkait</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
          <div
            v-for="rb in props.relatedBooks"
            :key="rb.id"
            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-md transition flex flex-col text-sm"
          >
            <div class="relative w-full bg-gray-100 dark:bg-gray-700">
              <img
                :src="rb.cover_url ?? 'https://via.placeholder.com/150x220'"
                alt="cover"
                class="w-full h-44 object-cover"
              />
            </div>
            <div class="p-3 flex-1 flex flex-col">
              <h3 class="font-semibold line-clamp-2">{{ rb.title }}</h3>
              <p class="text-gray-500 mt-1">{{ rb.author }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
