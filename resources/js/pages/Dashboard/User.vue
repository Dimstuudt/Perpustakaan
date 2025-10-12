<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{
  stats: {
    currentLoans: number
    totalBorrowed: number
    overdueBooks: number
    totalFees: number
  },
  activeLoans: {
    id: number
    book: { id: number, title: string, author: string, cover_path?: string }
    borrowed_at: string
    due_date: string
    is_overdue: boolean
    days_remaining: number
  }[],
  loanHistory: {
    id: number
    book: { id: number, title: string, author: string, cover_path?: string }
    borrowed_at: string
    returned_at: string
    fee: number
  }[],
  recommendedBooks: {
    id: number
    title: string
    author: string
    cover_path?: string
    stock: number
  }[],
  userStats: {
    joinDate: string
    totalDays: number
    averageLoanDays: number
  }
}>()

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getProgressColor = (days: number) => {
  if (days <= 0) return 'bg-red-500'
  if (days <= 2) return 'bg-orange-500'
  if (days <= 5) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getProgressWidth = (daysRemaining: number, totalDays: number = 14) => {
  const percentage = (daysRemaining / totalDays) * 100
  return Math.max(0, Math.min(100, percentage))
}
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-violet-50 via-pink-50 to-blue-50 p-6 md:p-8">
      <!-- Welcome Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-violet-600 to-pink-600 bg-clip-text text-transparent mb-2">
          Selamat Datang! 👋
        </h1>
        <p class="text-slate-600">Jelajahi koleksi buku dan kelola peminjaman Anda dengan mudah</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Current Loans -->
        <div class="group relative bg-white rounded-2xl p-5 md:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-violet-500/10 to-violet-600/10 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center text-xl shadow-lg">
                📖
              </div>
            </div>
            <h3 class="text-slate-600 text-xs font-medium mb-1">Sedang Dipinjam</h3>
            <p class="text-2xl md:text-3xl font-bold text-slate-800">{{ props.stats.currentLoans }}</p>
          </div>
        </div>

        <!-- Total Borrowed -->
        <div class="group relative bg-white rounded-2xl p-5 md:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-xl shadow-lg">
                📚
              </div>
            </div>
            <h3 class="text-slate-600 text-xs font-medium mb-1">Total Pinjaman</h3>
            <p class="text-2xl md:text-3xl font-bold text-slate-800">{{ props.stats.totalBorrowed }}</p>
          </div>
        </div>

        <!-- Overdue -->
        <div class="group relative bg-white rounded-2xl p-5 md:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-xl shadow-lg">
                ⚠️
              </div>
            </div>
            <h3 class="text-slate-600 text-xs font-medium mb-1">Terlambat</h3>
            <p class="text-2xl md:text-3xl font-bold text-red-600">{{ props.stats.overdueBooks }}</p>
          </div>
        </div>

        <!-- Total Fees -->
        <div class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 md:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-xl shadow-lg">
                💰
              </div>
            </div>
            <h3 class="text-white/90 text-xs font-medium mb-1">Total Biaya</h3>
            <p class="text-xl md:text-2xl font-bold text-white">Rp {{ props.stats.totalFees.toLocaleString() }}</p>
          </div>
        </div>
      </div>

      <!-- Active Loans -->
      <div v-if="props.activeLoans.length > 0" class="bg-white rounded-2xl p-6 shadow-lg mb-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-pink-500 rounded-xl flex items-center justify-center text-xl shadow-md">
            📖
          </div>
          <div>
            <h2 class="text-xl font-bold text-slate-800">Buku yang Sedang Dipinjam</h2>
            <p class="text-xs text-slate-500">Jangan lupa kembalikan tepat waktu!</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div
            v-for="loan in props.activeLoans"
            :key="loan.id"
            class="group relative p-5 border-2 rounded-2xl hover:shadow-xl transition-all duration-300"
            :class="loan.is_overdue ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200 hover:border-violet-300'"
          >
            <div class="flex gap-4">
              <!-- Book Cover -->
              <div class="w-20 h-28 bg-gradient-to-br from-violet-200 to-pink-200 rounded-xl flex items-center justify-center text-3xl shadow-md overflow-hidden flex-shrink-0">
                <img v-if="loan.book.cover_path" :src="`/storage/${loan.book.cover_path}`" :alt="loan.book.title" class="w-full h-full object-cover" />
                <span v-else>📚</span>
              </div>

              <!-- Book Info -->
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-slate-800 mb-1 line-clamp-2">{{ loan.book.title }}</h3>
                <p class="text-sm text-slate-600 mb-3">{{ loan.book.author }}</p>

                <!-- Progress Bar -->
                <div class="mb-3">
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-slate-600">Sisa waktu</span>
                    <span v-if="loan.is_overdue" class="font-bold text-red-600">TERLAMBAT!</span>
                    <span v-else class="font-semibold" :class="loan.days_remaining <= 2 ? 'text-orange-600' : 'text-slate-600'">
                      {{ loan.days_remaining }} hari
                    </span>
                  </div>
                  <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div
                      class="h-full transition-all duration-500 rounded-full"
                      :class="getProgressColor(loan.days_remaining)"
                      :style="{ width: `${getProgressWidth(loan.days_remaining)}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Dates -->
                <div class="flex items-center gap-3 text-xs text-slate-500">
                  <span>Pinjam: {{ formatDate(loan.borrowed_at) }}</span>
                  <span>•</span>
                  <span :class="loan.is_overdue ? 'text-red-600 font-semibold' : ''">
                    Kembali: {{ formatDate(loan.due_date) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommended Books + History -->
      <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Recommended Books -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                ✨
              </div>
              <div>
                <h2 class="text-xl font-bold text-slate-800">Rekomendasi Buku</h2>
                <p class="text-xs text-slate-500">Buku tersedia untuk dipinjam</p>
              </div>
            </div>
            <Link href="/books" class="text-sm font-semibold text-violet-600 hover:text-violet-700">
              Lihat Semua →
            </Link>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <Link
              v-for="book in props.recommendedBooks"
              :key="book.id"
              :href="`/user/loansuser/${book.id}`"
              class="group p-4 border-2 border-slate-200 rounded-xl hover:border-violet-300 hover:shadow-lg transition-all duration-300"
            >
              <div class="w-full aspect-[2/3] bg-gradient-to-br from-violet-200 to-pink-200 rounded-lg flex items-center justify-center text-4xl mb-3 overflow-hidden shadow-md">
                <img v-if="book.cover_path" :src="`/storage/${book.cover_path}`" :alt="book.title" class="w-full h-full object-cover" />
                <span v-else>📕</span>
              </div>
              <h3 class="font-semibold text-sm text-slate-800 mb-1 line-clamp-2 group-hover:text-violet-600 transition-colors">
                {{ book.title }}
              </h3>
              <p class="text-xs text-slate-600 mb-2 truncate">{{ book.author }}</p>
              <div class="flex items-center gap-1 text-xs">
                <span class="text-green-600 font-semibold">{{ book.stock }}</span>
                <span class="text-slate-500">tersedia</span>
              </div>
            </Link>

            <div v-if="!props.recommendedBooks.length" class="col-span-full text-center py-12">
              <div class="text-5xl mb-3">📚</div>
              <p class="text-sm text-slate-500">Belum ada rekomendasi</p>
            </div>
          </div>
        </div>

        <!-- Loan History + User Stats -->
        <div class="space-y-6">
          <!-- User Stats -->
          <div class="bg-gradient-to-br from-violet-500 to-pink-500 rounded-2xl p-6 shadow-lg text-white">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-xl shadow-md">
                🎯
              </div>
              <h2 class="text-lg font-bold">Statistik Anda</h2>
            </div>

            <div class="space-y-3">
              <div class="p-3 bg-white/10 backdrop-blur rounded-xl">
                <p class="text-xs text-white/80 mb-1">Member Sejak</p>
                <p class="font-bold">{{ formatDate(props.userStats.joinDate) }}</p>
              </div>
              <div class="p-3 bg-white/10 backdrop-blur rounded-xl">
                <p class="text-xs text-white/80 mb-1">Total Hari</p>
                <p class="font-bold">{{ props.userStats.totalDays }} hari</p>
              </div>
              <div class="p-3 bg-white/10 backdrop-blur rounded-xl">
                <p class="text-xs text-white/80 mb-1">Rata-rata Pinjam</p>
                <p class="font-bold">{{ Math.round(props.userStats.averageLoanDays) }} hari</p>
              </div>
            </div>
          </div>

          <!-- Loan History -->
          <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center text-lg shadow-md">
                📋
              </div>
              <h2 class="text-lg font-bold text-slate-800">Riwayat</h2>
            </div>

            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
              <div
                v-for="history in props.loanHistory"
                :key="history.id"
                class="p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors"
              >
                <p class="font-semibold text-sm text-slate-800 mb-1 line-clamp-1">{{ history.book.title }}</p>
                <p class="text-xs text-slate-600 mb-2">{{ history.book.author }}</p>
                <div class="flex items-center justify-between text-xs">
                  <span class="text-slate-500">{{ formatDate(history.returned_at) }}</span>
                  <span v-if="history.fee > 0" class="font-semibold text-red-600">
                    Rp {{ history.fee.toLocaleString() }}
                  </span>
                  <span v-else class="text-green-600 font-semibold">✓ Tepat waktu</span>
                </div>
              </div>

              <div v-if="!props.loanHistory.length" class="text-center py-8">
                <div class="text-4xl mb-2">📭</div>
                <p class="text-sm text-slate-500">Belum ada riwayat</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link href="/books" class="group bg-white hover:bg-gradient-to-br hover:from-violet-500 hover:to-violet-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-violet-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            🔍
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300 text-center">Cari Buku</span>
        </Link>

        <Link href="/my-loans" class="group bg-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-blue-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📚
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300 text-center">Pinjaman Saya</span>
        </Link>

        <Link href="/history" class="group bg-white hover:bg-gradient-to-br hover:from-pink-500 hover:to-pink-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-pink-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📋
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300 text-center">Riwayat</span>
        </Link>

        <Link href="/profile" class="group bg-white hover:bg-gradient-to-br hover:from-indigo-500 hover:to-indigo-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-indigo-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            👤
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300 text-center">Profil</span>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
