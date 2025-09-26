<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ref } from 'vue'

// Dummy data — ganti dengan data nyata dari API/Inertia
const stats = ref([
  { id: 1, label: 'Buku', value: 1248 },
  { id: 2, label: 'Anggota', value: 392 },
  { id: 3, label: 'Artikel', value: 86 },
])

const books = ref([
  { id: 1, title: 'Algoritma Pemrograman', author: 'Budi Santoso', cover: 'https://via.placeholder.com/150x220' },
  { id: 2, title: 'Fisika Modern', author: 'Ani Wijaya', cover: 'https://via.placeholder.com/150x220' },
  { id: 3, title: 'Sejarah Nusantara', author: 'Cahyo Prasetyo', cover: 'https://via.placeholder.com/150x220' },
  { id: 4, title: 'Belajar Vue 3', author: 'Sandy Dev', cover: 'https://via.placeholder.com/150x220' },
])


const categories = ref([
  { id: 1, name: 'Teknologi' },
  { id: 2, name: 'Sains' },
  { id: 3, name: 'Fiksi' },
  { id: 4, name: 'Sejarah' },
  { id: 5, name: 'Bahasa' },
])

// Helpers
function formatNumber(n: number) {
  return n.toLocaleString()
}
</script>

<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-10">
      <!-- NAV / Breadcrumb compact (can replace with real navbar in PublicLayout) -->

      <!-- HERO -->
      <section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-sky-600 to-indigo-700 text-white p-8 md:p-12 shadow-lg">
        <div class="flex flex-col lg:flex-row items-center gap-8">
          <div class="flex-1">
            <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">Perpustakaan Digital — Akses Literasi Tanpa Batas</h1>
            <p class="mt-4 text-lg md:text-xl max-w-2xl">Jelajahi ribuan buku & artikel, pinjam e-book, dan temukan rekomendasi bacaan yang cocok untuk semua umur — semua dari layar kamu.</p>

            <div class="mt-6 flex flex-wrap gap-3">
              <a href="#koleksi" class="inline-flex items-center gap-2 bg-white text-sky-700 px-4 py-2 rounded-md font-semibold shadow hover:opacity-95">Jelajahi Koleksi</a>
              <a href="#daftar" class="inline-flex items-center gap-2 bg-transparent border border-white/40 px-4 py-2 rounded-md font-medium hover:bg-white/10">Daftar Gratis</a>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-4 max-w-sm">
              <div v-for="s in stats" :key="s.id" class="text-center">
                <div class="text-2xl font-bold">{{ formatNumber(s.value) }}</div>
                <div class="text-sm opacity-90">{{ s.label }}</div>
              </div>
            </div>
          </div>

          <div class="w-full lg:w-1/2">
            <div class="bg-white/10 rounded-xl p-4">
              <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=6a4a1b3a3c3b9d1f8a4f6be6b8d7a1c7" alt="perpustakaan" class="w-full h-64 object-cover rounded-lg shadow-inner"/>
              <div class="mt-4 text-sm opacity-90">Temukan bacaan yang cocok — dari buku anak, remaja, hingga bacaan umum.</div>
            </div>
          </div>
        </div>

        <!-- subtle decoration -->
        <svg class="absolute -right-10 -bottom-10 opacity-10 w-64 h-64" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="g" x1="0" x2="1">
              <stop stop-color="#ffffff" stop-opacity="0.6" offset="0"/>
              <stop stop-color="#ffffff" stop-opacity="0.2" offset="1"/>
            </linearGradient>
          </defs>
          <circle cx="100" cy="100" r="80" fill="url(#g)" />
        </svg>
      </section>

      <div class="mt-10 grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- SIDEBAR -->
        <aside class="order-2 lg:order-1 lg:col-span-1 bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
          <h3 class="font-semibold text-lg mb-3">Kategori</h3>
          <ul class="space-y-2">
            <li v-for="cat in categories" :key="cat.id">
              <a href="#" class="flex items-center justify-between px-3 py-2 rounded hover:bg-sky-50 dark:hover:bg-gray-700 transition">
                <span>{{ cat.name }}</span>
                <span class="text-xs text-gray-400">›</span>
              </a>
            </li>
          </ul>

          <div class="mt-6">
            <h4 class="font-medium mb-2">Menu</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="block px-3 py-2 rounded hover:bg-sky-50 dark:hover:bg-gray-700">Home</a></li>
              <li><a href="#" class="block px-3 py-2 rounded hover:bg-sky-50 dark:hover:bg-gray-700">Buku Baru</a></li>
              <li><a href="#" class="block px-3 py-2 rounded hover:bg-sky-50 dark:hover:bg-gray-700">Tentang</a></li>
            </ul>
          </div>
        </aside>

        <!-- MAIN -->
        <main class="order-1 lg:order-2 lg:col-span-3">
          <!-- Buku Baru -->
          <section id="koleksi" class="mb-8">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-2xl font-bold">Buku Baru</h2>
              <a href="#" class="text-sky-600 hover:underline">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
              <div v-for="book in books" :key="book.id" class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                <img :src="book.cover" alt="cover" class="w-full h-48 object-cover"/>
                <div class="p-3">
                  <h3 class="font-semibold text-sm line-clamp-2">{{ book.title }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ book.author }}</p>
                </div>
              </div>
            </div>
          </section>

          

          <!-- Statistik + CTA -->
          <section class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex gap-6">
              <div v-for="s in stats" :key="s.id" class="text-center">
                <div class="text-2xl font-bold">{{ formatNumber(s.value) }}</div>
                <div class="text-sm text-gray-500">{{ s.label }}</div>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-600 dark:text-gray-300">Gabung jadi anggota gratis dan mulai akses koleksi digital kami.</p>
              <div class="mt-3 flex gap-3 justify-end">
                <a href="#daftar" class="px-4 py-2 bg-sky-600 text-white rounded-md">Daftar Sekarang</a>
                <a href="#" class="px-4 py-2 border rounded-md">Pelajari Lebih Lanjut</a>
              </div>
            </div>
          </section>
        </main>
      </div>

    </div>
  </PublicLayout>
</template>

<style scoped>
/* small helpers */
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>
