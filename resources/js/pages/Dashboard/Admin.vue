<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler)

const props = defineProps<{
  stats: {
    totalBooks: number
    borrowedBooks: number
    availableBooks: number
    overdueLoans: number
  },
  pendingReturns: {
    id: number
    user: { id: number, name: string, email: string }
    book: { id: number, title: string }
    borrowed_at: string
    due_date: string
    is_overdue: boolean
    days_overdue: number
  }[],
  popularBooks: {
    book: { id: number, title: string, author: string }
    loan_count: number
  }[],
  monthlyStats: {
    thisMonth: number
    lastMonth: number
  },
  chartData?: { month: string; loans: number }[]
}>()

// Chart configuration
const chartData = {
  labels: props.chartData?.map(d => d.month) ?? ['Jan', 'Feb', 'Mar'],
  datasets: [
    {
      label: 'Peminjaman',
      data: props.chartData?.map(d => d.loans) ?? [0, 0, 0],
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointRadius: 6,
      pointBackgroundColor: '#3b82f6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverRadius: 8
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      borderRadius: 8,
      titleFont: { size: 14, weight: 'bold' },
      bodyFont: { size: 13 }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
      ticks: { font: { size: 12 }, color: '#64748b' }
    },
    x: {
      grid: { display: false, drawBorder: false },
      ticks: { font: { size: 12 }, color: '#64748b' }
    }
  }
}

