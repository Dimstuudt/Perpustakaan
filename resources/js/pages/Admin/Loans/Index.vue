<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'

// PrimeVue components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Tag from 'primevue/tag'

interface Loan {
  id: number
  status: string
  borrowed_at: string | null
  returned_at: string | null
  user: { name: string }
  book: { title: string }
 fee: number // ✅ tambahin fee
}

// Props dari backend
const props = defineProps<{
  loans: Loan[]
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Loans', href: '/loans' },
]

// Pagination
const first = ref(0)
const rows = ref(10)
const onPage = (event: { first: number; rows: number; page: number }) => {
  const pageNumber = event.page + 1
  router.get(route('loans.index'), { page: pageNumber, per_page: event.rows }, { preserveState: true, replace: true })
  first.value = event.first
  rows.value = event.rows
}

// Actions with SweetAlert
const approve = (loan: Loan) => {
  Swal.fire({
    title: 'Approve peminjaman?',
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.name}`,
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
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.name}`,
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
    text: `Buku: ${loan.book.title}\nUser: ${loan.user.name}`,
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Daftar Peminjaman" />

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Daftar Peminjaman</h1>

      <DataTable
        :value="props.loans"
        :paginator="true"
        :first="first"
        :rows="rows"
        :totalRecords="props.loans.length"
        :rowsPerPageOptions="[5,10,20]"
        @page="onPage"
        class="p-datatable-gridlines p-datatable-sm"
      >
        <Column field="user.name" header="User" />
        <Column field="book.title" header="Buku" />
      <Column header="Status" style="text-align: center; width: 120px;">
  <template #body="slotProps">
    <span
      class="inline-block w-full px-2 py-1 text-xs font-semibold rounded-full text-white text-center"
      :class="{
        'bg-yellow-500': slotProps.data.status === 'pending',
        'bg-blue-500': slotProps.data.status === 'dipinjam',
        'bg-green-500': slotProps.data.status === 'dikembalikan',
        'bg-red-500': slotProps.data.status === 'ditolak'
      }"
    >
      {{
        slotProps.data.status === 'pending' ? 'Pending' :
        slotProps.data.status === 'dipinjam' ? 'Dipinjam' :
        slotProps.data.status === 'dikembalikan' ? 'Dikembalikan' :
        slotProps.data.status === 'ditolak' ? 'Ditolak' : 'Unknown'
      }}
    </span>
  </template>
</Column>

<Column header="Fee" style="width: 120px; text-align: left;">
  <template #body="slotProps">
    Rp {{ slotProps.data.fee.toLocaleString('id-ID') }}
  </template>
</Column>


       <!-- Tanggal pinjam -->
<Column header="Pinjam" style="width: 140px; text-align: center;">
  <template #body="slotProps">
    <span class="text-gray-500 text-xs">
      {{ slotProps.data.borrowed_at ? slotProps.data.borrowed_at : 'Belum Pinjam' }}
    </span>
  </template>
</Column>

<!-- Tanggal kembali -->
<Column header="Kembali" style="width: 140px; text-align: center;">
  <template #body="slotProps">
    <span class="text-gray-500 text-xs">
      {{ slotProps.data.returned_at ? slotProps.data.returned_at : 'Belum Kembali' }}
    </span>
  </template>
</Column>

        <Column header="Aksi">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Button
                v-if="slotProps.data.status === 'pending'"
                label="Approve"
                severity="success"
                size="small"
                @click="approve(slotProps.data)"
              />
              <Button
                v-if="slotProps.data.status === 'pending'"
                label="Reject"
                severity="danger"
                size="small"
                @click="reject(slotProps.data)"
              />
              <Button
                v-if="slotProps.data.status === 'dipinjam'"
                label="Terima Pengembalian"
                severity="info"
                size="small"
                @click="returned(slotProps.data)"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <div v-if="props.loans.length === 0" class="mt-4 text-center text-gray-500">
        Belum ada peminjaman
      </div>
    </div>
  </AppLayout>
</template>
