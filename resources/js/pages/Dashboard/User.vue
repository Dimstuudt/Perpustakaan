<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  activeLoans: Array,
  loanHistory: Array,
  recommendedBooks: Array,
  userStats: Object,
})

// Color mapping untuk setiap stat - fleksibel
const getStatColor = (key) => {
  const keyLower = String(key).toLowerCase()

  if (keyLower.includes('active') || keyLower.includes('aktif')) {
    return 'from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700'
  }
  if (keyLower.includes('total') || keyLower.includes('jumlah')) {
    return 'from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700'
  }
  if (keyLower.includes('pending') || keyLower.includes('tunggu')) {
    return 'from-amber-500 to-orange-600 dark:from-amber-600 dark:to-orange-700'
  }
  if (keyLower.includes('complete') || keyLower.includes('selesai') || keyLower.includes('return')) {
    return 'from-green-500 to-green-600 dark:from-green-600 dark:to-green-700'
  }

  return 'from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700'
}

// Icon mapping
const getStatIcon = (key) => {
  const keyLower = String(key).toLowerCase()

  if (keyLower.includes('active') || keyLower.includes('aktif')) return '📖'
  if (keyLower.includes('total') || keyLower.includes('jumlah')) return '📚'
  if (keyLower.includes('pending') || keyLower.includes('tunggu')) return '⏳'
  if (keyLower.includes('complete') || keyLower.includes('selesai') || keyLower.includes('return')) return '✅'

  return '📊'
}

// Format label
const formatLabel = (key) => {
  return key
    .replace(/([A-Z])/g, ' $1')
    .replace(/_/g, ' ')
    .trim()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

// Helper function to format fee
const formatFee = (fee) => {
  const numFee = Number(fee) || 0
  const formatted = numFee.toLocaleString('id-ID')
  return formatted.replace(/[.,]00$/, '')
}
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Saya" />

    <div class="p-6 space-y-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-3">
            <span class="text-4xl">👤</span>
            Dashboard Saya
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Ringkasan aktivitas peminjaman Anda
          </p>
        </div>
        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Online</span>
        </div>
      </div>

      <!-- Statistik Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(value, key) in stats"
          :key="key"
          class="group relative bg-gradient-to-br rounded-2xl p-6 shadow-lg hover:shadow-2xl dark:shadow-slate-900/50 transition-all duration-300 overflow-hidden"
          :class="getStatColor(key)"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 dark:bg-white/5 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-2xl shadow-lg">
                {{ getStatIcon(key) }}
              </div>
              <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <h3 class="text-white/90 text-sm font-medium mb-1">{{ formatLabel(key) }}</h3>
            <p class="text-3xl font-bold text-white">{{ value }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Peminjaman Aktif - Span 2 columns -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                  <span class="text-xl">📖</span>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Peminjaman Aktif
                  </h2>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Buku yang sedang Anda pinjam</p>
                </div>
              </div>
              <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-semibold">
                {{ activeLoans.length }} aktif
              </span>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Buku</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tgl Pinjam</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jatuh Tempo</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-if="activeLoans.length"
                  v-for="loan in activeLoans"
                  :key="loan.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                >
                  <td class="px-6 py-4">
                    <p class="text-gray-900 dark:text-gray-200 font-medium">{{ loan.book.title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ loan.book.author }}</p>
                  </td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ loan.borrowed_at }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ loan.due_date }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                      :class="loan.is_overdue
                        ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                        : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="loan.is_overdue ? 'bg-red-500' : 'bg-green-500'"></span>
                      {{ loan.is_overdue ? `Terlambat ${loan.days_remaining} hari` : 'Aktif' }}
                    </span>
                  </td>
                </tr>
                <tr v-else>
                  <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                        <span class="text-3xl">📭</span>
                      </div>
                      <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada peminjaman aktif</p>
                      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Anda belum meminjam buku saat ini</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Info Akun - 1 column -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                <span class="text-xl">👤</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                  Info Akun
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Detail aktivitas Anda</p>
              </div>
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Bergabung sejak</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ userStats.joined }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Hari sebagai member</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ userStats.daysSinceJoin }} hari</p>
              </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata durasi pinjam</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ userStats.avgLoanDuration }} hari</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Riwayat Peminjaman -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <span class="text-xl">📜</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                Riwayat Peminjaman
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Buku yang pernah Anda pinjam</p>
            </div>
          </div>
        </div>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
          <li
            v-if="loanHistory.length"
            v-for="loan in loanHistory"
            :key="loan.id"
            class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
          >
            <div class="flex items-center justify-between gap-4">
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 dark:text-gray-200 truncate">{{ loan.book.title }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ loan.borrowed_at }} - {{ loan.returned_at }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <div class="text-right">
                  <p class="text-xs text-gray-500 dark:text-gray-400">Biaya</p>
                  <p class="font-bold text-emerald-600 dark:text-emerald-400">
                    Rp {{ formatFee(loan.fee) }}
                  </p>
                </div>
              </div>
            </div>
          </li>
          <li v-else class="px-6 py-12">
            <div class="flex flex-col items-center justify-center">
              <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                <span class="text-3xl">📚</span>
              </div>
              <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada riwayat</p>
              <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Riwayat peminjaman akan muncul di sini</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Rekomendasi Buku -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
              <span class="text-xl">✨</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                Rekomendasi Buku
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Pilihan buku yang mungkin Anda suka</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <div v-if="recommendedBooks.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <Link
  v-for="book in recommendedBooks"
  :key="book.id"
  :href="route('user.loans.show', book.id)"
  class="group relative bg-gray-50 dark:bg-gray-700 rounded-xl p-3 hover:shadow-lg dark:hover:shadow-slate-900/50 transition-all duration-300 cursor-pointer block"
>
  <div class="aspect-[3/4] mb-3 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600">
    <img
      v-if="book.cover_path"
      :src="`/storage/${book.cover_path}`"
      :alt="book.title"
      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
    />
    <div v-else class="w-full h-full flex items-center justify-center">
      <span class="text-4xl">📚</span>
    </div>
  </div>
  <p class="text-sm font-semibold text-gray-900 dark:text-gray-200 line-clamp-2 mb-1">{{ book.title }}</p>
  <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ book.author }}</p>
</Link>

          </div>
          <div v-else class="py-12">
            <div class="flex flex-col items-center justify-center">
              <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                <span class="text-3xl">📚</span>
              </div>
              <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada rekomendasi</p>
              <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Rekomendasi buku akan muncul di sini</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
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
</style>
