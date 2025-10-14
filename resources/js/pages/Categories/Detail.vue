<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  category: Object,
  books: Object
})
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
            <p class="text-blue-100 text-base max-w-2xl">Temukan semua buku dalam kategori <span class="font-semibold">{{ category.name }}</span>.</p>
          </div>

          <Link
            href="/categories"
            class="px-5 py-2.5 bg-white/20 backdrop-blur-md hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 flex items-center space-x-2 border border-white/30 hover:scale-105 active:scale-95"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali</span>
          </Link>
        </div>
      </div>

      <!-- Grid Buku -->
      <div v-if="books.data.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <div
          v-for="book in books.data"
          :key="book.id"
          class="group bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden"
        >
          <Link :href="`/book/${book.id}`" class="block">
            <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden">
              <img
                :src="book.cover_url || '/images/default-book.png'"
                :alt="book.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 line-clamp-2 mb-1">
                {{ book.title }}
              </h3>
              <p class="text-xs text-gray-500">{{ book.author || 'Tidak diketahui' }}</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Jika Tidak Ada Buku -->
      <div v-else class="text-center py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border border-gray-200">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
          <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <p class="text-gray-600">Belum ada buku dalam kategori ini.</p>
      </div>

      <!-- Pagination -->
      <div v-if="books.links.length > 3" class="mt-12 flex justify-center gap-2 flex-wrap">
        <Link
          v-for="link in books.links"
          :key="link.label"
          :href="link.url"
          v-html="link.label"
          class="px-3 py-1.5 rounded-md text-sm"
          :class="{
            'bg-indigo-600 text-white': link.active,
            'text-gray-600 hover:bg-gray-200': !link.active && link.url
          }"
        />
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
