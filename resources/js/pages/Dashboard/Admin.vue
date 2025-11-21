<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  activeLoans: Array,
  popularBooks: Array,
  chartData: Array,
})

// Color mapping untuk setiap stat - lebih fleksibel dengan includes
const getStatColor = (key) => {
  const keyLower = String(key).toLowerCase()

  if (keyLower.includes('book') || keyLower.includes('buku')) {
    return 'from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700'
  }
  if (keyLower.includes('user') || keyLower.includes('pengguna')) {
    return 'from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700'
  }
  if (keyLower.includes('loan') || keyLower.includes('pinjam')) {
    return 'from-green-500 to-green-600 dark:from-green-600 dark:to-green-700'
  }
  if (keyLower.includes('revenue') || keyLower.includes('pendapatan') || keyLower.includes('fee')) {
    return 'from-amber-500 to-orange-600 dark:from-amber-600 dark:to-orange-700'
  }

  // Default fallback
  return 'from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700'
}

// Icon mapping - lebih fleksibel
const getStatIcon = (key) => {
  const keyLower = String(key).toLowerCase()

  if (keyLower.includes('book') || keyLower.includes('buku')) return '📚'
  if (keyLower.includes('user') || keyLower.includes('pengguna')) return '👥'
  if (keyLower.includes('loan') || keyLower.includes('pinjam')) return '📖'
  if (keyLower.includes('revenue') || keyLower.includes('pendapatan') || keyLower.includes('fee')) return '💰'

  return '📊'
}

// Format key menjadi label yang lebih baik
const formatLabel = (key) => {
  const labels = {
    totalBooks: 'Total Buku',
    totalUsers: 'Total Pengguna',
    activeLoans: 'Peminjaman Aktif',
    totalRevenue: 'Total Pendapatan',
    // Snake case versions
    total_books: 'Total Buku',
    total_users: 'Total Pengguna',
    active_loans: 'Peminjaman Aktif',
    total_revenue: 'Total Pendapatan'
  }
  return labels[key] || key.replace(/([A-Z_])/g, ' $1').trim()
}

// Helper function to format revenue
const formatFee = (fee) => {
  const numFee = Number(fee) || 0
  const formatted = numFee.toLocaleString('id-ID')
  return formatted.replace(/[.,]00$/, '')
}

// Format value dengan Rp jika revenue
const formatValue = (key, value) => {
  const keyLower = String(key).toLowerCase()
  if (keyLower.includes('revenue') || keyLower.includes('pendapatan') || keyLower.includes('fee')) {
    return 'Rp ' + formatFee(value)
  }
  return value
}
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Admin" />

    <div class="p-6 space-y-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-3">
            <span class="text-4xl">📊</span>
            Dashboard Admin
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Ringkasan aktivitas perpustakaan Anda
          </p>
        </div>
        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Live</span>
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
            <p class="text-3xl font-bold text-white">{{ formatValue(key, value) }}</p>
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
                  <p class="text-xs text-gray-500 dark:text-gray-400">Daftar buku yang sedang dipinjam</p>
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
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">User</th>
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
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                        {{ loan.user.name.charAt(0).toUpperCase() }}
                      </div>
                      <span class="ml-3 text-gray-900 dark:text-gray-200 font-medium">{{ loan.user.name }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-gray-900 dark:text-gray-200 font-medium">{{ loan.book.title }}</p>
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
                      {{ loan.is_overdue ? `Terlambat ${loan.days_overdue} hari` : 'Aktif' }}
                    </span>
                  </td>
                </tr>
                <tr v-else>
                  <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                        <span class="text-3xl">📭</span>
                      </div>
                      <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada peminjaman aktif</p>
                      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Belum ada buku yang dipinjam saat ini</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Buku Populer - 1 column -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                <span class="text-xl">🔥</span>
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                  Buku Populer
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Paling banyak dipinjam</p>
              </div>
            </div>
          </div>
          <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <li
              v-if="popularBooks.length"
              v-for="(book, index) in popularBooks"
              :key="book.book.id"
              class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
            >
              <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                  {{ index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-gray-200 truncate">{{ book.book.title }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ book.book.author }}</p>
                </div>
                <div class="flex items-center gap-1 px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                  <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  <span class="font-bold text-indigo-600 dark:text-indigo-400 text-sm">{{ book.count }}</span>
                </div>
              </div>
            </li>
            <li v-else class="px-6 py-12">
              <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                  <span class="text-3xl">📚</span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Buku populer akan muncul di sini</p>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Tren Peminjaman -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <span class="text-xl">📈</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                Tren Peminjaman
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">3 bulan terakhir</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div
              v-for="(month, index) in chartData"
              :key="month.month"
              class="relative group"
            >
              <div class="p-6 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border border-gray-200 dark:border-gray-600 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ month.month }}</span>
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                    <span class="text-white text-xs font-bold">{{ index + 1 }}</span>
                  </div>
                </div>
                <p class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                  {{ month.count }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                  </svg>
                  peminjaman
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
