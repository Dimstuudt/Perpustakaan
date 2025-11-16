<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  books: {
    data: Array<{
      id: number
      title: string
      author: string
      isbn?: string
      publisher?: string
      publication_year?: number
      cover_url: string | null
      category?: { id: number; name: string }
    }>
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  categories: Array<{ id: number; name: string }>
  publishers: Array<string>
  years: Array<number>
  filters: {
    search?: string
    category_id?: number
    year?: number
    publisher?: string
    sort?: string
  }
}>()

// Filter states
const search = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category_id || null)
const selectedYear = ref(props.filters.year || null)
const selectedPublisher = ref(props.filters.publisher || '')
const sortBy = ref(props.filters.sort || 'newest')

// UI states
const showFilters = ref(false)
const isFiltering = ref(false)

// Active filters count
const activeFiltersCount = computed(() => {
  let count = 0
  if (search.value) count++
  if (selectedCategory.value) count++
  if (selectedYear.value) count++
  if (selectedPublisher.value) count++
  return count
})

// Apply filters
const applyFilters = () => {
  isFiltering.value = true

  router.get('/all-books', {
    search: search.value || undefined,
    category_id: selectedCategory.value || undefined,
    year: selectedYear.value || undefined,
    publisher: selectedPublisher.value || undefined,
    sort: sortBy.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isFiltering.value = false
    }
  })
}

// Reset all filters
const resetFilters = () => {
  search.value = ''
  selectedCategory.value = null
  selectedYear.value = null
  selectedPublisher.value = ''
  sortBy.value = 'newest'
  applyFilters()
}

// Remove individual filter
const removeFilter = (filterType: string) => {
  switch(filterType) {
    case 'search':
      search.value = ''
      break
    case 'category':
      selectedCategory.value = null
      break
    case 'year':
      selectedYear.value = null
      break
    case 'publisher':
      selectedPublisher.value = ''
      break
  }
  applyFilters()
}

// Watch sort change
watch(sortBy, () => {
  applyFilters()
})
</script>

<template>
  <PublicLayout>
    <Head title="Semua Buku" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-indigo-50/30 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Semua Koleksi Buku</h1>
              <p class="text-gray-600 dark:text-gray-400">Jelajahi {{ books.total }} buku dalam koleksi kami</p>
            </div>
          </div>

          <!-- Breadcrumb -->
          <nav class="flex items-center space-x-2 text-sm mb-6">
            <Link href="/" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</Link>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white font-semibold">Semua Buku</span>
          </nav>

          <!-- Filter Bar (Horizontal) -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <!-- Toggle Button for Mobile -->
            <button
              @click="showFilters = !showFilters"
              class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg mb-4"
            >
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-semibold text-gray-900 dark:text-white">Filter & Pencarian</span>
                <span v-if="activeFiltersCount > 0" class="px-2 py-0.5 bg-indigo-600 text-white text-xs rounded-full">
                  {{ activeFiltersCount }}
                </span>
              </div>
              <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Filters Content -->
            <div :class="['lg:block', { 'hidden': !showFilters }]">
              <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Cari Buku
                  </label>
                  <div class="relative">
                    <input
                      v-model="search"
                      @keypress.enter="applyFilters"
                      type="text"
                      placeholder="Judul, Penulis, ISBN..."
                      class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                    />
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                </div>

                <!-- Category -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Kategori
                  </label>
                  <select
                    v-model="selectedCategory"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                  >
                    <option :value="null">Semua</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>

                <!-- Year -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Tahun
                  </label>
                  <select
                    v-model="selectedYear"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                  >
                    <option :value="null">Semua</option>
                    <option v-for="year in years" :key="year" :value="year">
                      {{ year }}
                    </option>
                  </select>
                </div>

                <!-- Publisher -->
                <div>
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Penerbit
                  </label>
                  <select
                    v-model="selectedPublisher"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                  >
                    <option value="">Semua</option>
                    <option v-for="pub in publishers" :key="pub" :value="pub">
                      {{ pub }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-wrap items-center gap-3 mt-4">
                <button
                  @click="applyFilters"
                  :disabled="isFiltering"
                  class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 transition-all duration-200 flex items-center gap-2"
                >
                  <svg v-if="!isFiltering" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                  <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isFiltering ? 'Mencari...' : 'Cari Buku' }}
                </button>

                <button
                  v-if="activeFiltersCount > 0"
                  @click="resetFilters"
                  class="px-6 py-2.5 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Reset Filter
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Filters Tags & Sort Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
          <!-- Active Filters Tags -->
          <div v-if="activeFiltersCount > 0" class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktif:</span>

            <button
              v-if="search"
              @click="removeFilter('search')"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              "{{ search }}"
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <button
              v-if="selectedCategory"
              @click="removeFilter('category')"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
            >
              {{ categories.find(c => c.id === selectedCategory)?.name }}
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <button
              v-if="selectedYear"
              @click="removeFilter('year')"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors"
            >
              {{ selectedYear }}
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <button
              v-if="selectedPublisher"
              @click="removeFilter('publisher')"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors"
            >
              {{ selectedPublisher }}
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Sort -->
          <div class="flex items-center gap-2 ml-auto">
            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Urutkan:</label>
            <select
              v-model="sortBy"
              class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all text-sm"
            >
              <option value="newest">Terbaru</option>
              <option value="oldest">Terlama</option>
              <option value="title_asc">Judul A-Z</option>
              <option value="title_desc">Judul Z-A</option>
              <option value="author_asc">Penulis A-Z</option>
              <option value="author_desc">Penulis Z-A</option>
            </select>
          </div>
        </div>

        <!-- Results Info -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
          <p class="text-sm text-blue-800 dark:text-blue-300">
            Menampilkan <span class="font-bold">{{ books.data.length }}</span> dari <span class="font-bold">{{ books.total }}</span> buku
            <span v-if="activeFiltersCount > 0">dengan {{ activeFiltersCount }} filter aktif</span>
          </p>
        </div>

        <!-- Books Grid with 3D Effect -->
        <div v-if="books.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-6 mb-8">
          <Link
            v-for="book in books.data"
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
                      :alt="book.title"
                      class="w-full h-full object-cover"
                    />
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <!-- Quick View -->
                    <div class="absolute top-3 right-3 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 shadow-lg">
                      <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </div>

                    <!-- Badge -->
                    <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                      <div class="bg-emerald-500 dark:bg-emerald-600 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center justify-center gap-1 shadow-lg">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
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
                <h3 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug mb-1.5">
                  {{ book.title }}
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-1 mb-2">
                  {{ book.author }}
                </p>
              </div>

              <div class="flex items-center justify-between gap-2 mt-auto">
                <span v-if="book.category" class="inline-flex items-center text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full font-medium truncate flex-1">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  {{ book.category.name }}
                </span>
                <div class="flex items-center gap-0.5 text-amber-400 dark:text-amber-500 flex-shrink-0">
                  <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                </div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak ada buku ditemukan</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-4">Coba ubah atau reset filter Anda</p>
          <button
            @click="resetFilters"
            class="px-6 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors"
          >
            Reset Filter
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="books.last_page > 1" class="flex items-center justify-center gap-2 flex-wrap">
          <Link
            v-for="page in books.last_page"
            :key="page"
            :href="`/all-books?page=${page}`"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              page === books.current_page
                ? 'bg-indigo-600 dark:bg-indigo-500 text-white shadow-lg'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700'
            ]"
          >
            {{ page }}
          </Link>
        </div>

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
