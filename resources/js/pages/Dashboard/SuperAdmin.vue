<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
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
    totalUsers: number
    totalFee: number
  },
  topBorrowers: {
    user: { id: number, name: string, email: string }
    total_loans: number
  }[],
  chartData?: { month: string; loans: number }[]
}>()

// Chart dataset dengan gradient
const chartData = {
  labels: props.chartData?.map(d => d.month) ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
  datasets: [
    {
      label: 'Peminjaman',
      data: props.chartData?.map(d => d.loans) ?? [10, 25, 18, 30, 22, 28],
      borderColor: '#8b5cf6',
      backgroundColor: 'rgba(139, 92, 246, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4,
      pointRadius: 6,
      pointBackgroundColor: '#8b5cf6',
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
</script>

<template>
  <Head title="Super Admin Dashboard" />

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-blue-50 p-6 md:p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
          Dashboard Super Admin
        </h1>
        <p class="text-slate-600 mt-2">Selamat datang kembali! Kelola sistem perpustakaan Anda dengan mudah.</p>
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
              <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Koleksi</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Total Buku</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.totalBooks }}</p>
          </div>
        </div>

        <!-- Buku Dipinjam -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                📖
              </div>
              <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Aktif</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Buku Dipinjam</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.borrowedBooks }}</p>
          </div>
        </div>

        <!-- Total User -->
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                👥
              </div>
              <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Member</span>
            </div>
            <h3 class="text-slate-600 text-sm font-medium mb-1">Total User</h3>
            <p class="text-3xl font-bold text-slate-800">{{ props.stats.totalUsers }}</p>
          </div>
        </div>

        <!-- Total Fee -->
        <div class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-300"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-2xl shadow-lg">
                💰
              </div>
              <span class="text-xs font-semibold text-white bg-white/20 backdrop-blur px-3 py-1 rounded-full">Revenue</span>
            </div>
            <h3 class="text-white/90 text-sm font-medium mb-1">Total Pendapatan</h3>
            <p class="text-3xl font-bold text-white">Rp {{ props.stats.totalFee.toLocaleString() }}</p>
          </div>
        </div>
      </div>

      <!-- Chart + Top Borrowers -->
      <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-slate-800">Trend Peminjaman</h2>
              <p class="text-sm text-slate-500 mt-1">6 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
              <span class="text-slate-600">Peminjaman</span>
            </div>
          </div>
          <div style="height: 300px;">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Top Borrowers -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-xl shadow-md">
              🏆
            </div>
            <div>
              <h2 class="text-xl font-bold text-slate-800">Top Peminjam</h2>
              <p class="text-xs text-slate-500">Member paling aktif</p>
            </div>
          </div>
          <div class="space-y-3">
            <div
              v-for="(b, idx) in props.topBorrowers.slice(0, 5)"
              :key="b.user.id"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors duration-200"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white text-xs font-bold shadow-md">
                {{ idx + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">{{ b.user.name }}</p>
                <p class="text-xs text-slate-500 truncate">{{ b.user.email }}</p>
              </div>
              <div class="flex items-center gap-1 bg-purple-50 px-3 py-1 rounded-full">
                <span class="text-xs font-bold text-purple-600">{{ b.total_loans }}</span>
                <span class="text-xs text-purple-600">📚</span>
              </div>
            </div>
            <div v-if="!props.topBorrowers.length" class="text-center py-8">
              <div class="text-4xl mb-2">📭</div>
              <p class="text-sm text-slate-500">Belum ada data peminjam</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <button class="group bg-white hover:bg-gradient-to-br hover:from-blue-500 hover:to-blue-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-blue-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📕
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Tambah Buku</span>
        </button>
        <button class="group bg-white hover:bg-gradient-to-br hover:from-emerald-500 hover:to-emerald-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-emerald-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            👥
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Kelola User</span>
        </button>
        <button class="group bg-white hover:bg-gradient-to-br hover:from-purple-500 hover:to-purple-600 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-purple-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            📑
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Lihat Laporan</span>
        </button>
        <button class="group bg-white hover:bg-gradient-to-br hover:from-slate-600 hover:to-slate-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col items-center gap-3">
          <div class="w-14 h-14 bg-slate-50 group-hover:bg-white/20 rounded-2xl flex items-center justify-center text-3xl transition-colors duration-300 shadow-md">
            ⚙️
          </div>
          <span class="font-semibold text-slate-800 group-hover:text-white transition-colors duration-300">Pengaturan</span>
        </button>
      </div>
    </div>
  </AppLayout>
</template>
