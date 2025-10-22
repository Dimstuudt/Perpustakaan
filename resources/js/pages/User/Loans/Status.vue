<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { computed } from 'vue'

interface Loan {
  id: number
  status: string
  borrowed_at: string | null
  returned_at: string | null
  due_date: string | null
  fee: number
  fine: number
  book: { title: string }
}

const props = defineProps<{ loans: Loan[] }>()

const breadcrumbs = [
  { title: 'Peminjaman', href: '/user/loansuser' },
  { title: 'Status Peminjaman', href: '/user/loans/status' },
]

const calculateTotal = (loan: Loan) => loan.fee + loan.fine

// Group loans by status
const groupedLoans = computed(() => {
  const groups = {
    pending: [] as Loan[],
    dipinjam: [] as Loan[],
    dikembalikan: [] as Loan[],
    ditolak: [] as Loan[]
  }

  props.loans.forEach(loan => {
    if (groups[loan.status]) {
      groups[loan.status].push(loan)
    }
  })

  return groups
})

const statusConfig = {
  pending: {
    label: 'Menunggu Persetujuan',
    icon: 'pi-clock',
    gradient: 'from-amber-400 via-yellow-500 to-orange-500',
    bgLight: 'bg-amber-50',
    textColor: 'text-amber-900',
    iconBg: 'bg-amber-100'
  },
  dipinjam: {
    label: 'Sedang Dipinjam',
    icon: 'pi-bookmark',
    gradient: 'from-blue-400 via-blue-500 to-indigo-600',
    bgLight: 'bg-blue-50',
    textColor: 'text-blue-900',
    iconBg: 'bg-blue-100'
  },
  dikembalikan: {
    label: 'Telah Dikembalikan',
    icon: 'pi-check-circle',
    gradient: 'from-emerald-400 via-green-500 to-teal-600',
    bgLight: 'bg-green-50',
    textColor: 'text-green-900',
    iconBg: 'bg-green-100'
  },
  ditolak: {
    label: 'Ditolak',
    icon: 'pi-times-circle',
    gradient: 'from-rose-400 via-red-500 to-pink-600',
    bgLight: 'bg-red-50',
    textColor: 'text-red-900',
    iconBg: 'bg-red-100'
  }
}

