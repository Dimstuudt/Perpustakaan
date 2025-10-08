<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  cabinet: Object,
  rack: Object,
  books: Object,
  categories: Array
})
</script>

<template>
  <PublicLayout>
    <Head :title="`Buku di ${rack.name}`" />

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Enhanced Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm mb-6">
        <Link href="/" class="flex items-center text-gray-500 hover:text-blue-600 transition-colors duration-200 group">
          <svg class="w-4 h-4 mr-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Home
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <Link href="/koleksi" class="text-gray-500 hover:text-blue-600 transition-colors duration-200">
          Cabinet
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <Link :href="route('koleksi', { cabinet: cabinet.id })" class="text-gray-500 hover:text-blue-600 transition-colors duration-200">
          {{ cabinet.name }}
        </Link>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-900 font-semibold">{{ rack.name }}</span>
      </nav>

      <!-- Hero Header -->
      <div class="relative mb-10 p-8 bg-gradient-to-br from-amber-600 via-orange-600 to-rose-600 rounded-3xl shadow-xl overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl transform translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/10 rounded-full blur-3xl transform -translate-x-28 translate-y-28"></div>

        <div class="relative z-10">
          <!-- Back Button -->
          <Link
            :href="route('koleksi', { cabinet: cabinet.id })"
            class="inline-flex items-center space-x-2 mb-4 px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/30 transition-all duration-200 border border-white/30"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Kembali ke Rak</span>
          </Link>

          <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-3">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <div>
                  <p class="text-amber-100 text-sm font-medium">{{ cabinet.name }} / {{ rack.name }}</p>
                  <h1 class="text-4xl font-bold text-white">Koleksi Buku</h1>
                </div>
              </div>
              <p class="text-amber-100 text-lg max-w-2xl">Jelajahi koleksi buku yang tersimpan rapi di rak ini. Klik untuk melihat detail lengkap setiap buku.</p>
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap gap-3">
              <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-white font-semibold">{{ books.data.length }} Buku</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Books Grid -->
      <div
        v-if="books.data && books.data.length > 0"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6"
      >
        <Link
          v-for="book in books.data"
          :key="book.id"
          :href="route('public.preview', { id: book.id })"
          class="group relative cursor-pointer block"
        >
          <!-- Book Card with 3D Effect -->
          <div class="relative">
            <!-- Book Cover Container -->
            <div class="relative transform transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-2">
              <!-- Book Spine Shadow -->
              <div class="absolute -left-2 top-2 bottom-2 w-2 bg-gradient-to-r from-gray-800 to-gray-700 rounded-l-sm opacity-60"></div>

              <!-- Main Book Cover -->
              <div class="relative bg-white rounded-lg shadow-xl overflow-hidden group-hover:shadow-2xl transition-shadow duration-500 border-2 border-gray-200">
                <!-- Cover Image -->
                <div class="relative aspect-[2/3] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                  <img
                    v-if="book.cover_url"
                    :src="book.cover_url"
                    :alt="book.title"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-200 to-orange-300">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>

                  <!-- Overlay on Hover -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                      <div class="flex items-center justify-center space-x-2 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="text-sm font-semibold">Lihat Detail</span>
                      </div>
                    </div>
                  </div>

                  <!-- Shine Effect -->
                  <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-transparent opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                </div>

                <!-- Book Pages Effect -->
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-gradient-to-b from-gray-300 via-gray-200 to-gray-300">
                  <div class="absolute inset-y-0 right-0 w-px bg-white/50"></div>
                </div>
              </div>

              <!-- Bottom Shadow -->
              <div class="absolute -bottom-1 left-0 right-0 h-3 bg-gradient-to-b from-black/20 to-transparent blur-sm transform scale-95 group-hover:scale-100 transition-transform duration-500"></div>
            </div>
          </div>

          <!-- Book Info -->
          <div class="mt-4 space-y-1">
            <h3 class="font-bold text-gray-900 line-clamp-2 text-sm leading-tight group-hover:text-amber-600 transition-colors duration-300">
              {{ book.title }}
            </h3>
            <p class="text-xs text-gray-600 truncate">
              {{ book.author }}
            </p>
          </div>

          <!-- Hover Indicator -->
          <div class="absolute -bottom-1 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left rounded-full"></div>
        </Link>
      </div>

      <!-- Pagination (if needed) -->
      <div v-if="books.links && books.links.length > 3" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <template v-for="(link, index) in books.links" :key="index">
            <Link
              v-if="link.url"
              :href="link.url"
              class="px-4 py-2 rounded-lg transition-all duration-200"
              :class="[
                link.active
                  ? 'bg-gradient-to-r from-amber-600 to-rose-600 text-white font-semibold shadow-lg'
                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
              ]"
              v-html="link.label"
            />
            <span
              v-else
              class="px-4 py-2 text-gray-400 cursor-not-allowed"
              v-html="link.label"
            />
          </template>
        </nav>
      </div>

      <!-- Empty State -->
      <div v-if="!books.data || books.data.length === 0" class="text-center py-20 bg-gradient-to-br from-gray-50 to-amber-50 rounded-3xl border-2 border-dashed border-gray-300">
        <div class="relative inline-block mb-6">
          <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-rose-600 rounded-2xl blur-xl opacity-20"></div>
          <div class="relative w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center">
            <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Buku</h3>
        <p class="text-gray-600 text-lg max-w-md mx-auto mb-6">Rak <span class="font-semibold text-amber-600">{{ rack.name }}</span> masih kosong. Tambahkan buku untuk mulai membangun koleksi Anda.</p>
        <Link
          :href="route('koleksi', { cabinet: cabinet.id })"
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-rose-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Rak
        </Link>
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
