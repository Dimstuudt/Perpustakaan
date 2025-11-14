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
const showFilters = ref(true)
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

            <!-- Toggle Filters Button (Mobile) -->
            <button
              @click="showFilters = !showFilters"
              class="lg:hidden px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg flex items-center gap-2 text-gray-700 dark:text-gray-200"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filter ({{ activeFiltersCount }})
            </button>
          </div>

          <!-- Breadcrumb -->
          <nav class="flex items-center space-x-2 text-sm">
            <Link href="/" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</Link>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white font-semibold">Semua Buku</span>
          </nav>
        </div>

        <div class="lg:flex lg:gap-8">

          <!-- Sidebar Filters -->
          <aside
            v-show="showFilters"
            class="lg:w-80 mb-8 lg:mb-0"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-24">

              <!-- Header -->
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                  </svg>
                  Filter
                </h2>
                <button
                  v-if="activeFiltersCount > 0"
                  @click="resetFilters"
                  class="text-sm text-red-600 dark:text-red-400 hover:underline"
                >
                  Reset Semua
                </button>
              </div>

              <!-- Search -->
              <div class="mb-6">
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
                  <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>

              <!-- Category -->
              <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Kategori
                </label>
                <select
                  v-model="selectedCategory"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                >
                  <option :value="null">Semua Kategori</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <!-- Year -->
              <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Tahun Terbit
                </label>
                <select
                  v-model="selectedYear"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                >
                  <option :value="null">Semua Tahun</option>
                  <option v-for="year in years" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>
              </div>

              <!-- Publisher -->
              <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  Penerbit
                </label>
                <select
                  v-model="selectedPublisher"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                >
                  <option value="">Semua Penerbit</option>
                  <option v-for="pub in publishers" :key="pub" :value="pub">
                    {{ pub }}
                  </option>
                </select>
              </div>

              <!-- Apply Button -->
              <button
                @click="applyFilters"
                :disabled="isFiltering"
                class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 transition-all duration-200 flex items-center justify-center gap-2"
              >
                <svg v-if="!isFiltering" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isFiltering ? 'Memfilter...' : 'Terapkan Filter' }}
              </button>

            </div>
          </aside>

          <!-- Main Content -->
          <main class="flex-1">

            <!-- Active Filters & Sort -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

              <!-- Active Filters -->
              <div v-if="activeFiltersCount > 0" class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Filter aktif:</span>

                <button
                  v-if="search"
                  @click="removeFilter('search')"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                  {{ search }}
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
                  Tahun {{ selectedYear }}
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
              <div class="flex items-center gap-2">
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

            <!-- Books Grid -->
            <div v-if="books.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
              <Link
                v-for="book in books.data"
                :key="book.id"
                :href="route('public.preview', book.id)"
                class="group block"
              >
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden shadow-lg">
                  <img
                    v-if="book.cover_url"
                    :src="book.cover_url"
                    :alt="book.title"
                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500 dark:from-indigo-500 dark:to-purple-600">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="mt-3">
                  <h3 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 mb-1">
                    {{ book.title }}
                  </h3>
                  <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-1">
                    {{ book.author }}
                  </p>
                  <div v-if="book.category" class="mt-2">
                    <span class="inline-block px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs rounded-full">
                      {{ book.category.name }}
                    </span>
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
            <div v-if="books.last_page > 1" class="flex items-center justify-center gap-2">
              <Link
                v-for="page in books.last_page"
                :key="page"
                :href="`/all-books?page=${page}`"
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-all',
                  page === books.current_page
                    ? 'bg-indigo-600 dark:bg-indigo-500 text-white'
                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                ]"
              >
                {{ page }}
              </Link>
            </div>

          </main>
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
</style>
