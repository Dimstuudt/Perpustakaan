<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ref } from 'vue'

// Dummy data
const articles = ref([
  { id: 1, title: 'Belajar Vue 3', excerpt: 'Panduan lengkap Vue 3 Composition API.', image: 'https://via.placeholder.com/400x200' },
  { id: 2, title: 'Laravel & Inertia', excerpt: 'Membangun SPA dengan Laravel + Inertia.', image: 'https://via.placeholder.com/400x200' },
  { id: 3, title: 'Tailwind CSS Tips', excerpt: 'Membuat layout modern dengan Tailwind.', image: 'https://via.placeholder.com/400x200' },
  { id: 4, title: 'Database Design', excerpt: 'Cara mendesain database untuk perpustakaan.', image: 'https://via.placeholder.com/400x200' },
  { id: 5, title: 'Soft Deletes', excerpt: 'Mengelola data yang bisa dihapus sementara.', image: 'https://via.placeholder.com/400x200' },
  { id: 6, title: 'CRUD Buku', excerpt: 'Membuat CRUD buku lengkap dengan kategori.', image: 'https://via.placeholder.com/400x200' },
])

const categories = ref([
  { id: 1, name: 'Teknologi' },
  { id: 2, name: 'Sains' },
  { id: 3, name: 'Fiksi' },
  { id: 4, name: 'Sejarah' },
  { id: 5, name: 'Bahasa' },
])
</script>

<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row gap-8">

      <!-- Sidebar -->
      <aside class="w-full md:w-1/4 bg-white dark:bg-gray-700 rounded-lg shadow p-4">
        <h2 class="text-xl font-bold mb-4">Kategori</h2>
        <ul class="space-y-2">
          <li v-for="cat in categories" :key="cat.id">
            <a href="#" class="block px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-600 transition">
              {{ cat.name }}
            </a>
          </li>
        </ul>

        <!-- Quick Links -->
        <h2 class="text-xl font-bold mt-8 mb-4">Menu</h2>
        <ul class="space-y-2">
          <li><a href="#" class="block px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-600 transition">Home</a></li>
          <li><a href="#" class="block px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-600 transition">Buku Baru</a></li>
          <li><a href="#" class="block px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-600 transition">Artikel</a></li>
          <li><a href="#" class="block px-3 py-2 rounded hover:bg-blue-100 dark:hover:bg-blue-600 transition">Tentang</a></li>
        </ul>
      </aside>

      <!-- Main Content -->
      <main class="flex-1">
        <!-- Hero -->
        <section class="bg-blue-600 dark:bg-gray-800 text-white py-12 px-6 rounded-lg mb-8 text-center">
          <h1 class="text-3xl md:text-4xl font-bold mb-2">Selamat Datang di Perpustakaan</h1>
          <p class="text-lg md:text-xl max-w-2xl mx-auto">Akses buku, artikel, dan referensi berkualitas dengan mudah.</p>
        </section>

        <!-- Articles Grid -->
        <section>
          <h2 class="text-2xl font-bold mb-6">Artikel Terbaru</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="article in articles" :key="article.id" class="bg-white dark:bg-gray-700 rounded-lg shadow hover:shadow-lg transition overflow-hidden">
              <img :src="article.image" alt="Thumbnail" class="w-full h-40 object-cover"/>
              <div class="p-4">
                <h3 class="font-semibold text-lg mb-2 hover:text-blue-600 dark:hover:text-blue-400 transition">{{ article.title }}</h3>
                <p class="text-gray-700 dark:text-gray-200 text-sm">{{ article.excerpt }}</p>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </PublicLayout>
</template>

<style scoped>
</style>
