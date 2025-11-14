<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{
  cabinets: Array
  latestBooks?: {
    id: number
    title: string
    author: string
    description?: string
    cover_url: string | null
    category?: { id: number; name: string } | string
  }[]
}>()
</script>

<template>
  <PublicLayout>
    <Head title="Daftar Cabinet" />

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm mb-6">
        <Link href="/" class="flex items-center text-gray-500 hover:text-blue-600 transition-colors duration-200">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Home
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-900 font-semibold">Daftar Cabinet</span>
      </nav>

      <!-- Header Section with Gradient Background -->
      <div class="relative mb-10 p-8 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-xl overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl transform -translate-x-24 translate-y-24"></div>

        <div class="relative z-10">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <h1 class="text-4xl font-bold text-white">Koleksi Cabinet</h1>
          </div>
          <p class="text-blue-100 text-lg max-w-2xl">Jelajahi koleksi lemari penyimpanan Anda. Kelola dan atur rak-rak dengan sistem yang terorganisir.</p>

          <!-- Stats Badge -->
          <div class="mt-6 inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="text-white font-semibold">{{ cabinets.length }} Cabinet Tersedia</span>
          </div>
        </div>
      </div>

      <!-- Cabinets Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-16">
        <div
          v-for="cabinet in cabinets"
          :key="cabinet.id"
          class="group relative bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300"
        >
          <!-- Badge Code - Top Right Corner -->
          <div class="absolute top-4 right-4 z-20">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition-opacity"></div>
              <div class="relative px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg">
                <span class="text-xs font-bold text-white tracking-wider">
                  {{ cabinet.code || 'UNKNOWN' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Hover Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 via-indigo-50/80 to-purple-50/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

          <!-- Card Content -->
          <div class="relative p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-gray-700">
            <!-- Cabinet Icon -->
            <div class="w-16 h-16 mb-4 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 dark:shadow-gray-600">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>

            <!-- Cabinet Name -->
            <h2 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-blue-700 dark:text-white dark:group-hover:text-blue-400 transition-colors duration-300 pr-20">
              {{ cabinet.name }}
            </h2>

            <!-- Divider Line -->
            <div class="h-px bg-gradient-to-r from-blue-200 via-purple-200 to-pink-200 dark:from-gray-600 dark:via-gray-500 dark:to-gray-600 mb-4"></div>

            <!-- Racks Count Info -->
            <div class="mb-5">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Rak</span>
                <div class="flex items-center space-x-1.5">
                  <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                  <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ cabinet.racks_count }}
                  </span>
                </div>
              </div>

              <!-- Visual Progress Bar -->
              <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden dark:bg-gray-700">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full transform origin-left group-hover:scale-x-100 scale-x-75 transition-transform duration-500"></div>
              </div>

              <p class="text-xs mt-2 flex items-center text-gray-500 dark:text-gray-400">
                <svg class="w-3.5 h-3.5 mr-1 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Siap untuk dikelola
              </p>
            </div>

            <!-- Action Button -->
            <Link
              :href="route('koleksi', { cabinet: cabinet.id })"
              class="group/btn relative w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 overflow-hidden dark:shadow-gray-700 dark:from-blue-500 dark:via-indigo-500 dark:to-purple-500"
            >
              <!-- Animated Background -->
              <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300 dark:from-purple-500 dark:via-pink-500 dark:to-blue-500"></div>

              <!-- Button Content -->
              <span class="relative z-10 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Jelajahi Rak
                <svg class="w-5 h-5 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </span>

              <!-- Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 transform -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"></div>
            </Link>
          </div>

          <!-- Animated Bottom Border -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="cabinets.length === 0" class="text-center py-20 bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl border-2 border-dashed border-gray-300 mb-16">
        <div class="relative inline-block mb-6">
          <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl blur-xl opacity-20"></div>
          <div class="relative w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl flex items-center justify-center">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Cabinet</h3>
        <p class="text-gray-600 text-lg max-w-md mx-auto">Mulai dengan menambahkan lemari penyimpanan baru untuk mengatur koleksi Anda dengan lebih baik.</p>
      </div>

      <!-- BOOKS COLLECTION SECTION -->
      <section v-if="latestBooks && latestBooks.length > 0" class="py-16 bg-gradient-to-br from-gray-50 to-blue-50/30 dark:bg-gradient-to-br dark:from-gray-900 dark:to-gray-800 rounded-3xl">
        <div class="px-6">
          <div class="flex justify-between items-end mb-8">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Buku Terbaru</h2>
              <p class="text-gray-600 dark:text-gray-400">Koleksi terbaru yang tersedia untuk dipinjam</p>
            </div>
            <!-- <Link
              href="/koleksi"
              class="hidden md:flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold text-sm"
            >
              Lihat Semua
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link> -->
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            <Link
              v-for="book in latestBooks.slice(0, 18)"
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

          <!-- View All Button for Mobile -->
          <!-- <div class="md:hidden text-center mt-8">
            <Link
              href="/koleksi"
              class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
            >
              Lihat Semua Buku
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link>
          </div> -->
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

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
.book-card:nth-child(7) { animation-delay: 0.35s; }
.book-card:nth-child(8) { animation-delay: 0.4s; }
.book-card:nth-child(9) { animation-delay: 0.45s; }
.book-card:nth-child(10) { animation-delay: 0.5s; }
.book-card:nth-child(11) { animation-delay: 0.55s; }
.book-card:nth-child(12) { animation-delay: 0.6s; }
</style>
