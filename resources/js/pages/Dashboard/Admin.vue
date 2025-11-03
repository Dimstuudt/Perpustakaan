<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  stats: Object,
  activeLoans: Array,
  popularBooks: Array,
  chartData: Array,
})
</script>

<template>
  <AppLayout>
    <Head title="Dashboard Admin" />

    <div class="p-6 space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
          Dashboard Admin
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
          Ringkasan aktivitas perpustakaan
        </p>
      </div>

      <!-- Statistik -->
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
                <th class="px-4 py-2 text-left">User</th>
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
                <td class="px-4 py-2">{{ loan.user.name }}</td>
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
                    {{ loan.is_overdue ? `Terlambat ${loan.days_overdue} hari` : 'Aktif' }}
                  </span>
                </td>
              </tr>
              <tr v-else>
                <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400 italic">
                  Tidak ada peminjaman aktif.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Buku Populer -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Buku Paling Populer
          </h2>
        </div>
        <ul>
          <li
            v-if="popularBooks.length"
            v-for="book in popularBooks"
            :key="book.book.id"
            class="flex justify-between items-center px-4 py-2 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900"
          >
            <div>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ book.book.title }}</p>
              <p class="text-sm text-gray-500">{{ book.book.author }}</p>
            </div>
            <span class="font-semibold text-indigo-600 dark:text-indigo-400">
              {{ book.count }}x
            </span>
          </li>
          <li v-else class="px-4 py-3 text-gray-500 dark:text-gray-400 italic">
            Belum ada data buku populer.
          </li>
        </ul>
      </div>

      <!-- Tren Sederhana (Text Based) -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">
          Tren Peminjaman (3 Bulan Terakhir)
        </h2>
        <div class="grid grid-cols-3 gap-4 text-center">
          <div
            v-for="month in chartData"
            :key="month.month"
            class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900"
          >
            <p class="text-sm text-gray-500">{{ month.month }}</p>
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
              {{ month.count }}
            </p>
            <p class="text-xs text-gray-500">peminjaman</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
