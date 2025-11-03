<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  activeLoans: Array,
  loanHistory: Array,
  recommendedBooks: Array,
  userStats: Object,
})
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Saya" />

    <div class="p-6 space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
          Dashboard Saya
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
          Ringkasan aktivitas peminjaman Anda
        </p>
      </div>

      <!-- Statistik Ringkas -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="(value, key) in stats"
          :key="key"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-100 dark:border-gray-700"
        >
          <h3 class="text-sm font-medium text-gray-500 capitalize">
            {{ key.replace(/([A-Z])/g, ' $1') }}
          </h3>
          <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
            {{ value }}
          </p>
        </div>
      </div>

      <!-- Peminjaman Aktif -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Peminjaman Aktif
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
            <thead class="bg-gray-100 dark:bg-gray-700/50">
              <tr>
                <th class="px-4 py-2 text-left">Buku</th>
                <th class="px-4 py-2 text-left">Pinjam</th>
                <th class="px-4 py-2 text-left">Jatuh Tempo</th>
                <th class="px-4 py-2 text-left">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-if="activeLoans.length"
                v-for="loan in activeLoans"
                :key="loan.id"
                class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900"
              >
                <td class="px-4 py-2 font-medium">{{ loan.book.title }}</td>
                <td class="px-4 py-2">{{ loan.borrowed_at }}</td>
                <td class="px-4 py-2">{{ loan.due_date }}</td>
                <td class="px-4 py-2">
                  <span
                    class="px-2 py-1 rounded-full text-xs font-semibold"
                    :class="loan.is_overdue
                      ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                      : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'"
                  >
                    {{ loan.is_overdue ? `Terlambat ${loan.days_remaining} hari` : 'Aktif' }}
                  </span>
                </td>
              </tr>
              <tr v-else>
                <td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-400 italic">
                  Tidak ada peminjaman aktif.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Riwayat Peminjaman Terakhir -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Riwayat Peminjaman
          </h2>
        </div>
        <ul>
          <li
            v-if="loanHistory.length"
            v-for="loan in loanHistory"
            :key="loan.id"
            class="flex justify-between items-center px-4 py-2 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900"
          >
            <div>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ loan.book.title }}</p>
              <p class="text-sm text-gray-500">
                {{ loan.borrowed_at }} - {{ loan.returned_at }}
              </p>
            </div>
            <span class="font-semibold text-indigo-600 dark:text-indigo-400">
              Rp {{ loan.fee }}
            </span>
          </li>
          <li v-else class="px-4 py-3 text-gray-500 dark:text-gray-400 italic">
            Belum ada riwayat peminjaman.
          </li>
        </ul>
      </div>

      <!-- Rekomendasi Buku -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">
          Rekomendasi Buku
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
          <div
            v-for="book in recommendedBooks"
            :key="book.id"
            class="bg-gray-50 dark:bg-gray-900 p-2 rounded-lg text-center hover:shadow-md transition"
          >
            <img
              v-if="book.cover_path"
              :src="`/storage/${book.cover_path}`"
              class="h-24 w-full object-cover rounded mb-2"
            />
            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ book.title }}</p>
            <p class="text-xs text-gray-500">{{ book.author }}</p>
          </div>
          <div v-if="recommendedBooks.length === 0" class="col-span-full text-center text-gray-500 italic">
            Tidak ada rekomendasi buku.
          </div>
        </div>
      </div>

      <!-- Info Tambahan User -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
          Info Akun
        </h2>
        <ul class="text-gray-700 dark:text-gray-300 space-y-1">
          <li>Tanggal bergabung: {{ userStats.joined }}</li>
          <li>Hari sejak bergabung: {{ userStats.daysSinceJoin }}</li>
          <li>Rata-rata durasi pinjam: {{ userStats.avgLoanDuration }} hari</li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
