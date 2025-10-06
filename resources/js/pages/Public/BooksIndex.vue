<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps<{
  books: any,
  categories: { id: number; name: string }[],
  currentCategory: number | null
}>()
</script>

<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-10">

      <!-- Kategori -->
      <div class="mb-6 flex gap-3 overflow-x-auto">
        <button
          :class="{'bg-blue-600 text-white': !currentCategory, 'bg-gray-100 dark:bg-gray-800': currentCategory}"
          class="px-3 py-1 rounded-full whitespace-nowrap"
          @click="$inertia.get(route('public.koleksi'))"
        >
          Semua
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          :class="{'bg-blue-600 text-white': currentCategory == cat.id, 'bg-gray-100 dark:bg-gray-800': currentCategory != cat.id}"
          class="px-3 py-1 rounded-full whitespace-nowrap"
          @click="$inertia.get(route('public.koleksi', { category: cat.id }))"
        >
          {{ cat.name }}
        </button>
      </div>

      <!-- Koleksi Buku -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        <Link
          v-for="book in books.data"
          :key="book.id"
          :href="route('public.preview', book.id)"
          class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
        >
          <!-- Cover -->
          <div class="aspect-[3/4] overflow-hidden">
            <img
              :src="book.cover_url ?? 'https://via.placeholder.com/300x400/6366f1/ffffff?text=No+Cover'"
              alt="cover"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <!-- Kategori badge -->
            <span v-if="book.category"
              class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow">
              {{ book.category.name }}
            </span>
          </div>

          <!-- Info -->
          <div class="p-3 flex flex-col gap-1">
            <h3 class="font-semibold text-sm line-clamp-2 group-hover:text-blue-600 transition-colors">{{ book.title }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ book.author }}</p>
            <p v-if="book.publisher" class="text-xs text-gray-400 dark:text-gray-500 line-clamp-1">{{ book.publisher }}</p>
          </div>

          <!-- Hover overlay -->
          <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-20 transition-opacity rounded-xl"></div>
        </Link>
      </div>

      <!-- Pagination -->
      <div class="mt-8 flex justify-center gap-2">
        <button
          v-if="books.prev_page_url"
          @click="$inertia.get(books.prev_page_url)"
          class="px-3 py-1 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
        >
          Prev
        </button>
        <span class="px-3 py-1 border rounded bg-gray-200 dark:bg-gray-700">{{ books.current_page }}</span>
        <button
          v-if="books.next_page_url"
          @click="$inertia.get(books.next_page_url)"
          class="px-3 py-1 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
        >
          Next
        </button>
      </div>

    </div>
  </PublicLayout>
</template>
