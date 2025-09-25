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

interface Book {
  id: number
  title: string
  author: string
  stock: number
}

// Props dari backend: daftar buku
const props = defineProps<{
  books: Book[]
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Ajukan Peminjaman', href: '/user/loansuser' },
]

// Action pinjam buku pakai SweetAlert
const pinjam = (book: Book) => {
  Swal.fire({
    title: 'Ajukan Peminjaman?',
    text: `Buku: ${book.title}\nPenulis: ${book.author}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, pinjam!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('user.loans.store'), { book_id: book.id }, {
        onSuccess: () => Swal.fire('Berhasil', 'Permintaan peminjaman terkirim.', 'success'),
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Ajukan Peminjaman" />

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Ajukan Peminjaman Buku</h1>

      <DataTable
        :value="props.books"
        class="p-datatable-gridlines p-datatable-sm"
      >
        <Column field="title" header="Judul Buku" />
        <Column field="author" header="Penulis" />
        <Column header="Stok">
          <template #body="slotProps">
            <Tag
              :value="slotProps.data.stock > 0 ? slotProps.data.stock : 'Habis'"
              :severity="slotProps.data.stock > 0 ? 'success' : 'danger'"
            />
          </template>
        </Column>
        <Column header="Aksi">
          <template #body="slotProps">
            <Button
              label="Pinjam"
              size="small"
              severity="info"
              :disabled="slotProps.data.stock === 0"
              @click="pinjam(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>

      <div v-if="props.books.length === 0" class="mt-4 text-center text-gray-500">
        Tidak ada buku tersedia
      </div>
    </div>
  </AppLayout>
</template>