const cancelLoan = (id: number, title: string) => {
  Swal.fire({
    title: 'Batalkan Pengajuan?',
    html: `<div class="text-left mt-2"><strong>Buku:</strong> ${title}</div>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Tidak',
    confirmButtonColor: '#ef4444',
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/user/loans/${id}/cancel`, {}, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Peminjaman telah dibatalkan.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal', 'Terjadi kesalahan saat membatalkan.', 'error')
        }
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

const isOverdue = (dueDate: string | null, status: string) => {
  if (!dueDate || status !== 'dipinjam') return false
  return new Date(dueDate) < new Date()
}

const formatRupiah = (value: any) => {
  const num = parseFloat(value)
  if (isNaN(num)) return '0' // kalau null/string kosong
  return num.toLocaleString('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Status Peminjaman" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Status Peminjaman</h1>
          <p class="text-gray-600">Pantau semua aktivitas peminjaman buku Anda</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Pending</p>
                <p class="text-2xl font-bold text-amber-600">{{ groupedLoans.pending.length }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center">
                <i class="pi pi-clock text-amber-600 text-xl"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600">{{ groupedLoans.dipinjam.length }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                <i class="pi pi-bookmark text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Selesai</p>
                <p class="text-2xl font-bold text-green-600">{{ groupedLoans.dikembalikan.length }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                <i class="pi pi-check-circle text-green-600 text-xl"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-600 mb-1">Ditolak</p>
                <p class="text-2xl font-bold text-red-600">{{ groupedLoans.ditolak.length }}</p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                <i class="pi pi-times-circle text-red-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Loans by Status -->
        <div v-if="props.loans.length > 0" class="space-y-8">
          <div v-for="(loans, status) in groupedLoans" :key="status" v-show="loans.length > 0">
            <!-- Section Header -->
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                :class="`bg-gradient-to-br ${statusConfig[status].gradient}`"
              >
                <i :class="`pi ${statusConfig[status].icon} text-white text-lg`"></i>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900">{{ statusConfig[status].label }}</h2>
                <p class="text-sm text-gray-500">{{ loans.length }} buku</p>
              </div>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="loan in loans"
                :key="loan.id"
                class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100"
              >
                <!-- Card Header with Gradient -->
                <div
                  class="h-1.5"
                  :class="`bg-gradient-to-r ${statusConfig[status].gradient}`"
                ></div>

                <div class="p-5">
                  <!-- Book Title & Icon -->
                  <div class="flex items-start gap-3 mb-4">
                    <div
                      class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center shadow-sm"
                      :class="statusConfig[status].iconBg"
                    >
                      <i class="pi pi-book text-xl" :class="statusConfig[status].textColor"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="font-bold text-gray-900 text-base leading-tight mb-1 group-hover:text-indigo-600 transition-colors line-clamp-2">
                        {{ loan.book.title }}
                      </h3>
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                        :class="`${statusConfig[status].bgLight} ${statusConfig[status].textColor}`"
                      >
                        {{ statusConfig[status].label }}
                      </span>
                    </div>
                  </div>

                  <!-- Overdue Warning -->
                  <div
                    v-if="isOverdue(loan.due_date, loan.status)"
                    class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2"
                  >
                    <i class="pi pi-exclamation-triangle text-red-600 text-sm"></i>
                    <span class="text-xs font-medium text-red-700">Terlambat dikembalikan!</span>
                  </div>

                  <!-- Info Grid -->
                  <div class="space-y-2.5 mb-4">
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="pi pi-calendar text-xs"></i>
                        Pinjam
                      </span>
                      <span class="font-medium text-gray-900">{{ formatDate(loan.borrowed_at) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="pi pi-calendar-times text-xs"></i>
                        Batas
                      </span>
                      <span class="font-medium" :class="isOverdue(loan.due_date, loan.status) ? 'text-red-600' : 'text-gray-900'">
                        {{ formatDate(loan.due_date) }}
                      </span>
                    </div>

                    <div v-if="loan.returned_at" class="flex items-center justify-between text-sm">
                      <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="pi pi-check text-xs"></i>
                        Kembali
                      </span>
                      <span class="font-medium text-gray-900">{{ formatDate(loan.returned_at) }}</span>
                    </div>

                    <div class="h-px bg-gray-100 my-2"></div>

                    <!-- Payment Info -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-3 space-y-2">
                      <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Biaya</span>
                        <span class="font-semibold text-gray-900">Rp {{ loan.fee.toLocaleString('id-ID') }}</span>
                      </div>

                 <div v-if="parseFloat(loan.fine) > 0" class="flex items-center justify-between text-sm">
  <span class="text-red-600 flex items-center gap-1">
    <i class="pi pi-exclamation-circle text-xs"></i>
    Denda
  </span>
  <span class="font-semibold text-red-600">
    Rp {{ formatRupiah(loan.fine) }}
  </span>
</div>

<div class="h-px bg-indigo-200/50"></div>

<div class="flex items-center justify-between">
  <span class="text-sm font-medium text-gray-700">Total</span>
  <span class="text-lg font-bold text-indigo-600">
    Rp {{ formatRupiah(calculateTotal(loan)) }}
  </span>
</div>

                    </div>
                  </div>

                  <!-- Action Button -->
                  <button
                    v-if="loan.status === 'pending'"
                    @click="cancelLoan(loan.id, loan.book.title)"
                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2"
                  >
                    <i class="pi pi-times text-sm"></i>
                    Batalkan Pengajuan
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-20">
          <div class="w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-6 shadow-lg">
            <i class="pi pi-book text-5xl text-indigo-400"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Peminjaman</h3>
          <p class="text-gray-500 mb-6 text-center max-w-md">
            Anda belum memiliki riwayat peminjaman buku. Mulai jelajahi koleksi kami dan ajukan peminjaman!
          </p>
          <a
            href="/user/loansuser"
            class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
          >
            Jelajahi Buku
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
