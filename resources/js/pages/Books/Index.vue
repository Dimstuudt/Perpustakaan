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
    }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
}>()

// Search & Selection
const search = ref('')
const selected = ref<any[]>([]) // <-- untuk bulk delete
const filteredBooks = computed(() =>
  props.books.data.filter((b) =>
    Object.values(b)
      .join(' ')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

// Hapus satuan
function deleteBook(id: number) {
  Swal.fire({
    title: 'Yakin hapus?',
    text: 'Buku akan dipindahkan ke sampah.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('books.destroy', id), {
        onSuccess: () => Swal.fire('Terhapus!', 'Buku berhasil dihapus.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

// Bulk delete
function bulkDeleteBooks() {
  if (!selected.value.length) return

  Swal.fire({
    title: 'Hapus buku terpilih?',
    text: 'Semua buku akan dipindahkan ke sampah.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.bulkDelete'), {
        ids: selected.value.map((b) => b.id),
      }, {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Buku dipindahkan ke sampah.', 'success')
          selected.value = []
          router.reload()
        },
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}
</script>

<template>
  <Head title="Books" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">📚 Daftar Buku</h1>
    <i style="color: grey;">#Nambah Cover Lewat Preview/Edit </i>


        <div class="flex gap-2">
          <Link
            v-if="can('books.create')"
            :href="route('books.create')"
            class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 flex items-center gap-1"
          >
            + Tambah Buku
          </Link>

          <Link
            v-if="can('books.view')"
            :href="route('books.trashed')"
            class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 flex items-center gap-1"
          >
            <span>🗑️</span>
            <span>Lihat Sampah</span>
          </Link>
        </div>
      </div>

      <!-- Bulk Action + Search -->
      <div class="mb-3 flex items-center gap-2">
        <Button
          v-if="selected.length && can('books.delete')"
          label="Hapus Terpilih"
          class="p-button-sm p-button-danger"
          @click="bulkDeleteBooks"
        />
        <InputText v-model="search" placeholder="Cari buku..." class="w-1/2" />
      </div>

      <!-- DataTable -->
      <DataTable
        v-model:selection="selected"
        :value="filteredBooks"
        dataKey="id"
        responsiveLayout="scroll"
        class="p-datatable-sm p-datatable-gridlines text-sm"
      >
        <!-- Checkbox untuk pilih banyak -->
        <Column selectionMode="multiple" style="width: 3rem" />

        <Column field="isbn" header="ISBN" :style="{ width: '120px' }" sortable />
        <Column field="title" header="Judul" :style="{ width: '200px' }" sortable />
        <Column field="author" header="Penulis" :style="{ width: '140px' }" sortable />
        <Column field="publisher" header="Penerbit" :style="{ width: '140px' }" sortable />
        <Column field="year" header="Tahun" :style="{ width: '80px' }" sortable />
        <Column field="pages" header="Halaman" :style="{ width: '80px' }" sortable />
        <Column field="type" header="Tipe" :style="{ width: '80px' }" sortable />
        <Column field="category" header="Kategori" :style="{ width: '120px' }" sortable />

        <!-- Aksi -->
        <Column header="Aksi" :style="{ width: '200px' }">
          <template #body="slotProps">
            <div class="flex gap-1 justify-center">
              <Button
                v-if="can('books.preview')"
                label="Preview"
                class="p-button-sm p-button-help text-xs"
                @click="() => router.get(route('books.preview', slotProps.data.id))"
              />
              <Button
                v-if="can('books.edit')"
                label="Edit"
                class="p-button-sm p-button-info text-xs"
                @click="() => router.get(route('books.edit', slotProps.data.id))"
              />
              <Button
                v-if="can('books.delete')"
                label="Hapus"
                class="p-button-sm p-button-danger text-xs"
                @click="deleteBook(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- Pagination -->
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
