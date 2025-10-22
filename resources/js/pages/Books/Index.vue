<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'
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
      cover?: string | null
    }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
}>()

const search = ref('')
const selected = ref<number[]>([])

const filteredBooks = computed(() =>
  props.books.data.filter((b) =>
    Object.values(b)
      .join(' ')
      .toLowerCase()
      .includes(search.value.toLowerCase())
  )
)

function toggleSelect(id: number) {
  if (selected.value.includes(id)) {
    selected.value = selected.value.filter((sid) => sid !== id)
  } else {
    selected.value.push(id)
  }
}

function toggleSelectAll() {
  if (selected.value.length === filteredBooks.value.length) {
    selected.value = []
  } else {
    selected.value = filteredBooks.value.map((b) => b.id)
  }
}

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
     router.post(route('books.destroy', id), {}, {
  onSuccess: () => Swal.fire('Terhapus!', 'Buku berhasil dihapus.', 'success'),
  onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
})

    }
  })
}

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
      router.post(
        route('books.bulkDelete'),
        { ids: selected.value },
        {
          onSuccess: () => {
            Swal.fire('Terhapus!', 'Buku dipindahkan ke sampah.', 'success')
            selected.value = []
            router.reload()
          },
          onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
        }
      )
    }
  })
}
</script>

<template>
  <Head title="Books" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">📚 Daftar Buku</h1>
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
            🗑️ Lihat Sampah
          </Link>
        </div>
      </div>

      <!-- Info Alert -->
      <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <p class="text-sm text-blue-800">
          <span class="font-semibold">📖 Catatan:</span> Tambahkan atau ubah cover buku melalui halaman Edit
        </p>
      </div>
      <div class="mb-6 flex items-center gap-3">
        <input
          v-model="search"
          placeholder="Cari buku..."
          class="w-full md:w-1/2 px-3 py-2 border rounded focus:ring focus:border-blue-400"
        />
        <button
          v-if="selected.length && can('books.delete')"
          @click="bulkDeleteBooks"
          class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 whitespace-nowrap"
        >
          Hapus ({{ selected.length }})
        </button>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="w-full border-collapse bg-white">
          <thead class="bg-gradient-to-r from-blue-600 to-blue-700 border-b-2 border-gray-300">
            <tr>
              <th class="px-4 py-3 text-left">
                <input
                  type="checkbox"
                  class="w-4 h-4 accent-yellow-400"
                  :checked="selected.length === filteredBooks.length && filteredBooks.length > 0"
                  @change="toggleSelectAll"
                />
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Cover</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Judul</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Penulis</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">ISBN</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Kategori</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Penerbit</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-white">Tahun</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-white">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="book in filteredBooks"
              :key="book.id"
              class="border-b border-gray-200 hover:bg-blue-50 transition duration-150"
            >
              <td class="px-4 py-3">
                <input
                  type="checkbox"
                  class="w-4 h-4 accent-blue-500"
                  :checked="selected.includes(book.id)"
                  @change="toggleSelect(book.id)"
                />
              </td>
              <td class="px-4 py-3">
                <div class="w-12 h-16 rounded-md overflow-hidden bg-gray-200 flex items-center justify-center shadow-md">
                  <img
                    v-if="book.cover_path"
                    :src="book.cover_path"
                    alt="Cover"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-2xl">📖</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm font-medium text-gray-900">
                {{ book.title }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">
                {{ book.author ?? '-' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">
                {{ book.isbn ?? '-' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">
                {{ book.category ?? '-' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">
                {{ book.publisher ?? '-' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-600">
                {{ book.year ?? '-' }}
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex gap-2 justify-center">
                  <button
                    v-if="can('books.preview')"
                    @click="router.get(route('books.preview', book.id))"
                    class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition"
                  >
                    Preview
                  </button>
                  <!-- #NambahCoverLewatEdit/Preview -->
                  <button
                    v-if="can('books.edit')"
                    @click="router.get(route('books.edit', book.id))"
                    class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 transition"
                  >
                    Edit
                  </button>
                  <button
                    v-if="can('books.delete')"
                    @click="deleteBook(book.id)"
                    class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredBooks.length === 0">
              <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                Tidak ada data buku
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between mt-6"
      >
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
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l disabled:opacity-50"
          >
            Prev
          </button>
          <button
            @click="router.get(props.books.next_page_url)"
            :disabled="!props.books.next_page_url"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
