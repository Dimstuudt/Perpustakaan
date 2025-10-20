<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'

const revealMap = ref({})
let observer = null

const setupObserver = () => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const id = entry.target.getAttribute('data-reveal-id')
      if (!id) return
      if (entry.isIntersecting) {
        revealMap.value[id] = true
      }
    })
  }, { threshold: 0.18 })

  const nodes = document.querySelectorAll('[data-reveal-id]')
  nodes.forEach((n) => observer.observe(n))
}

const scrollTo = (id) => {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const currentSlide = ref(0)
const carouselImages = [
  'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1000&h=600&fit=crop',
  'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1000&h=600&fit=crop',
  'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1000&h=600&fit=crop',
  'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=1000&h=600&fit=crop',

]

let carouselInterval = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % carouselImages.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + carouselImages.length) % carouselImages.length
}

const goToSlide = (idx) => {
  currentSlide.value = idx
}

onMounted(() => {
  setupObserver()
  carouselInterval = setInterval(() => {
    nextSlide()
  }, 5000)
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
  if (carouselInterval) clearInterval(carouselInterval)
})
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Tentang Perpustakaan - Perpustakaan Digital</title>
    </Head>

    <!-- HERO DENGAN CAROUSEL -->
    <section class="relative overflow-hidden py-16 px-6 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 dark:from-blue-900 dark:via-blue-950 dark:to-indigo-950">
      <div class="grid lg:grid-cols-2 gap-8 items-center max-w-6xl mx-auto">
        <!-- TEXT -->
        <div class="text-white z-10">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-semibold">Tentang Kami</span>
          </div>
          <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">Perpustakaan Digital Terpercaya</h1>
          <p class="text-lg text-blue-50 mb-8">Menyediakan akses mudah ke koleksi buku berkualitas dengan layanan terbaik untuk semua.</p>

          <div class="flex flex-col sm:flex-row gap-4">
            <Link href="/register" class="px-6 py-3 rounded-lg font-semibold bg-white hover:bg-gray-50 text-blue-600 transition hover:scale-105 dark:bg-gray-100 text-center">Daftar Sekarang</Link>
            <button @click="scrollTo('fasilitas')" class="px-6 py-3 rounded-lg bg-white/15 hover:bg-white/25 border border-white/30 text-white transition">Pelajari Lebih</button>
          </div>
        </div>

        <!-- CAROUSEL -->
        <div class="relative h-[400px] rounded-xl overflow-hidden shadow-2xl">
          <div
            v-for="(img, idx) in carouselImages"
            :key="idx"
            class="absolute inset-0 transition-opacity duration-1000"
            :class="currentSlide === idx ? 'opacity-100' : 'opacity-0'"
          >
            <img :src="img" alt="Perpustakaan" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
          </div>

          <!-- CONTROLS -->
          <button @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-blue-600 p-2 rounded-full transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-blue-600 p-2 rounded-full transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <!-- DOTS -->
          <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            <button
              v-for="(_, idx) in carouselImages"
              :key="idx"
              @click="goToSlide(idx)"
              class="h-2 rounded-full transition-all"
              :class="currentSlide === idx ? 'bg-white w-8' : 'bg-white/50 w-2 hover:bg-white/75'"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- VISI & MISI -->
    <section id="fasilitas" class="max-w-5xl mx-auto px-6 py-16" data-reveal-id="visi">
      <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Visi & Misi</h2>
      <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-8 border border-blue-100 dark:border-blue-800">
          <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Visi</h3>
          <p class="text-gray-700 dark:text-gray-300">Menjadi perpustakaan digital pilihan utama yang menyediakan akses pengetahuan berkualitas dan terpercaya.</p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-8 border border-blue-100 dark:border-blue-800">
          <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">Misi</h3>
          <ul class="text-gray-700 dark:text-gray-300 space-y-2 text-sm">
            <li>• Menyediakan koleksi buku berkualitas</li>
            <li>• Memudahkan akses pengetahuan untuk semua</li>
            <li>• Memberikan layanan terbaik</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- NILAI UTAMA -->
    <section class="bg-gray-50 dark:bg-gray-900 py-16 px-6" data-reveal-id="nilai">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Nilai Kami</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Profesional</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Layanan berkualitas tinggi dengan standar profesional.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Terpercaya</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Komitmen kami pada kepercayaan dan keamanan data pengguna.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Inovatif</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm">Terus berkembang dengan teknologi terkini.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- KEUNGGULAN -->
    <section class="max-w-5xl mx-auto px-6 py-16" data-reveal-id="keunggulan">
      <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Keunggulan Layanan</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
          <div class="flex gap-3 mb-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Koleksi Lengkap</h3>
          </div>
          <p class="text-gray-700 dark:text-gray-300 text-sm">Ribuan judul buku dari berbagai kategori dan penulis.</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
          <div class="flex gap-3 mb-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Sistem Mudah</h3>
          </div>
          <p class="text-gray-700 dark:text-gray-300 text-sm">Peminjaman dan pengembalian buku yang simpel dan cepat.</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
          <div class="flex gap-3 mb-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Aman & Terpercaya</h3>
          </div>
          <p class="text-gray-700 dark:text-gray-300 text-sm">Sistem keamanan terbaik untuk melindungi data Anda.</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
          <div class="flex gap-3 mb-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Support 24/7</h3>
          </div>
          <p class="text-gray-700 dark:text-gray-300 text-sm">Tim siap membantu Anda kapan saja diperlukan.</p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-br from-blue-600 to-indigo-700 dark:from-blue-900 dark:to-indigo-950 text-white py-16 px-6">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-4">Bergabunglah Bersama Kami</h2>
        <p class="text-blue-50 mb-8">Nikmati kemudahan akses koleksi buku berkualitas dengan sistem terpercaya.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link href="/register" class="px-8 py-3 rounded-lg font-semibold bg-white hover:bg-gray-50 text-blue-600 transition-all hover:scale-105 dark:bg-gray-100">Daftar Sekarang</Link>
          <Link href="/koleksi" class="px-8 py-3 rounded-lg border-2 border-white/30 hover:bg-white/10 text-white transition-all">Lihat Koleksi</Link>
        </div>
      </div>
    </section>

    <!-- BACK TO TOP -->
    <button @click="scrollTo('fasilitas')" class="fixed bottom-6 right-6 rounded-full shadow-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 hover:scale-110 transition-transform">
      <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
      </svg>
    </button>
  </PublicLayout>
</template>

<style scoped>
@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
