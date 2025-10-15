<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

defineProps({
  categories: Array
})

const showModal = ref(false)

// Prevent body scroll when modal is open
watch(showModal, (value) => {
  if (value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})
</script>

<template>
  <PublicLayout>
    <Head title="Kategori Buku" />

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
        <span class="text-gray-900 font-semibold">Kategori Buku</span>
      </nav>

      <!-- Header with Modal Button -->
      <div class="relative mb-8 p-8 bg-gradient-to-br from-indigo-600 via-blue-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
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
              <h1 class="text-3xl font-bold text-white">Kategori Buku</h1>
            </div>
            <p class="text-blue-100 text-base max-w-2xl">Jelajahi berbagai kategori dan temukan buku favorit Anda.</p>
          </div>

          <!-- Quick Jump Button -->
          <button
            @click="showModal = true"
            type="button"
            class="px-5 py-2.5 bg-white/20 backdrop-blur-md hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 flex items-center space-x-2 border border-white/30 hover:scale-105 active:scale-95"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>Langsung ke Kategori</span>
          </button>
        </div>
      </div>

 <!-- Category Grid -->
<div
  v-if="categories && categories.length > 0"
  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
>
  <div
    v-for="category in categories"
    :key="category.id"
    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700"
  >
    <!-- Category Header -->
    <div class="p-5 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 border-b border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
          {{ category.name }}
        </h2>
        <Link
          :href="`/categories/${category.id}`"
          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-semibold transition-colors flex items-center space-x-1"
        >
          <span>Lihat semua</span>
          <svg
            class="w-4 h-4 group-hover:translate-x-1 transition-transform"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </Link>
      </div>
    </div>

    <!-- Book Preview -->
    <div class="p-6">
      <div
        v-if="category.books && category.books.length > 0"
        class="flex items-start space-x-4"
      >
        <!-- Book Cover -->
        <Link
          :href="`/book/${category.books[0].id}`"
          class="book-cover-container flex-shrink-0"
        >
          <div class="book-3d">
            <div class="book-front">
              <img
                :src="category.books[0].cover_url || '/images/default-book.png'"
                :alt="`Cover ${category.books[0].title}`"
                class="w-full h-full object-cover"
              />
            </div>
            <div class="book-spine"></div>
          </div>
        </Link>

        <!-- Book Info -->
        <div class="flex-1 min-w-0">
          <Link
            :href="`/book/${category.books[0].id}`"
            class="block group/title"
          >
            <h3
              class="text-base font-semibold text-gray-800 dark:text-gray-100 group-hover/title:text-blue-600 dark:group-hover/title:text-blue-400 transition-colors line-clamp-2 mb-2"
            >
              {{ category.books[0].title }}
            </h3>
          </Link>
          <p
            v-if="category.books[0].author"
            class="text-sm text-gray-500 dark:text-gray-400 mb-3"
          >
            {{ category.books[0].author }}
          </p>
          <div class="flex items-center text-xs text-gray-400 dark:text-gray-500">
            <svg
              class="w-4 h-4 mr-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
              />
            </svg>
            <span>Buku Terbaru</span>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-8">
        <div
          class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3"
        >
          <svg
            class="w-8 h-8 text-gray-400 dark:text-gray-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
            />
          </svg>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada buku</p>
      </div>
    </div>
  </div>
</div>

<!-- Empty State for No Categories -->
<div
  v-else
  class="text-center py-20 rounded-2xl border-2 border-dashed transition-all duration-300
         border-gray-300 dark:border-gray-700
         bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800"
>
  <div class="relative inline-block mb-6">
    <!-- Glow background -->
    <div
      class="absolute inset-0 rounded-2xl blur-xl opacity-20
             bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-400 dark:to-purple-500"
    ></div>

    <!-- Icon container -->
    <div
      class="relative w-20 h-20 rounded-2xl flex items-center justify-center
             bg-gradient-to-br from-blue-100 to-purple-100 dark:from-gray-800 dark:to-gray-700"
    >
      <svg
        class="w-10 h-10 text-blue-600 dark:text-blue-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 10h16M4 14h16M4 18h16"
        />
      </svg>
    </div>
  </div>

  <h3
    class="text-2xl font-bold mb-2
           text-gray-900 dark:text-gray-100 transition-colors"
  >
    Belum Ada Kategori
  </h3>

  <p
    class="max-w-md mx-auto text-gray-600 dark:text-gray-400 transition-colors"
  >
    Mulai tambahkan kategori untuk mengelompokkan koleksi buku Anda.
  </p>
</div>

    </div>

    <!-- Modal Overlay -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showModal"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click="showModal = false"
        >
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="showModal"
              @click.stop
              class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden"
            >
              <!-- Modal Header -->
              <div class="sticky top-0 bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 p-6 border-b border-blue-700/20">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                      </svg>
                    </div>
                    <div>
                      <h2 class="text-2xl font-bold text-white">Pilih Kategori</h2>
                      <p class="text-blue-100 text-sm mt-0.5">{{ categories?.length || 0 }} kategori tersedia</p>
                    </div>
                  </div>
                  <button
                    @click="showModal = false"
                    class="w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-sm flex items-center justify-center transition-all hover:rotate-90"
                  >
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Modal Content -->
              <div class="overflow-y-auto max-h-[calc(80vh-100px)] custom-scrollbar">
                <div class="p-6">
                  <template v-if="categories && categories.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="`/categories/${category.id}`"
                        @click="showModal = false"
                        class="group p-4 bg-gradient-to-br from-gray-50 to-blue-50 hover:from-blue-50 hover:to-purple-50 rounded-xl border-2 border-gray-200 hover:border-blue-400 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]"
                      >
                        <div class="flex items-center justify-between mb-2">
                          <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                            {{ category.name }}
                          </h3>
                          <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                          </svg>
                        </div>
                        <div class="flex items-center justify-between">
                          <div class="flex items-center text-sm text-gray-600 group-hover:text-blue-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                          <span class="font-medium">{{ category.books_count || 0 }} buku</span>

                          </div>
                          <span class="text-xs px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full font-medium group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            Lihat
                          </span>
                        </div>
                      </Link>
                    </div>
                  </template>
                  <div v-else class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                      <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                      </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-600">Kategori akan muncul di sini setelah ditambahkan.</p>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </PublicLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #2563eb, #7c3aed);
}

