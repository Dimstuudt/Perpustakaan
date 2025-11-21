<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref, watch } from 'vue'
import Button from 'primevue/button'

interface Loan {
  id: number
  status: string
  borrowed_at: string | null
  returned_at: string | null
  due_date: string | null
  fee: number
  fine: number
  user: { username: string }
  book: { title: string }
}

const props = defineProps<{
  loans: {
    data: Loan[]
    links: { url: string | null; label: string; active: boolean }[]
    meta: { current_page: number; last_page: number }
  }
  filters?: {
    status?: string
  }
  stats?: {
    pending: number
    dipinjam: number
    dikembalikan: number
    ditolak: number
  }
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Loans', href: '/loans' },
]

const selectedStatus = ref(props.filters?.status || 'all')

watch(selectedStatus, (newStatus) => {
  router.get('/loans',
    { status: newStatus === 'all' ? undefined : newStatus },
    {
      preserveState: true,
      preserveScroll: true,
      only: ['loans', 'stats']
    }
  )
})

const approve = (loan: Loan) => {
  const isDark = document.documentElement.classList.contains('dark')

  Swal.fire({
    title: 'Approve peminjaman?',
    html: `<div class="text-left">
      <strong>Buku:</strong> ${loan.book.title}<br>
      <strong>User:</strong> ${loan.user.username}
    </div>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, approve!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#10b981',
    background: isDark ? '#1f2937' : '#fff',
    color: isDark ? '#f3f4f6' : '#000',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/approve`, {}, {
        onSuccess: () => Swal.fire({
          title: 'Berhasil',
          text: 'Peminjaman disetujui.',
          icon: 'success',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        }),
        onError: () => Swal.fire({
          title: 'Gagal',
          text: 'Terjadi kesalahan.',
          icon: 'error',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        })
      })
    }
  })
}

const reject = (loan: Loan) => {
  const isDark = document.documentElement.classList.contains('dark')

  Swal.fire({
    title: 'Tolak peminjaman?',
    html: `<div class="text-left">
      <strong>Buku:</strong> ${loan.book.title}<br>
      <strong>User:</strong> ${loan.user.username}
    </div>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, tolak!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#ef4444',
    background: isDark ? '#1f2937' : '#fff',
    color: isDark ? '#f3f4f6' : '#000',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/reject`, {}, {
        onSuccess: () => Swal.fire({
          title: 'Berhasil',
          text: 'Peminjaman ditolak.',
          icon: 'success',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        }),
        onError: () => Swal.fire({
          title: 'Gagal',
          text: 'Terjadi kesalahan.',
          icon: 'error',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        })
      })
    }
  })
}

