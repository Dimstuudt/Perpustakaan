<script setup lang="ts">
import { ref, watch } from "vue"
import { Link, usePage } from "@inertiajs/vue3"
import WelcomePopup from "@/Components/WelcomePopup.vue"

// ===== User =====
const page = usePage()
const user = page.props.auth?.user || null

// ===== Mobile Menu =====
const mobileMenuOpen = ref(false)

// ===== User Dropdown =====
const userDropdownOpen = ref(false)

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

// Get user initials for avatar
const getUserInitials = (name: string) => {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 via-blue-50/30 to-indigo-50/30 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 transition-colors duration-300">

    <!-- Enhanced Navbar -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl shadow-lg border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

          <!-- Logo -->
          <Link href="/welcome" class="flex items-center space-x-3 group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl blur opacity-50 group-hover:opacity-75 transition-opacity"></div>
              <div class="relative w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
            </div>
            <div>
              <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Perpus</span>
              <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Digital Library</p>
            </div>
          </Link>

          <!-- Desktop Menu -->
          <div class="hidden md:flex items-center space-x-1">
            <Link
              href="/welcome"
              class="group px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span class="font-medium">Home</span>
            </Link>
            <Link
              href="/koleksi"
              class="group px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span class="font-medium">Books</span>
            </Link>
            <Link
              href="/categoried"
              class="group px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <span class="font-medium">Categories</span>
            </Link>
            <Link
              href="/about"
              class="group px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-medium">About</span>
            </Link>
          </div>

          <!-- Right Section -->
          <div class="flex items-center space-x-3">

            <!-- Dark Mode Toggle -->
            <button
              @click="isDark = !isDark"
              class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 group"
            >
              <svg v-if="!isDark" class="w-5 h-5 text-yellow-500 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else class="w-5 h-5 text-indigo-400 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <!-- User Section -->
            <template v-if="user">
              <div class="relative">
                <button
                  @click="userDropdownOpen = !userDropdownOpen"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 group"
                >
                  <!-- Avatar -->
                  <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-50 group-hover:opacity-75 transition-opacity"></div>
<div class="relative w-9 h-9 rounded-full overflow-hidden shadow-lg">
  <img
    v-if="user.avatar"
    :src="user.avatar"
    alt="Avatar"
    class="w-full h-full object-cover"
  />
  <div
    v-else
    class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm"
  >
    {{ getUserInitials(user.name) }}
  </div>
</div>

                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-900"></div>
                  </div>
                  <!-- Username -->
                  <div class="hidden lg:block text-left">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                  </div>
                  <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': userDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                  v-if="userDropdownOpen"
                  @click="userDropdownOpen = false"
                  class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50"
                >
                  <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
                  </div>

                  <Link href="/dashboard" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                  </Link>

                  <Link href="/settings/profile" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profile</span>
                  </Link>

                  <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                  <Link href="/logout" method="post" as="button" class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                  </Link>
                </div>
              </div>
            </template>

            <!-- Guest Buttons -->
            <template v-else>
              <Link
                href="/login"
                class="hidden md:inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login
              </Link>
            </template>

            <!-- Mobile Menu Button -->
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            >
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden py-4 space-y-2 border-t border-gray-200 dark:border-gray-700">
          <Link href="/welcome" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">Home</Link>
          <Link href="/koleksi" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">Books</Link>
          <Link href="/categoried" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">Categories</Link>
          <Link href="/about" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors">About</Link>
          <Link v-if="!user" href="/login" class="block px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-center font-semibold">Login / Register</Link>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
      <WelcomePopup />
    </main>

    <!-- Premium Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

          <!-- Brand -->
          <div class="md:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Perpus</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Digital Library</p>
              </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md">Platform perpustakaan digital modern untuk membaca, meminjam, dan mengelola koleksi buku dengan mudah dan efisien.</p>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Navigasi</h3>
            <ul class="space-y-2 text-sm">
              <li><Link href="/welcome" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</Link></li>
              <li><Link href="/koleksi" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Books</Link></li>
              <li><Link href="/categoried" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Categories</Link></li>
              <li><Link href="/about" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</Link></li>
            </ul>
          </div>

          <!-- Social -->
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Follow Us</h3>
            <div class="flex space-x-3">
              <a href="" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all duration-300 group">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>
              <a href="https://www.instagram.com/sndyfrds._" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center hover:bg-pink-500 hover:text-white transition-all duration-300 group">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
              </a>
<!-- TikTok -->
<a href="https://www.tiktok.com/@dimstuud" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center hover:bg-black hover:text-white transition-all duration-300 group">
  <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-white" viewBox="0 0 24 24" fill="currentColor">
    <path d="M12.9 2c.3 1.9 1.7 3.4 3.6 3.7v2.4c-1.1 0-2.2-.3-3.1-.9v6.5c0 3-2.4 5.3-5.3 5.3S2.8 16.7 2.8 13.8s2.4-5.3 5.3-5.3c.5 0 1 .1 1.4.2v2.5c-.4-.2-.9-.3-1.4-.3-1.5 0-2.8 1.2-2.8 2.8s1.2 2.8 2.8 2.8 2.8-1.2 2.8-2.8V2h2z"/>
  </svg>
</a>

            </div>
          </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-800 mt-8 pt-8 text-center">
          <p class="text-sm text-gray-500 dark:text-gray-400">&copy; 2025 Perpus Digital Library. All rights reserved.</p>
        </div>
      </div>
    </footer>

  </div>
</template>

<style scoped>
/* Smooth transitions for dropdown */
</style>
