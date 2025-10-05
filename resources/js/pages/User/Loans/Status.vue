<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

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

const cancelLoan = (id: number) => {
  Swal.fire({
    title: 'Batalkan Pengajuan?',
    text: 'Apakah kamu yakin ingin membatalkan peminjaman ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Tidak',
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/user/loans/${id}/cancel`)
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Status Peminjaman" />

    <div class="p-6">
      <h1 class="text-xl font-bold mb-6">Status Peminjaman Buku</h1>

      <!-- Cards Grid -->
      <div v-if="props.loans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="loan in props.loans"
          :key="loan.id"
          class="rounded-xl shadow-md border border-gray-200 bg-white overflow-hidden flex flex-col"
        >
          <!-- Header strip by status -->
          <div
            class="h-2"
            :class="{
              'bg-yellow-500': loan.status === 'pending',
              'bg-blue-500': loan.status === 'dipinjam',
              'bg-green-500': loan.status === 'dikembalikan',
              'bg-red-500': loan.status === 'ditolak'
            }"
          ></div>

          <!-- Content -->
          <div class="p-5 flex flex-col flex-grow">
            <!-- Title -->
            <h2 class="font-semibold text-gray-800 mb-1">{{ loan.book.title }}</h2>

            <!-- Status Badge -->
            <span
              class="self-start px-3 py-1 text-xs font-semibold rounded-full mb-3"
              :class="{
                'bg-yellow-100 text-yellow-700': loan.status === 'pending',
                'bg-blue-100 text-blue-700': loan.status === 'dipinjam',
                'bg-green-100 text-green-700': loan.status === 'dikembalikan',
                'bg-red-100 text-red-700': loan.status === 'ditolak'
              }"
            >
              {{
                loan.status === 'pending' ? 'Pending' :
                loan.status === 'dipinjam' ? 'Dipinjam' :
                loan.status === 'dikembalikan' ? 'Dikembalikan' :
                loan.status === 'ditolak' ? 'Ditolak' : 'Unknown'
              }}
            </span>

            <!-- Info -->
            <div class="mt-2 space-y-1 text-sm">
              <p class="flex justify-between"><span class="text-gray-500">Pinjam:</span> <span>{{ formatDate(loan.borrowed_at) }}</span></p>
              <p class="flex justify-between"><span class="text-gray-500">Kembali:</span> <span>{{ formatDate(loan.returned_at) }}</span></p>
              <p class="flex justify-between"><span class="text-gray-500">Batas:</span> <span>{{ formatDate(loan.due_date) }}</span></p>
              <p class="flex justify-between"><span class="text-gray-500">Denda:</span> <span class="text-red-600">Rp {{ loan.fine.toLocaleString('id-ID') }}</span></p>
              <p class="flex justify-between font-semibold"><span>Total:</span> <span class="text-indigo-600">Rp {{ calculateTotal(loan).toLocaleString('id-ID') }}</span></p>
            </div>

            <!-- Actions -->
            <div class="mt-4">
              <button
                v-if="loan.status === 'pending'"
                @click="cancelLoan(loan.id)"
                class="w-full bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-2 rounded-lg"
              >
                Batalkan
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="mt-8 text-center text-gray-500 text-sm">
        Belum ada peminjaman
      </div>
    </div>
  </AppLayout>
</template>
