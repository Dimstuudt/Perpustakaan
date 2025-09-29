<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'


// PrimeVue components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'

interface Loan {
  id: number
  status: string
  borrowed_at: string | null
  returned_at: string | null
  book: { title: string }
}

// Props dari backend
const props = defineProps<{
  loans: Loan[]
}>()

const breadcrumbs = [
  { title: 'Peminjaman', href: '/user/loansuser' },
  { title: 'Status Peminjaman', href: '/user/loans/status' },
]

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

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Status Peminjaman Buku</h1>

      <DataTable
        :value="props.loans"
        class="p-datatable-gridlines p-datatable-sm"
        :paginator="true"
        :rows="10"
      >
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
        <Column header="Pinjam" style="width: 140px; text-align: center;">
          <template #body="slotProps">
            <span class="text-gray-500 text-xs">
              {{ slotProps.data.borrowed_at ?? 'Belum Pinjam' }}
            </span>
          </template>
        </Column>
        <Column header="Kembali" style="width: 140px; text-align: center;">
          <template #body="slotProps">
            <span class="text-gray-500 text-xs">
              {{ slotProps.data.returned_at ?? 'Belum Kembali' }}
            </span>
          </template>
        </Column>
        <Column header="Aksi" style="width: 120px; text-align: center;">
  <template #body="slotProps">
    <button
      v-if="slotProps.data.status === 'pending'"
      @click="cancelLoan(slotProps.data.id)"
      class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded"
    >
      Batalkan
    </button>
  </template>
</Column>

      </DataTable>

      <div v-if="props.loans.length === 0" class="mt-4 text-center text-gray-500">
        Belum ada peminjaman
      </div>
    </div>
  </AppLayout>
</template>
