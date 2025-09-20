<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

// PrimeVue Components
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { ref, computed } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Books', href: '/books' },
  { title: 'Trash', href: '/books/trashed' },
]

const props = defineProps<{
  books: {
    data: {
      id: number
      isbn: string
      title: string
      author: string | null
      publisher: string | null
      year: number | null
      pages: number | null
      category: string | null
      deleted_at: string | null
    }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
}>()

// Search
const search = ref('')
const filteredBooks = computed(() =>
  props.books.data.filter((b) =>
    Object.values(b)
      .join(' ')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

// Restore buku
function restoreBook(id: number) {
  Swal.fire({
    title: 'Kembalikan buku?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, kembalikan!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.restore', id), {
        onSuccess: () => Swal.fire('Berhasil!', 'Buku dikembalikan.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

// Hapus permanen
function forceDeleteBook(id: number) {
  Swal.fire({
    title: 'Hapus permanen?',
    text: 'Buku akan dihapus selamanya!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('books.forceDelete', id), {
        onSuccess: () => Swal.fire('Terhapus!', 'Buku dihapus permanen.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}
</script>

<template>
  <Head title="Books Trash" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">🗑️ Trash Buku</h1>
        <Link
          :href="route('books.index')"
          class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600"
        >
          ← Kembali ke Daftar Buku
        </Link>
        
      </div>

      <!-- Search -->
      <div class="mb-3 w-1/2">
        <span class="p-input-icon-left w-full">
          <InputText v-model="search" placeholder="Cari buku..." class="w-full" />
        </span>
      </div>

      <!-- DataTable -->
      <DataTable :value="filteredBooks" responsiveLayout="scroll" class="p-datatable-sm p-datatable-gridlines text-sm">
        <Column field="isbn" header="ISBN" :style="{ width: '120px' }" sortable></Column>
        <Column field="title" header="Judul" :style="{ width: '200px' }" sortable></Column>
        <Column field="author" header="Penulis" :style="{ width: '140px' }" sortable></Column>
        <Column field="publisher" header="Penerbit" :style="{ width: '140px' }" sortable></Column>
        <Column field="year" header="Tahun" :style="{ width: '80px' }" sortable></Column>
        <Column field="pages" header="Halaman" :style="{ width: '80px' }" sortable></Column>
        <Column field="category" header="Kategori" :style="{ width: '120px' }" sortable></Column>
        <Column field="deleted_at" header="Dihapus Pada" :style="{ width: '160px' }">
          <template #body="slotProps">
            <span class="text-red-600 text-xs">{{ slotProps.data.deleted_at }}</span>
          </template>
        </Column>

        <!-- Kolom Aksi -->
        <Column header="Aksi" :style="{ width: '220px' }">
          <template #body="slotProps">
            <div class="flex gap-1 justify-center">
              <Button
                v-if="can('books.restore')"
                label="Restore"
                class="p-button-sm p-button-success text-xs"
                @click="restoreBook(slotProps.data.id)"
              />

              <Button
                v-if="can('books.forceDelete')"
                label="Hapus Permanen"
                class="p-button-sm p-button-danger text-xs"
                @click="forceDeleteBook(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- Pagination manual -->
      <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between mt-4">
        <span class="text-xs xs:text-sm text-gray-900">
          Showing
          {{ (props.books.current_page - 1) * props.books.per_page + 1 }}
          to
          {{
            props.books.current_page * props.books.per_page > props.books.total
              ? props.books.total
              : props.books.current_page * props.books.per_page
          }}
          of {{ props.books.total }} entries
        </span>
        <div class="inline-flex mt-2 xs:mt-0">
          <button
            @click="router.get(props.books.prev_page_url)"
            :disabled="!props.books.prev_page_url"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l"
          >
            Prev
          </button>
          <button
            @click="router.get(props.books.next_page_url)"
            :disabled="!props.books.next_page_url"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
