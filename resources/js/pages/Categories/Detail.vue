<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  category: Object,
  books: Array
})

const searchQuery = ref('')
const activeSearch = ref('')
const isSearching = ref(false)

const filteredBooks = computed(() => {
  if (!activeSearch.value.trim()) {
    return props.books || []
  }

  const query = activeSearch.value.toLowerCase()
  return (props.books || []).filter(book => {
    const titleMatch = book.title?.toLowerCase().includes(query)
    const authorMatch = book.author?.toLowerCase().includes(query)
    return titleMatch || authorMatch
  })
})

const handleSearch = () => {
  isSearching.value = true
  setTimeout(() => {
    activeSearch.value = searchQuery.value
    isSearching.value = false
  }, 500)
}

const handleClear = () => {
  searchQuery.value = ''
  activeSearch.value = ''
}

const handleKeypress = (e) => {
  if (e.key === 'Enter') {
    handleSearch()
  }
}
</script>

<template>
  <PublicLayout>
    <Head :title="`Kategori: ${category.name}`" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm mb-6">
        <Link href="/" class="flex items-center text-gray-500 hover:text-blue-600 transition-colors duration-200">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Home
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <Link href="/categoried" class="text-gray-500 hover:text-blue-600 transition-colors duration-200">
          Kategori
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-900 font-semibold">{{ category.name }}</span>
      </nav>

      <!-- Header -->
      <div class="relative mb-10 p-8 bg-gradient-to-br from-indigo-600 via-blue-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl transform -translate-x-24 translate-y-24"></div>

        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
          <div>
            <div class="flex items-center space-x-3 mb-2">
              <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </div>
              <h1 class="text-3xl font-bold text-white">{{ category.name }}</h1>
            </div>
            <div class="flex items-center space-x-2 text-blue-100">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span class="text-base font-medium">{{ books?.length || 0 }} buku tersedia</span>
            </div>
          </div>

          <Link
            href="/categoried"
            class="px-5 py-2.5 bg-white/20 backdrop-blur-md hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 flex items-center space-x-2 border border-white/30 hover:scale-105 active:scale-95"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali</span>
          </Link>
        </div>
      </div>

<!-- Search Bar - Above Grid -->
<div class="mb-6">
  <div class="flex gap-2">
    <div class="relative flex-1 max-w-md">
      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" :class="{ 'animate-pulse': isSearching }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <input
        v-model="searchQuery"
        @keypress="handleKeypress"
        type="text"
        placeholder="Cari judul atau penulis..."
        class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200 shadow-sm"
        :disabled="isSearching"
      />
      <div v-if="searchQuery && !isSearching" class="absolute inset-y-0 right-0 pr-4 flex items-center">
        <button
          @click="handleClear"
          class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-4 flex items-center">
        <svg class="w-5 h-5 text-blue-500 dark:text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>
    <button
      @click="handleSearch"
      :disabled="isSearching || !searchQuery.trim()"
      class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 transition-all duration-200 flex items-center gap-2"
    >
      <svg v-if="!isSearching" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>{{ isSearching ? 'Mencari...' : 'Cari' }}</span>
    </button>
  </div>
  <p v-if="activeSearch" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
    Menampilkan {{ filteredBooks.length }} dari {{ books?.length || 0 }} buku
  </p>
</div>

    <!-- Grid Buku dengan 3D Effect -->
    <div v-if="filteredBooks && filteredBooks.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
      <Link
        v-for="book in filteredBooks"
        :key="book.id"
        :href="`/book/${book.id}`"
        class="group block"
      >
        <div class="book-card">
          <!-- Book Container with 3D -->
          <div class="book-3d-container">
            <div class="book-3d">
              <!-- Front Cover -->
              <div class="book-front">
                <div class="relative w-full h-full">
                  <img
                    :src="book.cover_url || '/images/default-book.png'"
                    :alt="book.title"
                    class="w-full h-full object-cover"
                  />
                  <!-- Gradient Overlay on Hover -->
                  <div
                    class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300
                          bg-gradient-to-t from-black/60 via-transparent to-transparent dark:from-black/40"
                  ></div>

                  <!-- Quick View Badge -->
                  <div
                    class="absolute top-3 right-3 rounded-full p-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0
                          bg-white/90 backdrop-blur-sm dark:bg-gray-800/80"
                  >
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Book Spine -->
              <div class="book-spine"></div>

              <!-- Book Shadow -->
              <div class="book-shadow"></div>
            </div>
          </div>

          <!-- Book Info -->
          <div class="mt-4 px-1">
            <h3 class="text-sm font-bold line-clamp-2 mb-1.5 leading-snug
                      text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
              {{ book.title }}
            </h3>
            <p class="text-xs line-clamp-1 mb-2
                      text-gray-500 dark:text-gray-400">
              {{ book.author || 'Tidak diketahui' }}
            </p>

            <!-- Rating or Category Badge -->
            <div class="flex items-center justify-between">
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                      bg-blue-50 text-blue-700 dark:bg-blue-800 dark:text-blue-200
                      group-hover:bg-blue-100 dark:group-hover:bg-blue-700 transition-colors"
              >
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Buku
              </span>
            </div>
          </div>
        </div>
      </Link>
    </div>


     <!-- Jika Tidak Ada Buku -->
    <div
      v-if="!filteredBooks || filteredBooks.length === 0"
      class="text-center py-20 rounded-2xl border-2 border-dashed border-gray-300
            bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900"
    >
      <div class="relative inline-block mb-6">
        <!-- Background Glow -->
        <div
          class="absolute inset-0 rounded-2xl blur-xl opacity-20
                bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-700 dark:to-purple-900"
        ></div>

        <!-- Icon Container -->
        <div
          class="relative w-20 h-20 rounded-2xl flex items-center justify-center
                bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-800 dark:to-purple-800"
        >
          <svg class="w-10 h-10 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="activeSearch" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <h3 v-if="activeSearch" class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Hasil</h3>
      <h3 v-else class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Belum Ada Buku</h3>

      <!-- Description -->
      <p v-if="activeSearch" class="text-gray-600 dark:text-gray-300 max-w-md mx-auto mb-6">
        Tidak ditemukan buku dengan kata kunci <span class="font-semibold text-blue-600">"{{ activeSearch }}"</span>. Coba kata kunci lain.
      </p>
      <p v-else class="text-gray-600 dark:text-gray-300 max-w-md mx-auto">
        Belum ada buku dalam kategori
        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ category.name }}</span>.
        Mulai tambahkan buku untuk mengisi kategori ini dan mempermudah pengelolaan koleksi.
      </p>

      <!-- Action Button -->
      <button
        v-if="activeSearch"
        @click="handleClear"
        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Hapus Pencarian
      </button>
    </div>

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
