<script setup>
import { ref, onMounted } from "vue"
import { Link } from "@inertiajs/vue3"

const lines = [
  "📚 Lagi cari bacaan seru? Perpustakaan ini penuh kejutan!",
  "✨ Katanya satu buku bisa ubah hidup, mau buktiin?",
  "😉 Banyak koleksi baru nunggu kamu jelajahi!",
  "🕮 Kalau buka satu buku aja udah seru, gimana kalau punya akun sendiri?",
  "🌸 Buku-buku di sini siap nemenin kamu kapan aja~",
  "🚀 Mau aku tunjukin koleksi paling populer minggu ini?",
  "💡 Login biar kamu bisa simpan buku favoritmu loh!",
  "🌙 Malam panjang paling pas ditemani buku bagus, setuju?",
  "🎯 Siap nemuin buku yang cocok sama seleramu?",
  "💌 Buku yang tepat bisa jadi teman terbaik — tinggal kamu login dulu 😉"
]

const sublines = [
  "Bikin akun dulu yuk, biar bisa pinjam & simpan buku kesukaanmu 📖",
  "Login biar bisa lanjut baca tanpa kehilangan jejak ✨",
  "Tanpa akun juga bisa sih, tapi bakal lebih seru kalau kamu gabung 😏",
  "Yuk gabung, siapa tau nemu inspirasi baru dari rak buku kami 🌟",
  "Akses koleksi lengkap, pinjam buku, dan kelola bacaanmu dengan mudah 🚀",
  "Cuma butuh beberapa detik buat login, manfaatnya bisa selamanya 💯"
]

const gradients = [
  "from-sky-100 via-blue-50 to-indigo-100 dark:from-gray-800 dark:via-gray-900 dark:to-indigo-950",
  "from-amber-100 via-yellow-50 to-orange-100 dark:from-gray-800 dark:via-gray-900 dark:to-yellow-950",
  "from-green-100 via-emerald-50 to-teal-100 dark:from-gray-800 dark:via-gray-900 dark:to-teal-950",
  "from-rose-100 via-pink-50 to-red-100 dark:from-gray-800 dark:via-gray-900 dark:to-rose-950"
]

const show = ref(false)
const closing = ref(false)
const quote = ref("")
const sub = ref("")
const bg = ref("")
const sessionKey = "libraryWelcomeShown"

function closePopup() {
  closing.value = true
  setTimeout(() => {
    show.value = false
    closing.value = false
  }, 400)
}

function randomPick() {
  quote.value = lines[Math.floor(Math.random() * lines.length)]
  sub.value = sublines[Math.floor(Math.random() * sublines.length)]
  bg.value = gradients[Math.floor(Math.random() * gradients.length)]
}

onMounted(() => {
  if (!sessionStorage.getItem(sessionKey)) {
    setTimeout(() => {
      randomPick()
      show.value = true
      sessionStorage.setItem(sessionKey, "true")
      setInterval(randomPick, 5000)
    }, 600)
  }
})
</script>

<template>
  <div
    v-if="show"
    class="fixed right-6 bottom-6 z-50 w-80 md:w-96"
    role="dialog"
    aria-label="Welcome popup"
  >
    <div
      class="relative rounded-2xl p-6 shadow-2xl border border-white/40 backdrop-blur-lg transition-all duration-400"
      :class="[ `bg-gradient-to-br ${bg}`, closing ? 'animate-fadeOut' : 'animate-fadeIn']"
    >
      <!-- Tombol Tutup -->
      <button
        @click="closePopup"
        class="absolute top-3 right-3 rounded-full p-1 hover:scale-110 transition"
        aria-label="Tutup"
      >
        <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Header Brand -->
      <div class="text-center mb-3">
        <div class="flex items-center justify-center gap-2">
          <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 014 17.5v-13z" />
          </svg>
          <h2 class="text-2xl font-extrabold text-blue-800 dark:text-blue-400 tracking-wide">Perpus</h2>
        </div>
        <p class="text-xs uppercase tracking-widest text-gray-600 dark:text-gray-300 mt-1">
          Digital Library
        </p>
      </div>

      <!-- Dynamic Message -->
      <p class="text-sm text-gray-800 dark:text-gray-200 mt-2 animate-fadeSlide text-center">
        {{ quote }}
      </p>

      <p class="mt-4 text-sm md:text-base text-gray-800 dark:text-gray-200 font-medium animate-fadeSlide text-center">
        {{ sub }}
      </p>

<!-- Tombol Aksi -->
<div class="mt-5 grid grid-cols-2 gap-2">
  <!-- Tombol Nanti Aja -->
  <button
    class="col-span-1 inline-flex items-center justify-center px-3 py-2 rounded-lg
           bg-blue-500 text-white font-semibold text-sm hover:bg-blue-600 transition transform hover:scale-105 hover:animate-wiggle"
    @click="closePopup"
  >
    Nanti aja 😄
  </button>

  <!-- Tombol Login / Daftar -->
  <Link
    href="/login"
    class="col-span-1 inline-flex items-center justify-center px-3 py-2 rounded-lg
           border border-blue-200 dark:border-gray-700 bg-white/70 dark:bg-gray-700
           text-sm font-medium hover:shadow-lg transition transform hover:scale-105 hover:animate-wiggle"
    @click.native="closePopup"
  >
    Login / Daftar ✨
  </Link>
</div>


      <!-- Footer -->
      <div class="mt-4 text-xs text-gray-600 dark:text-gray-400 text-center italic animate-pulse">
        Temukan buku yang menginspirasi harimu 📖
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes wiggle { 0%,100%{transform:rotate(-2deg);}50%{transform:rotate(2deg);} }
@keyframes fadeIn { from{opacity:0;transform:scale(0.95) translateY(6px);} to{opacity:1;transform:scale(1) translateY(0);} }
@keyframes fadeOut { from{opacity:1;transform:scale(1) translateY(0);} to{opacity:0;transform:scale(0.95) translateY(6px);} }
@keyframes fadeSlide { from{opacity:0;transform:translateY(5px);} to{opacity:1;transform:translateY(0);} }

.animate-wiggle { animation: wiggle 0.3s ease-in-out; }
.animate-fadeIn { animation: fadeIn 0.4s ease-out; }
.animate-fadeOut { animation: fadeOut 0.4s ease-in; }
.animate-fadeSlide { animation: fadeSlide 0.5s ease-out; }
</style>
