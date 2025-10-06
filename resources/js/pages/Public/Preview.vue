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

const page = usePage()
const isLoggedIn = !!page.props.auth?.user

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.message) Swal.fire('Berhasil', flash.message, 'success')
    if (flash?.error) Swal.fire('Gagal', flash.error, 'error')
  },
  { immediate: true, deep: true }
)

// SweetAlert sebelum diarahkan ke halaman show
const pinjamBuku = (id: number) => {
  if (!isLoggedIn) {
    router.get('/login')
    return
  }

  Swal.fire({
    title: '📖 Pinjam Buku',
    html: 'Kamu akan diarahkan ke halaman detail peminjaman buku. <br>Pastikan stok masih tersedia.',
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Lanjutkan',
    cancelButtonText: 'Kembali',
    reverseButtons: true,
    width: 400,
  }).then((result) => {
    if (result.isConfirmed) {
      router.get(route('user.loans.show', id))
    }
  })
}
</script>

<template>
  <PublicLayout>
    <div class="max-w-5xl mx-auto px-6 py-10">
      <!-- Buku Utama -->
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
            <div class="mt-5 text-gray-700 dark:text-gray-300 leading-relaxed text-sm whitespace-pre-line line-clamp-4">
              {{ props.book.description ?? 'Belum ada deskripsi untuk buku ini.' }}
            </div>

            <!-- Tombol Pinjam -->
            <div class="mt-5">
              <button
                class="bg-sky-600 text-white px-4 py-2 rounded-md shadow hover:bg-sky-700 transition"
                :disabled="props.book.stock <= 0"
                @click="pinjamBuku(props.book.id)"
              >
                📖 Pinjam Buku
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Buku Terkait -->
      <div v-if="props.relatedBooks && props.relatedBooks.length" class="mt-10">
        <h2 class="text-xl font-semibold mb-4">Buku Terkait</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
          <div
            v-for="rb in props.relatedBooks"
            :key="rb.id"
            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-md transition flex flex-col"
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
              <button
                class="mt-auto bg-sky-500 text-white text-xs px-3 py-1 rounded hover:bg-sky-600 transition"
                @click="pinjamBuku(rb.id)"
              >
                📖 Pinjam
              </button>
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

.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
