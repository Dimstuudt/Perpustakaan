<script setup lang="ts">
import { ref, watch } from "vue"
import { Link, usePage } from "@inertiajs/vue3"

// ===== User =====
const page = usePage()
const user = page.props.auth?.user || null

// ===== Dark Mode =====
const isDark = ref(localStorage.getItem("theme") === "dark")
if (isDark.value) document.documentElement.classList.add("dark")
watch(isDark, (val) => {
  if (val) {
    document.documentElement.classList.add("dark")
    localStorage.setItem("theme", "dark")
  } else {
    document.documentElement.classList.remove("dark")
    localStorage.setItem("theme", "light")
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors duration-300 font-sans">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white dark:bg-gray-800 shadow-md px-6 py-3 flex flex-wrap justify-between items-center">
      <!-- Logo -->
      <Link href="/" class="text-2xl font-bold text-blue-700 dark:text-blue-400">📚 Perpus</Link>

      <!-- Menu -->
      <div class="flex items-center space-x-4 md:space-x-6 mt-2 md:mt-0">
        <Link href="/" class="text-gray-700 dark:text-gray-200 hover:text-blue-500 transition font-medium">Home</Link>
        <Link href="/books" class="text-gray-700 dark:text-gray-200 hover:text-blue-500 transition font-medium">Books</Link>
        <Link href="/categories" class="text-gray-700 dark:text-gray-200 hover:text-blue-500 transition font-medium">Categories</Link>
        <Link href="/about" class="text-gray-700 dark:text-gray-200 hover:text-blue-500 transition font-medium">About</Link>
      </div>

      <!-- Right -->
      <div class="flex items-center space-x-3 mt-2 md:mt-0">
        <!-- Dark Mode Toggle -->
        <button @click="isDark = !isDark"
          class="relative h-6 w-14 rounded-full bg-gray-300 dark:bg-gray-600 p-1 flex items-center transition-colors duration-300 focus:outline-none">
          <span class="absolute left-1 h-2.5 w-2.5 bg-yellow-400 rounded-full transition-opacity duration-300"
            :class="isDark ? 'opacity-0' : 'opacity-100'"></span>
          <span class="absolute right-1 h-2.5 w-2.5 bg-gray-800 rounded-full transition-opacity duration-300"
            :class="isDark ? 'opacity-100' : 'opacity-0'"></span>
          <span class="inline-block h-5 w-5 rounded-full shadow-md transform transition-all duration-300 ease-in-out cursor-pointer"
            :class="[isDark ? 'translate-x-8 bg-gray-800' : 'translate-x-0 bg-yellow-400']"></span>
        </button>

        <!-- Auth -->
        <template v-if="user">
          <Link href="/dashboard" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow transition">Dashboard</Link>
        </template>
        <template v-else>
          <Link href="/login" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow transition">Login / Register</Link>
        </template>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow text-gray-900 dark:text-gray-100 transition-colors duration-300">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 mt-12">
      <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div>
          <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-2">📚 Perpus</h2>
          <p class="text-sm text-gray-600 dark:text-gray-300">Platform perpustakaan online untuk membaca artikel, buku, dan berita terbaru.</p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="font-semibold mb-4">Navigasi</h3>
          <ul class="space-y-2">
            <li><Link href="/" class="hover:text-blue-500 transition">Home</Link></li>
            <li><Link href="/books" class="hover:text-blue-500 transition">Books</Link></li>
            <li><Link href="/categories" class="hover:text-blue-500 transition">Categories</Link></li>
            <li><Link href="/about" class="hover:text-blue-500 transition">About</Link></li>
          </ul>
        </div>

        <!-- Social Media -->
        <div>
          <h3 class="font-semibold mb-4">Follow Us</h3>
          <div class="flex space-x-4">
            <a href="#" class="hover:text-blue-500 transition">Facebook</a>
            <a href="#" class="hover:text-blue-500 transition">Instagram</a>
            <a href="#" class="hover:text-blue-500 transition">Twitter</a>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-300 dark:border-gray-600 text-center py-4 text-xs text-gray-500 dark:text-gray-400">
        &copy; 2025 Perpus. All rights reserved.
      </div>
    </footer>

  </div>
</template>

<style scoped>
</style>