// Calculate percentage change
const monthlyChange = props.monthlyStats.lastMonth > 0
  ? ((props.monthlyStats.thisMonth - props.monthlyStats.lastMonth) / props.monthlyStats.lastMonth * 100).toFixed(1)
  : 0

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6 md:p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
          Dashboard Admin
        </h1>
        <p class="text-slate-600 mt-2">Kelola peminjaman dan pantau aktivitas perpustakaan</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Buku -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                📚
              </div>
              <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Total</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Total Buku</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.totalBooks }}</p>
          </div>
        </div>

        <!-- Buku Tersedia -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                ✅
              </div>
              <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Ready</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Buku Tersedia</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.availableBooks }}</p>
          </div>
        </div>

        <!-- Sedang Dipinjam -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                📖
              </div>
              <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Active</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Sedang Dipinjam</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.borrowedBooks }}</p>
          </div>
        </div>

        <!-- Terlambat -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                ⚠️
              </div>
              <span class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-full">Urgent</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Terlambat</h3>
            <p class="text-3xl font-bold text-red-600">{{ props.stats.overdueLoans }}</p>
          </div>
        </div>
      </div>

      <!-- Chart + Monthly Stats -->
      <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-slate-800">Trend Peminjaman</h2>
              <p class="text-sm text-slate-500 mt-1">3 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span class="text-slate-600">Peminjaman</span>
            </div>
          </div>
          <div style="height: 280px;">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <h2 class="text-xl font-bold text-slate-800 mb-6">Perbandingan Bulanan</h2>

          <div class="space-y-6">
            <!-- This Month -->
            <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl">
              <p class="text-sm text-slate-600 mb-2">Bulan Ini</p>
              <p class="text-4xl font-bold text-blue-600 mb-2">{{ props.monthlyStats.thisMonth }}</p>
              <div class="flex items-center gap-2">
                <span v-if="Number(monthlyChange) > 0" class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full flex items-center gap-1">
                  <span>↑</span> {{ monthlyChange }}%
                </span>
                <span v-else-if="Number(monthlyChange) < 0" class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full flex items-center gap-1">
                  <span>↓</span> {{ Math.abs(Number(monthlyChange)) }}%
                </span>
                <span v-else class="text-xs font-semibold text-slate-600 bg-slate-50 px-2 py-1 rounded-full">
                  → 0%
                </span>
                <span class="text-xs text-slate-500">vs bulan lalu</span>
              </div>
            </div>

            <!-- Last Month -->
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-sm text-slate-600 mb-2">Bulan Lalu</p>
              <p class="text-3xl font-bold text-slate-700">{{ props.monthlyStats.lastMonth }}</p>
            </div>

            <!-- Info Box -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
              <div class="flex items-start gap-2">
                <span class="text-xl">💡</span>
                <div>
                  <p class="text-sm font-semibold text-amber-900 mb-1">Tips</p>
                  <p class="text-xs text-amber-700">Monitor peminjaman yang mendekati jatuh tempo untuk menghindari keterlambatan.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Returns + Popular Books -->
      <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Pending Returns -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                ⏰
              </div>
              <div>
                <h2 class="text-xl font-bold text-slate-800">Perlu Diproses</h2>
                <p class="text-xs text-slate-500">Peminjaman aktif dan terlambat</p>
              </div>
            </div>
            <Link href="/loans" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
              Lihat Semua →
            </Link>
          </div>

          <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
            <div
              v-for="loan in props.pendingReturns"
              :key="loan.id"
              class="p-4 border rounded-xl hover:shadow-md transition-all duration-200"
              :class="loan.is_overdue ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200'"
            >
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-slate-800 text-sm truncate mb-1">{{ loan.book.title }}</h3>
                  <p class="text-xs text-slate-600 truncate">{{ loan.user.name }}</p>
                </div>
                <span
                  v-if="loan.is_overdue"
                  class="ml-2 text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full whitespace-nowrap"
                >
                  {{ loan.days_overdue }} hari
                </span>
                <span
                  v-else
                  class="ml-2 text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full whitespace-nowrap"
                >
                  Aktif
                </span>
              </div>

              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-3 text-slate-500">
                  <span>Pinjam: {{ formatDate(loan.borrowed_at) }}</span>
                  <span>•</span>
                  <span :class="loan.is_overdue ? 'text-red-600 font-semibold' : ''">
                    Kembali: {{ formatDate(loan.due_date) }}
                  </span>
                </div>

              </div>
            </div>

            <div v-if="!props.pendingReturns.length" class="text-center py-12">
              <div class="text-5xl mb-3">✨</div>
              <p class="text-sm text-slate-500 font-medium">Semua peminjaman sudah diproses!</p>
            </div>
          </div>
        </div>

        <!-- Popular Books -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-xl shadow-md">
              🔥
            </div>
            <div>
              <h2 class="text-xl font-bold text-slate-800">Buku Populer</h2>
              <p class="text-xs text-slate-500">Paling sering dipinjam</p>
            </div>
          </div>

          <div class="space-y-3">
            <div
              v-for="(item, idx) in props.popularBooks"
              :key="item.book.id"
              class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors duration-200"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0">
                {{ idx + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate mb-1">{{ item.book.title }}</p>
                <p class="text-xs text-slate-500 truncate">{{ item.book.author }}</p>
              </div>
              <div class="flex flex-col items-end gap-1 flex-shrink-0">
                <span class="text-lg font-bold text-orange-600">{{ item.loan_count }}</span>
                <span class="text-xs text-slate-500">pinjam</span>
              </div>
            </div>

            <div v-if="!props.popularBooks.length" class="text-center py-8">
              <div class="text-4xl mb-2">📚</div>
              <p class="text-sm text-slate-500">Belum ada data peminjaman</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <Link href="/books" class="group bg-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-blue-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📚
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Kelola Buku</span>
        </Link>

        <Link href="/loans" class="group bg-white hover:bg-gradient-to-br hover:from-purple-500 hover:to-purple-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-purple-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📋
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Daftar Pinjaman</span>
        </Link>

        <Link href="/users" class="group bg-white hover:bg-gradient-to-br hover:from-emerald-500 hover:to-emerald-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-emerald-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            👥
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Data Member</span>
        </Link>

        <Link href="/categories" class="group bg-white hover:bg-gradient-to-br hover:from-orange-500 hover:to-red-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-orange-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            🔬
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Kategori</span>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
