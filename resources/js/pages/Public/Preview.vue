<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch, ref } from 'vue'

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

const props = defineProps<{
  book: Book
  relatedBooks?: RelatedBook[]
  hasPendingLoan: boolean
}>()

const page = usePage()
const isLoggedIn = !!page.props.auth?.user
const showFullDescription = ref(false)

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.message) Swal.fire('Berhasil', flash.message, 'success')
    if (flash?.error) Swal.fire('Gagal', flash.error, 'error')
  },
  { immediate: true, deep: true }
)

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

function goBack() {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.visit('/koleksi')
  }
}
</script>

<template>
  <PublicLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-gray-900 dark:to-indigo-950 transition-colors duration-500">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Back Button -->
        <button
          @click="goBack"
          class="group inline-flex items-center space-x-2 mb-6 px-4 py-2 bg-white/80 dark:bg-gray-800/60 backdrop-blur-sm rounded-full text-gray-700 dark:text-gray-200 hover:bg-white hover:shadow-md dark:hover:bg-gray-700 transition-all duration-300 border border-gray-200 dark:border-gray-700"
        >
          <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          <span class="font-medium">Kembali</span>
        </button>

        <!-- Main Book Card -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800 transition-colors duration-500">
          <div class="grid grid-cols-1 lg:grid-cols-5">

            <!-- Book Cover Section -->
            <div class="lg:col-span-2 relative bg-gradient-to-br from-indigo-100 via-blue-50 to-purple-100 dark:from-gray-800 dark:via-indigo-950 dark:to-gray-900 p-8 flex items-center justify-center">
              <div class="absolute top-0 left-0 w-40 h-40 bg-blue-400/20 rounded-full blur-3xl"></div>
              <div class="absolute bottom-0 right-0 w-56 h-56 bg-purple-400/20 rounded-full blur-3xl"></div>

              <div class="relative z-10 group">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur-2xl opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                <div class="relative">
                  <div class="absolute -left-3 top-4 bottom-4 w-3 bg-gradient-to-r from-gray-800 to-gray-700 rounded-l-lg opacity-60"></div>

                  <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border-4 border-white dark:border-gray-700 transform group-hover:scale-105 group-hover:rotate-1 transition-all duration-500">
                    <img
                      :src="props.book.cover_url ?? 'https://via.placeholder.com/400x600'"
                      :alt="props.book.title"
                      class="w-80 h-[480px] object-cover"
                    />
                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-gradient-to-b from-gray-300 via-white to-gray-300 dark:from-gray-700 dark:via-gray-800 dark:to-gray-700">
                      <div class="absolute inset-y-0 right-0 w-px bg-gray-400 dark:bg-gray-600"></div>
                    </div>
                  </div>

                  <div class="absolute -bottom-2 left-0 right-0 h-4 bg-black/20 blur-xl rounded-full transform scale-95"></div>
                </div>
              </div>
            </div>

            <!-- Book Details Section -->
            <div class="lg:col-span-3 p-8 lg:p-10">
              <div class="flex flex-wrap gap-2 mb-4">
                <span
                  v-if="props.book.category"
                  class="inline-flex items-center px-4 py-1.5 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 dark:from-blue-950 dark:to-indigo-950 dark:text-blue-300 text-sm font-semibold rounded-full border border-blue-200 dark:border-blue-800"
                >
                  <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  {{ props.book.category.name }}
                </span>
                <span
                  v-if="props.book.type"
                  :class="[
                    'inline-flex items-center px-4 py-1.5 text-sm font-semibold rounded-full border',
                    props.book.type === 'ebook'
                      ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border-green-200 dark:from-green-900 dark:to-emerald-950 dark:text-green-300 dark:border-green-800'
                      : 'bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 border-amber-200 dark:from-amber-950 dark:to-orange-950 dark:text-amber-300 dark:border-amber-800'
                  ]"
                >
                  {{ props.book.type === 'ebook' ? '📱 E-Book' : '📚 Fisik' }}
                </span>
              </div>

              <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                {{ props.book.title }}
              </h1>
              <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ props.book.author }}
              </p>

              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950 dark:to-indigo-950 rounded-xl p-4 border border-blue-100 dark:border-blue-800">
                  <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">ISBN</div>
                  <div class="font-bold text-gray-900 dark:text-gray-100 text-sm">{{ props.book.isbn }}</div>
                </div>
                <div v-if="props.book.publisher" class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-950 dark:to-pink-950 rounded-xl p-4 border border-purple-100 dark:border-purple-800">
                  <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Penerbit</div>
                  <div class="font-bold text-gray-900 dark:text-gray-100 text-sm truncate">{{ props.book.publisher }}</div>
                </div>
                <div v-if="props.book.year" class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950 dark:to-emerald-950 rounded-xl p-4 border border-green-100 dark:border-green-800">
                  <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Tahun</div>
                  <div class="font-bold text-gray-900 dark:text-gray-100 text-sm">{{ props.book.year }}</div>
                </div>
                <div v-if="props.book.pages" class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950 dark:to-orange-950 rounded-xl p-4 border border-amber-100 dark:border-amber-800">
                  <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Halaman</div>
                  <div class="font-bold text-gray-900 dark:text-gray-100 text-sm">{{ props.book.pages }} hlm</div>
                </div>
              </div>

              <!-- Stock & Fee -->
              <div class="flex flex-wrap gap-3 mb-6">
                <div class="flex items-center space-x-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  <div>
                    <div class="text-xs text-emerald-100">Stok Tersedia</div>
                    <div class="text-xl font-bold text-white">{{ props.book.stock }} Buku</div>
                  </div>
                </div>

                <div class="flex items-center space-x-2 px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div>
                    <div class="text-xs text-blue-100">Biaya Pinjam</div>
                    <div class="text-xl font-bold text-white">
                      {{ props.book.fee && props.book.fee > 0 ? 'Rp ' + props.book.fee.toLocaleString() : 'GRATIS' }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Deskripsi
                </h3>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                  <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line" :class="{ 'line-clamp-4': !showFullDescription }">
                    {{ props.book.description ?? 'Belum ada deskripsi untuk buku ini.' }}
                  </p>
                  <button
                    v-if="props.book.description && props.book.description.length > 200"
                    @click="showFullDescription = !showFullDescription"
                    class="mt-3 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold text-sm flex items-center"
                  >
                    {{ showFullDescription ? 'Tampilkan Lebih Sedikit' : 'Selengkapnya' }}
                    <svg
                      class="w-4 h-4 ml-1 transition-transform"
                      :class="{ 'rotate-180': showFullDescription }"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Action -->
              <div>
                <button
                  @click="pinjamBuku(props.book.id)"
                  :disabled="props.book.stock <= 0"
                  class="group w-full relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative z-10 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ props.book.stock <= 0 ? 'Stok Habis' : 'Pinjam Sekarang' }}
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Books -->
        <div v-if="props.relatedBooks && props.relatedBooks.length" class="mt-12">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Buku Terkait</h2>
            <div class="h-1 flex-1 ml-6 bg-gradient-to-r from-blue-500 via-purple-500 to-transparent rounded-full"></div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <div
              v-for="rb in props.relatedBooks"
              :key="rb.id"
              class="group bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer border border-gray-100 dark:border-gray-800"
            >
              <div class="relative aspect-[2/3] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 overflow-hidden">
                <img
                  :src="rb.cover_url ?? 'https://via.placeholder.com/200x300'"
                  :alt="rb.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <button
                      @click="pinjamBuku(rb.id)"
                      class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center space-x-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <span>Lihat Detail</span>
                    </button>
                  </div>
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm line-clamp-2 mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                  {{ rb.title }}
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ rb.author }}</p>
              </div>
              <div class="h-1 bg-gradient-to-r from-blue-500 to-purple-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
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
