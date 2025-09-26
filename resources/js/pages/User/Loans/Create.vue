<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch, ref } from 'vue'

// PrimeVue components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import InputText from 'primevue/inputtext'

interface Book {
  id: number
  title: string
  author: string
  stock: number
}

// Props dari backend: daftar buku + status peminjaman pending
const props = defineProps<{
  books: Book[]
  hasPendingLoan: boolean
}>()

// Search term untuk filter global
const search = ref('')

// Ambil flash message dari backend
const page = usePage()
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.message) Swal.fire('Berhasil', flash.message, 'success')
    if (flash?.error) Swal.fire('Gagal', flash.error, 'error')
  },
  { immediate: true, deep: true }
)

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Ajukan Peminjaman', href: '/user/loansuser' },
]

// Tombol pinjam buku
const pinjam = (book: Book) => {
  if (props.hasPendingLoan) return // prevent click jika sudah ada peminjaman

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
        preserveScroll: true,
        preserveState: true,
        onError: () => Swal.fire('Gagal', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

// Tombol disabled jika stock habis atau sudah ada peminjaman pending
const isButtonDisabled = (book: Book) => {
  return props.hasPendingLoan || book.stock === 0
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Ajukan Peminjaman" />

    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">Ajukan Peminjaman Buku</h1>

      <!-- Note jika sudah ada peminjaman -->
      <div v-if="props.hasPendingLoan" class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
        Anda hanya bisa merequest pinjam 1 buku.
      </div>

     <!-- Search bar + button -->
<div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
  <span class="p-input-icon-left w-full md:w-1/2">
    <InputText v-model="search" placeholder="Cari judul atau penulis..." class="w-full" />
  </span>

  <Button
    label="Lihat Status Peminjaman"
    severity="primary"
    class="w-full md:w-auto"
    @click="router.get(route('user.loans.status'))"
  />
</div>


      <DataTable
        :value="props.books"
        :paginator="true"
        :rows="5"
        :globalFilterFields="['title','author']"
        :filters="{ 'global': { value: search, matchMode: 'contains' } }"
        class="p-datatable-gridlines p-datatable-sm"
      >
        <Column field="title" header="Judul Buku" sortable />
        <Column field="author" header="Penulis" sortable />
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
              :class="{'bg-gray-400 hover:bg-gray-400': props.hasPendingLoan}"
              severity="info"
              :disabled="isButtonDisabled(slotProps.data)"
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
