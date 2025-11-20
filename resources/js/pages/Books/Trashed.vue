<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

// PrimeVue
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

// search
const search = ref('')
const filteredBooks = computed(() =>
  props.books.data.filter((b) =>
    Object.values(b)
      .join(' ')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

const selected = ref<any[]>([])

// === actions ===
function restoreBook(id: number) {
  Swal.fire({
    title: 'Kembalikan buku?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, kembalikan!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.restore', id), {}, {
        onSuccess: () => Swal.fire('Berhasil!', 'Buku dikembalikan.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

function forceDeleteBook(id: number) {
  Swal.fire({
    title: 'Hapus permanen?',
    text: 'Data tidak bisa dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.forceDelete', id), {}, {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Buku sudah dihapus.',
            timer: 1500,
            showConfirmButton: false,
          }).then(() => router.reload())
        },
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

// === bulk ===
function bulkRestore() {
  if (!selected.value.length) return
  Swal.fire({
    title: 'Restore buku terpilih?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Restore!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.bulkRestore'), {
        ids: selected.value.map(b => b.id)
      }, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Buku direstore.', 'success')
          selected.value = []
          router.reload()
        }
      })
    }
  })
}

function bulkForceDelete() {
  if (!selected.value.length) return
  Swal.fire({
    title: 'Hapus permanen semua?',
    text: 'Tidak bisa dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hapus!',
    confirmButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('books.bulkForceDelete'), {
        ids: selected.value.map(b => b.id)
      }, {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Semua buku terhapus.', 'success')
          selected.value = []
          router.reload()
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Books Trash" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 text-gray-900 dark:text-gray-200">

      <!-- TITLE -->
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">🗑️ Trash Buku</h1>
        <Link
          :href="route('books.index')"
          class="bg-gray-500 dark:bg-gray-700 text-white px-3 py-2 rounded hover:bg-gray-600 dark:hover:bg-gray-600"
        >
          ← Kembali
        </Link>
      </div>

      <!-- Actions -->
      <div class="mb-3 flex items-center gap-2">
        <Button
          v-if="selected.length && can('books.restore')"
          label="Restore Terpilih"
          class="p-button-sm p-button-success"
          @click="bulkRestore"
        />

        <Button
          v-if="selected.length && can('books.forceDelete')"
          label="Hapus Permanen"
          class="p-button-sm p-button-danger"
          @click="bulkForceDelete"
        />

        <InputText
          v-model="search"
          placeholder="Cari buku..."
          class="w-1/2 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700"
        />
      </div>

      <!-- TABLE -->
      <div
        class="bg-white dark:bg-gray-800 dark:border-gray-700 border rounded-lg shadow-sm"
      >
        <DataTable
          v-model:selection="selected"
          :value="filteredBooks"
          dataKey="id"
          responsiveLayout="scroll"
          class="p-datatable-sm p-datatable-gridlines text-sm dark:text-gray-200"
        >
          <Column selectionMode="multiple" style="width: 3rem" />
          <Column field="isbn" header="ISBN" sortable />
          <Column field="title" header="Judul" sortable />
          <Column field="author" header="Penulis" sortable />
          <Column field="publisher" header="Penerbit" sortable />
          <Column field="year" header="Tahun" sortable />
          <Column field="pages" header="Halaman" sortable />
          <Column field="category" header="Kategori" sortable />

          <Column field="deleted_at" header="Dihapus Pada">
            <template #body="slotProps">
              <span class="text-red-500 text-xs">{{ slotProps.data.deleted_at }}</span>
            </template>
          </Column>

          <Column header="Aksi">
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
                  label="Hapus"
                  class="p-button-sm p-button-danger text-xs"
                  @click="forceDeleteBook(slotProps.data.id)"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- Pagination -->
      <div
        class="px-5 py-5 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col xs:flex-row items-center xs:justify-between mt-4 rounded-b-lg"
      >
        <span class="text-xs xs:text-sm">
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
            class="text-sm bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-4 rounded-l disabled:opacity-40"
          >
            Prev
          </button>

          <button
            @click="router.get(props.books.next_page_url)"
            :disabled="!props.books.next_page_url"
            class="text-sm bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-4 rounded-r disabled:opacity-40"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
