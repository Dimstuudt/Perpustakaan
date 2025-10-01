<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

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

// Chart dataset
const chartData = {
  labels: props.chartData?.map(d => d.month) ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
  datasets: [
    {
      label: 'Jumlah Peminjaman',
      data: props.chartData?.map(d => d.loans) ?? [10, 25, 18, 30, 22],
      backgroundColor: '#3b82f6',
      borderRadius: 6
    }
  ]
}

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: true, position: 'top' },
    tooltip: { enabled: true }
  },
  scales: {
    y: { beginAtZero: true, ticks: { stepSize: 5 } }
  }
}
</script>

<template>
  <Head title="Super Admin Dashboard" />

  <AppLayout>
    <div class="p-6 space-y-8">
      <h1 class="text-3xl font-bold">📊 Super Admin Dashboard</h1>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button
          class="p-5 bg-blue-50 hover:bg-blue-100 border rounded-xl flex flex-col items-center shadow-sm"
        >
          <span class="text-3xl">📕</span>
          <span class="mt-2 font-semibold">Tambah Buku</span>
        </button>
        <button
          class="p-5 bg-green-50 hover:bg-green-100 border rounded-xl flex flex-col items-center shadow-sm"
        >
          <span class="text-3xl">👥</span>
          <span class="mt-2 font-semibold">Kelola User</span>
        </button>
        <button
          class="p-5 bg-yellow-50 hover:bg-yellow-100 border rounded-xl flex flex-col items-center shadow-sm"
        >
          <span class="text-3xl">📑</span>
          <span class="mt-2 font-semibold">Lihat Laporan</span>
        </button>
        <button
          class="p-5 bg-red-50 hover:bg-red-100 border rounded-xl flex flex-col items-center shadow-sm"
        >
          <span class="text-3xl">⚙️</span>
          <span class="mt-2 font-semibold">Pengaturan</span>
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 border rounded-xl bg-white shadow-sm flex flex-col">
          <div class="flex items-center gap-2 text-gray-500 text-sm">
            📚 <span>Total Buku</span>
          </div>
          <div class="text-3xl font-bold mt-1">{{ props.stats.totalBooks }}</div>
        </div>
        <div class="p-5 border rounded-xl bg-white shadow-sm flex flex-col">
          <div class="flex items-center gap-2 text-gray-500 text-sm">
            📖 <span>Buku Dipinjam</span>
          </div>
          <div class="text-3xl font-bold mt-1">{{ props.stats.borrowedBooks }}</div>
        </div>
        <div class="p-5 border rounded-xl bg-white shadow-sm flex flex-col">
          <div class="flex items-center gap-2 text-gray-500 text-sm">
            👤 <span>Total User</span>
          </div>
          <div class="text-3xl font-bold mt-1">{{ props.stats.totalUsers }}</div>
        </div>
        <div class="p-5 border rounded-xl bg-white shadow-sm flex flex-col">
          <div class="flex items-center gap-2 text-gray-500 text-sm">
            💰 <span>Total Fee</span>
          </div>
          <div class="text-3xl font-bold mt-1 text-green-600">
            Rp {{ props.stats.totalFee.toLocaleString() }}
          </div>
        </div>
      </div>

      <!-- Chart + Top Borrowers -->
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Chart -->
        <div class="p-5 border rounded-xl bg-white shadow-sm">
          <h2 class="font-semibold mb-4">📈 Trend Peminjaman (6 Bulan)</h2>
          <Bar :data="chartData" :options="chartOptions" />
        </div>

        <!-- Top Borrowers -->
        <div class="p-5 border rounded-xl bg-white shadow-sm">
          <h2 class="font-semibold mb-4">🏆 Top Peminjam</h2>
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left border-b">
                <th class="p-2">Nama</th>
                <th class="p-2">Email</th>
                <th class="p-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="b in props.topBorrowers"
                :key="b.user.id"
                class="border-b hover:bg-gray-50"
              >
                <td class="p-2">{{ b.user.name }}</td>
                <td class="p-2">{{ b.user.email }}</td>
                <td class="p-2 font-medium">{{ b.total_loans }}</td>
              </tr>
              <tr v-if="!props.topBorrowers.length">
                <td colspan="3" class="p-3 text-center text-gray-500">
                  Belum ada data peminjam
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