/* 3D Book Effect */
.book-cover-container {
  perspective: 1000px;
  width: 110px;
}

.book-3d {
  position: relative;
  width: 110px;
  height: 150px;
  transform-style: preserve-3d;
  transition: transform 0.5s ease;
}

.book-cover-container:hover .book-3d {
  transform: rotateY(-25deg) translateX(10px);
}

.book-front {
  position: absolute;
  width: 110px;
  height: 150px;
  background: white;
  border-radius: 4px;
  overflow: hidden;
  box-shadow:
    2px 2px 8px rgba(0, 0, 0, 0.1),
    4px 4px 15px rgba(0, 0, 0, 0.05);
  transform: translateZ(12px);
}

.book-spine {
  position: absolute;
  width: 24px;
  height: 150px;
  background: linear-gradient(to right,
    rgba(0, 0, 0, 0.15) 0%,
    rgba(0, 0, 0, 0.05) 50%,
    rgba(255, 255, 255, 0.1) 100%
  );
  right: -12px;
  transform: rotateY(90deg);
  transform-origin: left center;
  border-radius: 0 4px 4px 0;
}

/* Responsive */
@media (max-width: 768px) {
  .book-cover-container {
    width: 90px;
  }

  .book-3d,
  .book-front {
    width: 90px;
    height: 130px;
  }

  .book-spine {
    height: 130px;
  }
}
</style>
