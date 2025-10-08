<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  cabinet: Object,
  racks: Array
})
</script>

<template>
  <PublicLayout>
    <Head :title="`Rak di ${cabinet.name}`" />

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
        <span class="text-gray-900 font-semibold">Rak</span>
      </nav>

      <!-- Hero Header with Cabinet Info -->
      <div class="relative mb-10 p-8 bg-gradient-to-br from-emerald-600 via-green-600 to-teal-600 rounded-3xl shadow-xl overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl transform translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/10 rounded-full blur-3xl transform -translate-x-28 translate-y-28"></div>
        <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>

        <div class="relative z-10">
          <!-- Back Button -->
          <Link
            :href="route('koleksi', { cabinet: cabinet.id })"
            class="inline-flex items-center space-x-2 mb-4 px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/30 transition-all duration-200 border border-white/30"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Kembali ke Cabinet</span>
          </Link>

          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-3">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                  </svg>
                </div>
                <div>
                  <p class="text-emerald-100 text-sm font-medium">Cabinet</p>
                  <h1 class="text-4xl font-bold text-white">{{ cabinet.name }}</h1>
                </div>
              </div>
              <p class="text-emerald-100 text-lg max-w-2xl">Jelajahi koleksi rak penyimpanan. Setiap rak berisi buku-buku yang tersusun rapi dan terorganisir.</p>
            </div>

            <!-- Cabinet Code Badge -->
            <div v-if="cabinet.code" class="ml-4">
              <div class="relative">
                <div class="absolute inset-0 bg-white rounded-xl blur-lg opacity-50"></div>
                <div class="relative px-5 py-3 bg-white/30 backdrop-blur-xl rounded-xl border border-white/40 shadow-xl">
                  <p class="text-emerald-100 text-xs font-medium mb-1">Kode Cabinet</p>
                  <p class="text-2xl font-bold text-white tracking-wider">{{ cabinet.code }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Row -->
          <div class="mt-6 flex flex-wrap gap-4">
            <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
              </svg>
              <span class="text-white font-semibold">{{ racks.length }} Rak</span>
            </div>
            <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span class="text-white font-semibold">{{ racks.reduce((sum, rack) => sum + rack.books_count, 0) }} Total Buku</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Racks Grid -->
      <div
        v-if="racks.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <div
          v-for="rack in racks"
          :key="rack.id"
          class="group relative bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300"
        >
          <!-- Badge Code - Top Right Corner -->
          <div class="absolute top-4 right-4 z-20">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition-opacity"></div>
              <div class="relative px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow-lg">
                <span class="text-xs font-bold text-white tracking-wider">
                  {{ rack.code || 'UNKNOWN' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Hover Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/80 via-green-50/80 to-teal-50/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

          <!-- Decorative Shelf Lines -->
          <div class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
            <div class="absolute top-1/4 left-0 right-0 h-px bg-emerald-600"></div>
            <div class="absolute top-2/4 left-0 right-0 h-px bg-emerald-600"></div>
            <div class="absolute top-3/4 left-0 right-0 h-px bg-emerald-600"></div>
          </div>

          <!-- Card Content -->
          <div class="relative p-6">
            <!-- Rack Icon -->
            <div class="w-16 h-16 mb-4 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
              </svg>
            </div>

            <!-- Rack Name -->
            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-emerald-700 transition-colors duration-300 pr-20">
              {{ rack.name }}
            </h2>

            <!-- Divider Line -->
            <div class="h-px bg-gradient-to-r from-emerald-200 via-green-200 to-teal-200 mb-4"></div>

            <!-- Books Count Info -->
            <div class="mb-5">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600 font-medium">Total Buku</span>
                <div class="flex items-center space-x-1.5">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  <span class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    {{ rack.books_count }}
                  </span>
                </div>
              </div>

              <!-- Book Stack Visualization -->
              <div class="flex items-center space-x-1 mb-2">
                <div
                  v-for="i in Math.min(rack.books_count, 5)"
                  :key="i"
                  class="flex-1 h-2 rounded-full transition-all duration-300"
                  :class="[
                    i <= 1 ? 'bg-emerald-500' : '',
                    i === 2 ? 'bg-emerald-400' : '',
                    i === 3 ? 'bg-green-400' : '',
                    i === 4 ? 'bg-teal-400' : '',
                    i === 5 ? 'bg-teal-300' : ''
                  ]"
                  :style="{ transform: `scaleY(${1 + i * 0.2})` }"
                ></div>
                <span v-if="rack.books_count > 5" class="text-xs text-gray-500 font-semibold">+{{ rack.books_count - 5 }}</span>
              </div>

              <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 flex items-center">
                  <svg class="w-3.5 h-3.5 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Tersedia
                </p>
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                  <span class="text-xs text-emerald-600 font-semibold">Online</span>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <Link
              :href="route('koleksi', { rack: rack.id })"
              class="group/btn relative w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 overflow-hidden"
            >
              <!-- Animated Background -->
              <div class="absolute inset-0 bg-gradient-to-r from-teal-600 via-emerald-600 to-green-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>

              <!-- Button Content -->
              <span class="relative z-10 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Lihat Buku
                <svg class="w-5 h-5 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </span>

              <!-- Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 transform -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700"></div>
            </Link>
          </div>

          <!-- Animated Bottom Border -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-green-500 to-teal-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20 bg-gradient-to-br from-gray-50 to-emerald-50 rounded-3xl border-2 border-dashed border-gray-300">
        <div class="relative inline-block mb-6">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl blur-xl opacity-20"></div>
          <div class="relative w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center">
            <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
            </svg>
          </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Rak</h3>
        <p class="text-gray-600 text-lg max-w-md mx-auto mb-6">Tambahkan rak baru ke cabinet <span class="font-semibold text-emerald-600">{{ cabinet.name }}</span> untuk mulai menyimpan koleksi buku Anda.</p>
        <Link
          :href="route('koleksi', { cabinet: cabinet.id })"
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Cabinet
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
</style>