const returned = (loan: Loan) => {
  const isDark = document.documentElement.classList.contains('dark')

  Swal.fire({
    title: 'Konfirmasi pengembalian?',
    html: `<div class="text-left">
      <strong>Buku:</strong> ${loan.book.title}<br>
      <strong>User:</strong> ${loan.user.username}
    </div>`,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Ya, terima!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#3b82f6',
    background: isDark ? '#1f2937' : '#fff',
    color: isDark ? '#f3f4f6' : '#000',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/return`, {}, {
        onSuccess: () => Swal.fire({
          title: 'Berhasil',
          text: 'Buku dikembalikan.',
          icon: 'success',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        }),
        onError: () => Swal.fire({
          title: 'Gagal',
          text: 'Terjadi kesalahan.',
          icon: 'error',
          background: isDark ? '#1f2937' : '#fff',
          color: isDark ? '#f3f4f6' : '#000',
        })
      })
    }
  })
}

const formatDate = (date: string | null) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatPrice = (price: any) => {
  const numPrice = Number(price) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(numPrice)
}

const getStatusClass = (status: string) => {
  const classes = {
    pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
    dipinjam: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-700',
    dikembalikan: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-700',
    ditolak: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-700'
  }
  return classes[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-600'
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Daftar Peminjaman" />

    <div class="p-6 mx-auto" style="max-width: 100%; width: 100%;">
      <!-- Header -->
      <div class="mb-6 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">Manajemen Peminjaman</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Kelola dan pantau semua aktivitas peminjaman buku</p>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Pending</p>
              <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats?.pending || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <i class="pi pi-clock text-xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Dipinjam</p>
              <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats?.dipinjam || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <i class="pi pi-book text-xl text-blue-600 dark:text-blue-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Dikembalikan</p>
              <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats?.dikembalikan || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="pi pi-check-circle text-xl text-green-600 dark:text-green-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Ditolak</p>
              <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats?.ditolak || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
              <i class="pi pi-times-circle text-xl text-red-600 dark:text-red-400"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Filter -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 max-w-7xl mx-auto">
        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
          <button
            @click="selectedStatus = 'all'"
            class="px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap"
            :class="selectedStatus === 'all'
              ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <i class="pi pi-list mr-2"></i>
            Semua ({{ (stats?.pending || 0) + (stats?.dipinjam || 0) + (stats?.dikembalikan || 0) + (stats?.ditolak || 0) }})
          </button>
          <button
            @click="selectedStatus = 'pending'"
            class="px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap"
            :class="selectedStatus === 'pending'
              ? 'text-yellow-600 dark:text-yellow-400 border-b-2 border-yellow-600 dark:border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <i class="pi pi-clock mr-2"></i>
            Pending ({{ stats?.pending || 0 }})
          </button>
          <button
            @click="selectedStatus = 'dipinjam'"
            class="px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap"
            :class="selectedStatus === 'dipinjam'
              ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <i class="pi pi-book mr-2"></i>
            Dipinjam ({{ stats?.dipinjam || 0 }})
          </button>
          <button
            @click="selectedStatus = 'dikembalikan'"
            class="px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap"
            :class="selectedStatus === 'dikembalikan'
              ? 'text-green-600 dark:text-green-400 border-b-2 border-green-600 dark:border-green-400 bg-green-50 dark:bg-green-900/20'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <i class="pi pi-check-circle mr-2"></i>
            Dikembalikan ({{ stats?.dikembalikan || 0 }})
          </button>
          <button
            @click="selectedStatus = 'ditolak'"
            class="px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap"
            :class="selectedStatus === 'ditolak'
              ? 'text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 bg-red-50 dark:bg-red-900/20'
              : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <i class="pi pi-times-circle mr-2"></i>
            Ditolak ({{ stats?.ditolak || 0 }})
          </button>
        </div>
      </div>

      <!-- Table - FULL WIDTH, break dari container -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 -mx-6" style="width: calc(100% + 3rem);">
        <div class="w-full" style="overflow-x: scroll !important; -webkit-overflow-scrolling: touch;">
          <table class="border-collapse" style="width: 100%; min-width: 1350px; table-layout: fixed;">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
              <tr>
                <th style="width: 60px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ID</th>
                <th style="width: 150px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Peminjam</th>
                <th style="width: 200px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Judul Buku</th>
                <th style="width: 120px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tgl Pinjam</th>
                <th style="width: 120px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Batas Kembali</th>
                <th style="width: 120px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tgl Kembali</th>
                <th style="width: 110px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Biaya</th>
                <th style="width: 110px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Denda</th>
                <th style="width: 120px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                <th style="width: 120px;" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                <th style="width: 120px;" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-if="props.loans.data.length === 0">
                <td colspan="11" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <i class="pi pi-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data peminjaman</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Belum ada peminjaman dengan status "{{ selectedStatus === 'all' ? 'apapun' : selectedStatus }}"</p>
                  </div>
                </td>
              </tr>
              <tr
                v-for="loan in props.loans.data"
                :key="loan.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
              >
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium whitespace-nowrap">#{{ loan.id }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 whitespace-nowrap">
                  <div class="flex items-center">
                    <i class="pi pi-user text-gray-400 dark:text-gray-500 mr-2 text-xs"></i>
                    {{ loan.user.username }}
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium" style="width: 200px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="loan.book.title">
                  {{ loan.book.title }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ formatDate(loan.borrowed_at) }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ formatDate(loan.due_date) }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ formatDate(loan.returned_at) }}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ formatPrice(loan.fee) }}</td>
                <td class="px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 whitespace-nowrap">{{ formatPrice(loan.fine) }}</td>
                <td class="px-4 py-3 text-sm font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                  {{ formatPrice((Number(loan.fee) || 0) + (Number(loan.fine) || 0)) }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border"
                    :class="getStatusClass(loan.status)"
                  >
                    {{ loan.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                  <div class="flex items-center justify-center gap-2">
                    <template v-if="loan.status === 'pending'">
                      <Button
                        icon="pi pi-check"
                        severity="success"
                        size="small"
                        rounded
                        title="Setujui"
                        @click="approve(loan)"
                      />
                      <Button
                        icon="pi pi-times"
                        severity="danger"
                        size="small"
                        rounded
                        outlined
                        title="Tolak"
                        @click="reject(loan)"
                      />
                    </template>
                    <Button
                      v-else-if="loan.status === 'dipinjam'"
                      icon="pi pi-undo"
                      severity="info"
                      size="small"
                      rounded
                      title="Terima Pengembalian"
                      @click="returned(loan)"
                    />
                    <span v-else class="text-xs text-gray-400 dark:text-gray-600 italic">-</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="props.loans.links.length > 3" class="flex justify-center items-center mt-6 gap-2 max-w-7xl mx-auto">
        <Link
          v-for="link in props.loans.links"
          :key="link.label"
          :href="link.url || ''"
          class="px-3 py-2 rounded-md text-sm font-medium transition-all"
          :class="{
            'bg-indigo-600 dark:bg-indigo-500 text-white': link.active,
            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600': !link.active && link.url,
            'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed': !link.url
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Force horizontal scroll */
div[style*="overflow-x: scroll"] {
  display: block !important;
  overflow-x: scroll !important;
  overflow-y: visible !important;
  width: 100% !important;
}

/* Custom Scrollbar - lebih gede */
div[style*="overflow-x: scroll"]::-webkit-scrollbar {
  height: 14px !important;
}

div[style*="overflow-x: scroll"]::-webkit-scrollbar-track {
  background: #e2e8f0 !important;
  border-radius: 6px !important;
}

.dark div[style*="overflow-x: scroll"]::-webkit-scrollbar-track {
  background: #374151 !important;
}

div[style*="overflow-x: scroll"]::-webkit-scrollbar-thumb {
  background: #64748b !important;
  border-radius: 6px !important;
  border: 2px solid #e2e8f0 !important;
}

.dark div[style*="overflow-x: scroll"]::-webkit-scrollbar-thumb {
  background: #6b7280 !important;
  border: 2px solid #374151 !important;
}

div[style*="overflow-x: scroll"]::-webkit-scrollbar-thumb:hover {
  background: #475569 !important;
}

.dark div[style*="overflow-x: scroll"]::-webkit-scrollbar-thumb:hover {
  background: #9ca3af !important;
}

/* Table styles */
table {
  border-collapse: collapse !important;
  table-layout: fixed !important;
}

table td,
table th {
  white-space: nowrap !important;
  overflow: hidden !important;
}

/* Khusus kolom judul buku */
table td:nth-child(3) {
  text-overflow: ellipsis !important;
  overflow: hidden !important;
  white-space: nowrap !important;
  max-width: 200px !important;
}
</style>
