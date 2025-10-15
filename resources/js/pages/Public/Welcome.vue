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
                <a href="/koleksi" class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
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

      <!-- WHY CHOOSE US -->
      <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 text-white py-24">
        <div class="max-w-7xl mx-auto px-6">
          <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
              <div>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Mengapa Memilih Perpustakaan Kami?</h2>
                <p class="text-xl text-blue-100">
                  Kami berkomitmen memberikan pelayanan terbaik untuk mendukung literasi masyarakat
                </p>
              </div>

              <div class="space-y-6">
                <div class="flex gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-bold text-lg mb-2">Koleksi Terawat & Terpercaya</h3>
                    <p class="text-blue-100">Semua buku dalam kondisi baik dan dirawat dengan standar perpustakaan profesional</p>
                  </div>
                </div>

                <div class="flex gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-bold text-lg mb-2">Tanpa Biaya Tersembunyi</h3>
                    <p class="text-blue-100">Gratis mendaftar, gratis meminjam, tanpa biaya deposit atau administrasi apapun</p>
                  </div>
                </div>

                <div class="flex gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-bold text-lg mb-2">Komunitas Pembaca Aktif</h3>
                    <p class="text-blue-100">Bergabung dengan ribuan anggota yang antusias berbagi pengalaman membaca</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="relative">
              <img
                src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=800&auto=format&fit=crop"
                alt="Library Interior"
                class="rounded-2xl shadow-2xl border-4 border-white/20"
              />
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

      <!-- BOOKS COLLECTION WITH 3D EFFECT -->
<section id="koleksi" class="py-16 bg-gray-50 dark:bg-gray-900">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex justify-between items-end mb-8">
      <div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Koleksi Terbaru</h2>
        <p class="text-gray-600 dark:text-gray-400">Buku berkualitas siap dipinjam</p>
      </div>
      <Link
        href="/koleksi"
        class="hidden md:flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold text-sm"
      >
        Lihat Semua
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </Link>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-6">
      <Link
        v-for="book in latestBooks"
        :key="book.id"
        :href="route('public.preview', book.id)"
        class="group block book-card h-full"
      >
        <!-- 3D Book Container -->
        <div class="book-3d-container">
          <div class="book-3d">
            <!-- Front Cover -->
            <div class="book-front">
              <div class="relative w-full h-full aspect-[3/4] overflow-hidden rounded-lg">
                <img
                  :src="book.cover_url ?? 'https://via.placeholder.com/300x400/6366f1/ffffff?text=No+Cover'"
                  alt="cover"
                  class="w-full h-full object-cover"
                />
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <!-- Quick View -->
                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 shadow-lg">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>

                <!-- Badge -->
                <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                  <div class="bg-emerald-500 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center justify-center gap-1 shadow-lg">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                    </svg>
                    Tersedia
                  </div>
                </div>
              </div>
            </div>

            <div class="book-spine"></div>
            <div class="book-shadow"></div>
          </div>
        </div>

        <!-- Book Info -->
        <div class="mt-4 px-1 flex flex-col justify-between min-h-[90px]">
          <div>
            <h3
              class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug mb-1.5">
              {{ book.title }}
            </h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-1 mb-2">
              {{ book.author }}
            </p>
          </div>

          <div class="flex items-center justify-between gap-2 mt-auto">
            <span v-if="book.category"
              class="inline-flex items-center text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full font-medium truncate flex-1">
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              {{ typeof book.category === 'object' ? book.category.name : book.category }}
            </span>
            <div class="flex items-center gap-0.5 text-amber-400 flex-shrink-0">
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
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
            <Link
              v-for="cat in categories"
              :key="cat.id"
              :href="`/categories/${cat.id}`"
              class="group p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg border border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-all hover:scale-105"
            >
              <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-950 dark:to-purple-950 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
                <h3 class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">{{ cat.name }}</h3>
              </div>
            </Link>
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

/* 3D Book Card Styles */
.book-card {
  position: relative;
  transition: transform 0.3s ease;
}

.book-3d-container {
  perspective: 1500px;
  perspective-origin: center center;
}

.book-3d {
  position: relative;
  width: 100%;
  aspect-ratio: 2/3;
  transform-style: preserve-3d;
  transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.book-card:hover .book-3d {
  transform: rotateY(-25deg) translateX(8px) translateZ(20px);
}

.book-front {
  position: absolute;
  width: 100%;
  height: 100%;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06),
    inset 0 0 0 1px rgba(0, 0, 0, 0.05);
  transform: translateZ(20px);
  transition: box-shadow 0.3s ease;
}

.book-card:hover .book-front {
  box-shadow:
    0 20px 25px -5px rgba(0, 0, 0, 0.15),
    0 10px 10px -5px rgba(0, 0, 0, 0.08),
    inset 0 0 0 1px rgba(0, 0, 0, 0.05);
}

.book-spine {
  position: absolute;
  width: 40px;
  height: 100%;
  right: -20px;
  background: linear-gradient(
    to right,
    rgba(0, 0, 0, 0.2) 0%,
    rgba(0, 0, 0, 0.08) 20%,
    rgba(255, 255, 255, 0.05) 50%,
    rgba(0, 0, 0, 0.08) 80%,
    rgba(0, 0, 0, 0.15) 100%
  );
  transform: rotateY(90deg);
  transform-origin: left center;
  border-radius: 0 8px 8px 0;
  transition: opacity 0.3s ease;
}

.book-card:hover .book-spine {
  opacity: 1;
}

.book-shadow {
  position: absolute;
  bottom: -10px;
  left: 10%;
  width: 80%;
  height: 20px;
  background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.3) 0%, transparent 70%);
  transform: translateZ(-20px);
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.book-card:hover .book-shadow {
  opacity: 1;
  transform: translateZ(-20px) translateY(5px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .book-card:hover .book-3d {
    transform: rotateY(-15deg) translateX(5px) translateZ(10px);
  }

  .book-spine {
    width: 30px;
    right: -15px;
  }
}

/* Animation on load */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.book-card {
  animation: fadeInUp 0.5s ease-out backwards;
}

.book-card:nth-child(1) { animation-delay: 0.05s; }
.book-card:nth-child(2) { animation-delay: 0.1s; }
.book-card:nth-child(3) { animation-delay: 0.15s; }
.book-card:nth-child(4) { animation-delay: 0.2s; }
.book-card:nth-child(5) { animation-delay: 0.25s; }
.book-card:nth-child(6) { animation-delay: 0.3s; }
</style>
