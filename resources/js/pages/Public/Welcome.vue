<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps<{
  stats: { books: number; members: number; articles: number }
  latestBooks: {
    id: number
    title: string
    author: string
    description?: string
    cover_url: string | null
    category?: { id: number; name: string } | string
  }[]
  categories: { id: number; name: string }[]
}>()

function formatNumber(n: number) {
  return n.toLocaleString()
}
</script>

<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-10">

      <!-- HERO -->
      <section
        class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-sky-600 to-indigo-700 text-white p-8 md:p-12 shadow-lg"
      >
        <div class="flex flex-col lg:flex-row items-center gap-8">
          <div class="flex-1">
            <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">
              Perpustakaan Digital — Akses Literasi Tanpa Batas
            </h1>
            <p class="mt-4 text-lg md:text-xl max-w-2xl">
              Jelajahi ribuan buku & artikel, pinjam e-book, dan temukan rekomendasi bacaan yang cocok untuk semua umur — semua dari layar kamu.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
              <a
                href="#koleksi"
                class="inline-flex items-center gap-2 bg-white text-sky-700 px-4 py-2 rounded-md font-semibold shadow hover:opacity-95"
              >
                Jelajahi Koleksi
              </a>
              <a
                href="/register"
                class="inline-flex items-center gap-2 bg-transparent border border-white/40 px-4 py-2 rounded-md font-medium hover:bg-white/10"
              >
                Daftar Gratis
              </a>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-4 max-w-sm">
              <div class="text-center">
                <div class="text-2xl font-bold">
                  {{ formatNumber(stats.books) }}
                </div>
                <div class="text-sm opacity-90">Buku</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold">
                  {{ formatNumber(stats.members) }}
                </div>
                <div class="text-sm opacity-90">Anggota</div>
              </div>
              <div class="text-center">
                <!-- Placeholder artikel -->
              </div>
            </div>
          </div>

          <div class="w-full lg:w-1/2">
            <div class="bg-white/10 rounded-xl p-4">
              <img
                src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3"
                alt="perpustakaan"
                class="w-full h-64 object-cover rounded-lg shadow-inner"
              />
              <div class="mt-4 text-sm opacity-90">
                Temukan bacaan yang cocok — dari buku anak, remaja, hingga bacaan umum.
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- GRID -->
      <div class="mt-10 grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- SIDEBAR -->
        <aside
          class="order-2 lg:order-1 lg:col-span-1 bg-white dark:bg-gray-800 rounded-lg p-4 shadow max-h-[400px] overflow-y-auto"
        >
          <h3 class="font-semibold text-lg mb-3">Kategori</h3>
          <ul v-if="categories.length" class="space-y-2">
            <li v-for="cat in categories" :key="cat.id">
              <a
                href="#"
                class="flex items-center justify-between px-3 py-2 rounded hover:bg-sky-50 dark:hover:bg-gray-700 transition"
              >
                <span>{{ cat.name }}</span>
                <span class="text-xs text-gray-400">›</span>
              </a>
            </li>
          </ul>
          <p v-else class="text-gray-500 text-sm italic">
            Belum ada kategori tersedia.
          </p>
        </aside>

        <!-- MAIN -->
        <main class="order-1 lg:order-2 lg:col-span-3">
          <section id="koleksi" class="mb-8">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-2xl font-bold">Buku Baru</h2>
              <a href="#" class="text-sky-600 hover:underline">Lihat Semua →</a>
            </div>

            <div
              v-if="latestBooks.length"
              class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5"
            >
              <Link
                v-for="book in latestBooks"
                :key="book.id"
                :href="route('public.preview', book.id)"
                class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-md transition flex flex-col text-sm"
              >
                <!-- Cover -->
                <div class="relative w-full bg-gray-100 dark:bg-gray-700">
                  <img
                    :src="book.cover_url ?? 'https://via.placeholder.com/150x220'"
                    alt="cover"
                    class="w-full h-44 object-cover"
                  />

                  <!-- Badge kategori -->
                  <span
                    v-if="book.category"
                    class="absolute top-2 left-2 bg-sky-600 text-white text-[10px] px-2 py-1 rounded-full shadow"
                  >
                    {{ typeof book.category === 'object' ? book.category.name : book.category }}
                  </span>
                </div>

                <!-- Info -->
                <div class="p-3 flex-1 flex flex-col">
                  <h3 class="font-semibold line-clamp-2">
                    {{ book.title }}
                  </h3>
                  <p class="text-gray-500 mt-1">
                    {{ book.author }}
                  </p>
                  <p
                    v-if="book.description"
                    class="text-gray-600 dark:text-gray-400 mt-2 line-clamp-3 text-xs"
                  >
                    {{ book.description }}
                  </p>
                </div>
              </Link>
            </div>

            <!-- Fallback -->
            <div v-else class="text-center py-10 text-gray-500">
              Belum ada buku baru.
            </div>
          </section>
        </main>
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
