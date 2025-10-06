<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

defineProps<{
  stats: { books: number; members: number; articles: number; loans: number }
  latestBooks: {
    id: number
    title: string
    author: string
    description?: string
    cover_url: string | null
    category?: { id: number; name: string } | string
  }[]
  categories: { id: number; name: string }[]
}>()

function formatNumber(n: number) {
  return n.toLocaleString()
}

// Carousel state
const currentSlide = ref(0)
const carouselImages = [
  'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1200&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1200&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1200&auto=format&fit=crop'
]

let carouselInterval: number

onMounted(() => {
  carouselInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % carouselImages.length
  }, 5000)
})

onUnmounted(() => {
  clearInterval(carouselInterval)
})
</script>

<template>
  <PublicLayout>
    <div class="bg-white dark:bg-gray-950">

      <!-- HERO SECTION -->
      <section class="relative bg-gradient-to-b from-slate-50 to-white dark:from-gray-900 dark:to-gray-950">
        <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
          <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
              <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-full">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">Platform Terpercaya</span>
              </div>

              <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                Perpustakaan Fisik Modern
              </h1>

              <p class="text-xl text-gray-600 dark:text-gray-300">
                Pinjam buku fisik dengan mudah. Sistem digital yang memudahkan akses ke ribuan koleksi berkualitas.
              </p>

              <div class="grid grid-cols-2 gap-3">
                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Biaya Terjangkau</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Tanpa biaya tersembunyi</p>
                  </div>
                </div>

                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Proses Cepat</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Dalam 5 menit</p>
                  </div>
                </div>

                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Aman Terjamin</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Sistem terverifikasi</p>
                  </div>
                </div>

                <div class="flex items-start gap-2">
                  <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Koleksi Lengkap</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ formatNumber(stats.books) }}+ buku</p>
                  </div>
                </div>
              </div>

              <div class="flex flex-wrap gap-3 pt-2">
                <Link href="/register" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                  Daftar Sekarang
                </Link>
                <a href="#koleksi" class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                  Lihat Koleksi
                </a>
              </div>
            </div>

            <div class="relative">
              <div class="relative h-[500px] rounded-2xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-800">
                <div
                  v-for="(image, index) in carouselImages"
                  :key="index"
                  class="absolute inset-0 transition-opacity duration-1000"
                  :class="currentSlide === index ? 'opacity-100' : 'opacity-0'"
                >
                  <img :src="image" alt="Library" class="w-full h-full object-cover"/>
                  <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                </div>

                <div class="absolute bottom-6 left-6 right-6 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm rounded-xl p-4">
                  <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                      <p class="text-xl font-bold text-blue-600">{{ formatNumber(stats.books) }}+</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">Buku</p>
                    </div>
                    <div class="border-x border-gray-200 dark:border-gray-700">
                      <p class="text-xl font-bold text-emerald-600">{{ formatNumber(stats.members) }}+</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">Anggota</p>
                    </div>
                    <div class="border-r border-gray-200 dark:border-gray-700">
                      <p class="text-xl font-bold text-purple-600">{{ formatNumber(stats.loans) }}+</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">Dipinjam</p>
                    </div>
                    <div>
                      <p class="text-xl font-bold text-amber-600">99%</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">Puas</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
                <div
                  v-for="(_, index) in carouselImages"
                  :key="index"
                  class="h-1.5 rounded-full transition-all duration-300"
                  :class="currentSlide === index ? 'bg-blue-600 w-8' : 'bg-gray-300 w-1.5'"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- STATS BAR -->
      <section class="bg-gray-50 dark:bg-gray-900 border-y border-gray-200 dark:border-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
              <div class="text-3xl font-bold text-blue-600 mb-1">{{ formatNumber(stats.books) }}+</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Koleksi Buku</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-emerald-600 mb-1">{{ formatNumber(stats.members) }}+</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Anggota Aktif</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-purple-600 mb-1">{{ formatNumber(stats.loans) }}+</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Total Peminjaman</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-amber-600 mb-1">24/7</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Layanan Online</div>
            </div>
          </div>
        </div>
      </section>

      <!-- HOW IT WORKS -->
      <section class="py-16 bg-white dark:bg-gray-950">
        <div class="max-w-6xl mx-auto px-6">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Cara Meminjam</h2>
            <p class="text-gray-600 dark:text-gray-400">3 langkah mudah</p>
          </div>

          <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center group">
              <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-center text-xl font-bold mb-4 shadow-lg group-hover:scale-110 transition-transform">
                1
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Daftar</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Buat akun dengan data lengkap</p>
            </div>

            <div class="text-center group">
              <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-xl flex items-center justify-center text-xl font-bold mb-4 shadow-lg group-hover:scale-110 transition-transform">
                2
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Pilih Buku</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Pesan buku secara online</p>
            </div>

            <div class="text-center group">
              <div class="mx-auto w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl flex items-center justify-center text-xl font-bold mb-4 shadow-lg group-hover:scale-110 transition-transform">
                3
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Ambil & Baca</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Ambil di perpustakaan</p>
            </div>
          </div>
        </div>
      </section>

      <!-- BOOKS COLLECTION -->
      <section id="koleksi" class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
          <div class="flex justify-between items-end mb-8">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Koleksi Terbaru</h2>
              <p class="text-gray-600 dark:text-gray-400">Buku berkualitas siap dipinjam</p>
            </div>
            <Link href="/books" class="hidden md:flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold text-sm">
              Lihat Semua
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </Link>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <Link
              v-for="book in latestBooks"
              :key="book.id"
              :href="route('public.preview', book.id)"
              class="group"
            >
              <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-xl border border-gray-200 dark:border-gray-700 transition-all duration-300 h-full flex flex-col">
                <div class="aspect-[3/4] overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                  <img
                    :src="book.cover_url ?? 'https://via.placeholder.com/300x400/6366f1/ffffff?text=No+Cover'"
                    alt="cover"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                </div>

                <div class="p-3 flex flex-col flex-grow">
                  <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 mb-1.5 group-hover:text-blue-600 transition-colors h-10">{{ book.title }}</h3>
                  <p class="text-xs text-gray-600 dark:text-gray-400 mb-2.5 line-clamp-1 h-5">{{ book.author }}</p>
                  <div class="flex items-center justify-between mt-auto gap-2">
                    <span v-if="book.category" class="inline-block text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded font-medium truncate flex-1">
                      {{ typeof book.category === 'object' ? book.category.name : book.category }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex-shrink-0">
                      <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                    </span>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>
      </section>

      <!-- CATEGORIES -->
      <section class="py-16 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-6">
          <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Kategori</h2>
            <p class="text-gray-600 dark:text-gray-400">Temukan berdasarkan minat</p>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <div
              v-for="cat in categories"
              :key="cat.id"
              class="group p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 hover:border-blue-500 cursor-pointer transition-all"
            >
              <div class="text-center">
                <div class="w-10 h-10 mx-auto mb-2 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-950 dark:to-purple-950 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
                <h3 class="font-semibold text-xs text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-1">{{ cat.name }}</h3>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="py-16 bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
        <div class="max-w-3xl mx-auto px-6 text-center">
          <h2 class="text-3xl lg:text-4xl font-bold mb-4">
            Mulai Perjalanan Literasi
          </h2>
          <p class="text-lg text-blue-100 mb-6">
            Akses ribuan buku berkualitas dengan sistem yang mudah
          </p>

          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <Link href="/register" class="px-8 py-3 bg-white text-blue-600 rounded-lg font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
              Daftar Sekarang
            </Link>
            <Link href="/login" class="px-8 py-3 border-2 border-white/50 backdrop-blur-sm rounded-lg font-bold hover:bg-white/10 transition-all">
              Login
            </Link>
          </div>
        </div>
      </section>

    </div>
  </PublicLayout>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
