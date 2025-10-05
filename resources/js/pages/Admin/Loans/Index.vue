<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref, computed } from 'vue'
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

// Props dari backend dengan pagination
const props = defineProps<{
  loans: {
    data: Loan[]
    links: { url: string | null; label: string; active: boolean }[]
    meta: { current_page: number; last_page: number }
  }
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Loans', href: '/loans' },
]

// === Filter & Prioritas ===
const selectedStatus = ref('all')
const statusPriority: Record<string, number> = {
  pending: 1,
  dipinjam: 2,
  dikembalikan: 3,
  ditolak: 4,
}

const filteredLoans = computed(() => {
  return props.loans.data
    .filter(loan => selectedStatus.value === 'all' || loan.status === selectedStatus.value)
    .sort((a, b) => statusPriority[a.status] - statusPriority[b.status])
})

// === Actions ===
const approve = (loan: Loan) => {
  Swal.fire({
    title: 'Approve peminjaman?',
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.username}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, approve!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/loans/${loan.id}/approve`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Peminjaman disetujui.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

const reject = (loan: Loan) => {
  Swal.fire({
    title: 'Tolak peminjaman?',
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.username}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, tolak!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/loans/${loan.id}/reject`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Peminjaman ditolak.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

const returned = (loan: Loan) => {
  Swal.fire({
    title: 'Konfirmasi pengembalian?',
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.username}`,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Ya, terima!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/loans/${loan.id}/return`, {}, {
        onSuccess: () => Swal.fire('Berhasil', 'Buku dikembalikan.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

// Format tanggal
const formatDate = (date: string | null) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Daftar Peminjaman" />

    <div class="p-6">
      <!-- Header + Filter -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Daftar Peminjaman</h1>
        <select
          v-model="selectedStatus"
          class="border rounded-lg px-3 py-2 text-sm text-gray-700 shadow-sm"
        >
          <option value="all">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="dipinjam">Dipinjam</option>
          <option value="ditolak">Ditolak</option>
          <option value="dikembalikan">Dikembalikan</option>
        </select>
      </div>

      <!-- Cards -->
      <div v-if="filteredLoans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="loan in filteredLoans"
          :key="loan.id"
          class="rounded-xl shadow-lg overflow-hidden border border-gray-200 bg-white flex flex-col"
        >
          <!-- Header Color Strip -->
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
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 text-gray-600">
                <i class="pi pi-book text-xl"></i>
              </div>
              <div>
                <h2 class="font-semibold text-gray-800 truncate">{{ loan.book.title }}</h2>
                <p class="text-sm text-gray-500">oleh {{ loan.user.username }}</p>
              </div>
            </div>

            <div class="mt-3">
              <span
                class="px-3 py-1 text-xs font-semibold rounded-full"
                :class="{
                  'bg-yellow-100 text-yellow-800': loan.status === 'pending',
                  'bg-blue-100 text-blue-800': loan.status === 'dipinjam',
                  'bg-green-100 text-green-800': loan.status === 'dikembalikan',
                  'bg-red-100 text-red-800': loan.status === 'ditolak'
                }"
              >
                {{ loan.status }}
              </span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-y-1 text-sm">
              <p class="text-gray-500">Fee</p>
              <p class="text-gray-800">Rp {{ loan.fee.toLocaleString('id-ID') }}</p>

              <p class="text-gray-500">Denda</p>
              <p class="text-red-600">Rp {{ loan.fine.toLocaleString('id-ID') }}</p>

              <p class="text-gray-500 font-medium">Total</p>
              <p class="font-bold text-indigo-600">Rp {{ (loan.fee + loan.fine).toLocaleString('id-ID') }}</p>

              <p class="text-gray-500">Batas</p>
              <p>{{ formatDate(loan.due_date) }}</p>

              <p class="text-gray-500">Pinjam</p>
              <p>{{ formatDate(loan.borrowed_at) }}</p>

              <p class="text-gray-500">Kembali</p>
              <p>{{ formatDate(loan.returned_at) }}</p>
            </div>

            <!-- Actions -->
            <div class="mt-5 flex gap-2">
              <Button
                v-if="loan.status === 'pending'"
                icon="pi pi-check"
                label="Approve"
                severity="success"
                size="small"
                rounded
                class="flex-1"
                @click="approve(loan)"
              />
              <Button
                v-if="loan.status === 'pending'"
                icon="pi pi-times"
                label="Reject"
                severity="danger"
                size="small"
                rounded
                outlined
                class="flex-1"
                @click="reject(loan)"
              />
              <Button
                v-if="loan.status === 'dipinjam'"
                icon="pi pi-undo"
                label="Kembali"
                severity="info"
                size="small"
                rounded
                class="w-full"
                @click="returned(loan)"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="mt-4 text-center text-gray-500">
        Belum ada peminjaman
      </div>

      <!-- Pagination -->
      <div v-if="props.loans.links.length > 3" class="flex justify-center mt-6 space-x-2">
        <Link
          v-for="link in props.loans.links"
          :key="link.label"
          :href="link.url || ''"
          class="px-3 py-1 rounded text-sm"
          :class="{
            'bg-indigo-500 text-white': link.active,
            'text-gray-500 cursor-not-allowed': !link.url
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>
