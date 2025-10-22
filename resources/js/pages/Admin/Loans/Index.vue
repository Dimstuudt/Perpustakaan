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
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/approve`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Peminjaman disetujui.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

const reject = (loan: Loan) => {
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
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/reject`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Peminjaman ditolak.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

const returned = (loan: Loan) => {
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
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/loans/${loan.id}/return`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Buku dikembalikan.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
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

// Format harga tanpa desimal (1000 bukan 1000,00)
const formatPrice = (price: any) => {
  const numPrice = Number(price) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(numPrice)
}

const getStatusIcon = (status: string) => {
  const icons = {
    pending: 'pi-clock',
    dipinjam: 'pi-book',
    dikembalikan: 'pi-check-circle',
    ditolak: 'pi-times-circle'
  }
  return icons[status] || 'pi-circle'
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Daftar Peminjaman" />

    <div class="p-6 max-w-7xl mx-auto">
      <!-- Header Section -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Peminjaman</h1>
            <p class="text-gray-600">Kelola dan pantau semua aktivitas peminjaman buku</p>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <button
            @click="selectedStatus = 'pending'"
            class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
            :class="{ 'ring-4 ring-yellow-300': selectedStatus === 'pending' }"
          >
            <div class="flex items-center justify-between">
              <div class="text-left">
                <p class="text-yellow-100 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold mt-1">{{ stats?.pending || 0 }}</p>
              </div>
              <i class="pi pi-clock text-3xl opacity-80"></i>
            </div>
          </button>

          <button
            @click="selectedStatus = 'dipinjam'"
            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
            :class="{ 'ring-4 ring-blue-300': selectedStatus === 'dipinjam' }"
          >
            <div class="flex items-center justify-between">
              <div class="text-left">
                <p class="text-blue-100 text-sm font-medium">Dipinjam</p>
                <p class="text-3xl font-bold mt-1">{{ stats?.dipinjam || 0 }}</p>
              </div>
              <i class="pi pi-book text-3xl opacity-80"></i>
            </div>
          </button>

          <button
            @click="selectedStatus = 'dikembalikan'"
            class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
            :class="{ 'ring-4 ring-green-300': selectedStatus === 'dikembalikan' }"
          >
            <div class="flex items-center justify-between">
              <div class="text-left">
                <p class="text-green-100 text-sm font-medium">Dikembalikan</p>
                <p class="text-3xl font-bold mt-1">{{ stats?.dikembalikan || 0 }}</p>
              </div>
              <i class="pi pi-check-circle text-3xl opacity-80"></i>
            </div>
          </button>

          <button
            @click="selectedStatus = 'ditolak'"
            class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
            :class="{ 'ring-4 ring-red-300': selectedStatus === 'ditolak' }"
          >
            <div class="flex items-center justify-between">
              <div class="text-left">
                <p class="text-red-100 text-sm font-medium">Ditolak</p>
                <p class="text-3xl font-bold mt-1">{{ stats?.ditolak || 0 }}</p>
              </div>
              <i class="pi pi-times-circle text-3xl opacity-80"></i>
            </div>
          </button>
        </div>

        <!-- Filter Bar -->
        <div class="flex items-center gap-3 bg-white rounded-xl shadow-sm p-4 border border-gray-200">
          <i class="pi pi-filter text-gray-500"></i>
          <select
            v-model="selectedStatus"
            class="flex-1 border-0 focus:ring-2 focus:ring-indigo-500 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50"
          >
            <option value="all">🔍 Tampilkan Semua Status</option>
            <option value="pending">⏳ Pending</option>
            <option value="dipinjam">📖 Dipinjam</option>
            <option value="dikembalikan">✅ Dikembalikan</option>
            <option value="ditolak">❌ Ditolak</option>
          </select>
          <button
            v-if="selectedStatus !== 'all'"
            @click="selectedStatus = 'all'"
            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Loan Cards with Enhanced Design -->
      <div v-if="props.loans.data.length > 0" class="space-y-4">
        <div
          v-for="loan in props.loans.data"
          :key="loan.id"
          class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all border border-gray-200 overflow-hidden group"
        >
          <div class="flex flex-col md:flex-row">
            <!-- Left Side - Status Indicator & Icon -->
            <div
              class="w-full md:w-48 p-6 flex items-center justify-center relative"
              :class="{
                'bg-gradient-to-br from-yellow-50 to-yellow-100': loan.status === 'pending',
                'bg-gradient-to-br from-blue-50 to-blue-100': loan.status === 'dipinjam',
                'bg-gradient-to-br from-green-50 to-green-100': loan.status === 'dikembalikan',
                'bg-gradient-to-br from-red-50 to-red-100': loan.status === 'ditolak'
              }"
            >
              <div class="text-center">
                <div
                  class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-3 shadow-lg"
                  :class="{
                    'bg-yellow-400 text-yellow-900': loan.status === 'pending',
                    'bg-blue-500 text-white': loan.status === 'dipinjam',
                    'bg-green-500 text-white': loan.status === 'dikembalikan',
                    'bg-red-500 text-white': loan.status === 'ditolak'
                  }"
                >
                  <i :class="`pi ${getStatusIcon(loan.status)} text-3xl`"></i>
                </div>
                <span
                  class="px-4 py-1.5 text-xs font-bold rounded-full uppercase tracking-wider"
                  :class="{
                    'bg-yellow-400 text-yellow-900': loan.status === 'pending',
                    'bg-blue-500 text-white': loan.status === 'dipinjam',
                    'bg-green-500 text-white': loan.status === 'dikembalikan',
                    'bg-red-500 text-white': loan.status === 'ditolak'
                  }"
                >
                  {{ loan.status }}
                </span>
              </div>
            </div>

            <!-- Right Side - Content -->
            <div class="flex-1 p-6">
              <!-- Book & User Info -->
              <div class="mb-4">
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                  {{ loan.book.title }}
                </h3>
                <div class="flex items-center gap-2 text-gray-600">
                  <i class="pi pi-user text-sm"></i>
                  <span class="text-sm font-medium">{{ loan.user.username }}</span>
                </div>
              </div>

              <!-- Details Grid -->
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                  <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                    <i class="pi pi-calendar"></i> Tanggal Pinjam
                  </p>
                  <p class="text-sm font-semibold text-gray-900">{{ formatDate(loan.borrowed_at) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                  <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                    <i class="pi pi-calendar-times"></i> Batas Kembali
                  </p>
                  <p class="text-sm font-semibold text-gray-900">{{ formatDate(loan.due_date) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                  <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                    <i class="pi pi-check"></i> Dikembalikan
                  </p>
                  <p class="text-sm font-semibold text-gray-900">{{ formatDate(loan.returned_at) }}</p>
                </div>
              </div>

              <!-- Payment Info -->
              <div class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 mb-4">
                <div class="flex gap-6">
                  <div>
                    <p class="text-xs text-gray-600 mb-1">Biaya Pinjam</p>
                    <p class="text-lg font-bold text-indigo-600">{{ formatPrice(loan.fee) }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-600 mb-1">Denda</p>
                    <p class="text-lg font-bold text-red-600">{{ formatPrice(loan.fine) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-600 mb-1">Total Bayar</p>
                  <p class="text-2xl font-bold text-purple-600">{{ formatPrice((Number(loan.fee) || 0) + (Number(loan.fine) || 0)) }}</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-3">
                <template v-if="loan.status === 'pending'">
                  <Button
                    icon="pi pi-check"
                    label="Setujui"
                    severity="success"
                    size="small"
                    class="flex-1"
                    @click="approve(loan)"
                  />
                  <Button
                    icon="pi pi-times"
                    label="Tolak"
                    severity="danger"
                    size="small"
                    outlined
                    class="flex-1"
                    @click="reject(loan)"
                  />
                </template>
                <Button
                  v-if="loan.status === 'dipinjam'"
                  icon="pi pi-undo"
                  label="Terima Pengembalian"
                  severity="info"
                  size="small"
                  class="w-full"
                  @click="returned(loan)"
                />
                <div v-if="loan.status === 'dikembalikan' || loan.status === 'ditolak'" class="text-center w-full py-2 text-sm text-gray-500 italic">
                  {{ loan.status === 'dikembalikan' ? 'Peminjaman telah selesai' : 'Peminjaman ditolak' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
          <i class="pi pi-inbox text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak ada data</h3>
        <p class="text-gray-500">Belum ada peminjaman dengan status "{{ selectedStatus === 'all' ? 'apapun' : selectedStatus }}"</p>
      </div>

      <!-- Pagination -->
      <div v-if="props.loans.links.length > 3" class="flex justify-center mt-8 gap-2">
        <Link
          v-for="link in props.loans.links"
          :key="link.label"
          :href="link.url || ''"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
          :class="{
            'bg-indigo-600 text-white shadow-lg': link.active,
            'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border border-gray-200': !link.active && link.url,
            'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.group:hover .group-hover\:text-indigo-600 {
  transition: color 0.3s ease;
}
</style>
