<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

// PrimeVue components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

interface Loan {
  id: number
  status: string
  borrowed_at: string | null
  returned_at: string | null
  due_date: string | null
  fee: number
  fine: number           // ✅ ambil dari backend
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Status Peminjaman" />

    <div class="p-4">
      <h1 class="text-lg font-semibold mb-4">Status Peminjaman Buku</h1>

      <div class="overflow-x-auto">
        <DataTable
          :value="props.loans"
          class="p-datatable-gridlines p-datatable-sm text-sm"
          :paginator="true"
          :rows="10"
        >
          <Column field="book.title" header="Buku" style="min-width: 150px" />

          <Column header="Status" style="width: 100px; text-align: center;">
            <template #body="slotProps">
              <span
                class="inline-block w-full px-1 py-0.5 text-xs font-semibold rounded-full text-white text-center"
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

          <Column header="Pinjam" style="width: 110px; text-align: center;">
            <template #body="slotProps">
              <span class="text-gray-500 text-xs">{{ slotProps.data.borrowed_at ?? '-' }}</span>
            </template>
          </Column>

          <Column header="Kembali" style="width: 110px; text-align: center;">
            <template #body="slotProps">
              <span class="text-gray-500 text-xs">{{ slotProps.data.returned_at ?? '-' }}</span>
            </template>
          </Column>

          <Column header="Batas Kembali" style="width: 110px; text-align: center;">
            <template #body="slotProps">
              <span class="text-gray-500 text-xs">{{ slotProps.data.due_date ?? '-' }}</span>
            </template>
          </Column>

          <Column header="Denda" style="width: 90px; text-align: right;">
            <template #body="slotProps">
              Rp {{ slotProps.data.fine.toLocaleString('id-ID') }}
            </template>
          </Column>

          <Column header="Total" style="width: 90px; text-align: right;">
            <template #body="slotProps">
              Rp {{ calculateTotal(slotProps.data).toLocaleString('id-ID') }}
            </template>
          </Column>

          <Column header="Aksi" style="width: 90px; text-align: center;">
            <template #body="slotProps">
              <button
                v-if="slotProps.data.status === 'pending'"
                @click="cancelLoan(slotProps.data.id)"
                class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded"
              >
                Batalkan
              </button>
            </template>
          </Column>
        </DataTable>
      </div>

      <div v-if="props.loans.length === 0" class="mt-4 text-center text-gray-500 text-sm">
        Belum ada peminjaman
      </div>
    </div>
  </AppLayout>
</template>
