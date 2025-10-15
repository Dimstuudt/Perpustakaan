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
                <span class="text-white font-semibold">{{ books.data?.length || 0 }} Buku</span>
              </div>
            </div>
          </div>
        </div>
      </div>

     <!-- Books Grid with 3D Effect - Fixed Layout -->
<div v-if="books.data && books.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
  <Link
    v-for="book in books.data"
    :key="book.id"
    :href="route('public.preview', { id: book.id })"
    class="group block book-card"
  >
    <!-- 3D Book Container -->
    <div class="book-3d-container">
      <div class="book-3d">
        <!-- Front Cover -->
        <div class="book-front">
          <div class="relative w-full h-full">
            <img
              v-if="book.cover_url"
              :src="book.cover_url"
              :alt="book.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-400 to-orange-500">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>

            <!-- Gradient Overlay on Hover -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Quick View Badge -->
            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 shadow-lg">
              <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>

            <!-- Location Badge -->
            <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
              <div class="bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center justify-center gap-1 shadow-lg">
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ rack.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Book Spine -->
        <div class="book-spine"></div>

        <!-- Book Shadow -->
        <div class="book-shadow"></div>
      </div>
    </div>

    <!-- Book Info - Fixed Height Container -->
    <div class="mt-4 px-1 flex flex-col" style="min-height: 88px;">
      <!-- Title - Fixed 2 lines (40px) -->
      <h3 class="text-sm font-bold text-gray-800 group-hover:text-amber-600 transition-colors line-clamp-2 mb-1.5 leading-snug" style="height: 40px; min-height: 40px;">
        {{ book.title }}
      </h3>

      <!-- Author - Fixed 1 line (20px) -->
      <p class="text-xs text-gray-500 line-clamp-1 mb-2" style="height: 20px; min-height: 20px;">
        {{ book.author || 'Tidak diketahui' }}
      </p>

      <!-- Bottom Info - Fixed height (28px) -->
      <div class="flex items-center justify-between gap-2 mt-auto" style="height: 28px; min-height: 28px;">
        <!-- Category Badge - Always same size -->
        <div class="flex-1 min-w-0">
          <span v-if="book.category" class="inline-flex items-center text-xs bg-amber-50 text-amber-700 px-2 py-1 rounded-full font-medium w-full max-w-full">
            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span class="truncate">
              {{ typeof book.category === 'object' ? book.category.name : book.category }}
            </span>
          </span>
          <span v-else class="inline-block text-xs text-gray-400 px-2 py-1">-</span>
        </div>

        <!-- Star Icon - Fixed position -->
        <div class="flex items-center gap-0.5 text-amber-400 flex-shrink-0" style="width: 16px;">
          <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        </div>
      </div>
    </div>
  </Link>
</div>


      <!-- Pagination -->
      <div v-if="books.links && books.links.length > 3" class="mt-12 flex justify-center gap-2 flex-wrap">
        <Link
          v-for="link in books.links"
          :key="link.label"
          :href="link.url"
          v-html="link.label"
          :class="{
            'bg-gradient-to-r from-amber-600 to-rose-600 text-white shadow-lg shadow-amber-500/30': link.active,
            'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200': !link.active && link.url,
            'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
          }"
          class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 active:scale-95"
        />
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
