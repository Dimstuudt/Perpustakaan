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
      <i style="color: grey;">#NambahCoverLewatEdit/Preview</i>


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

      <!-- Search & Bulk Action -->
      <div class="mb-6 flex items-center gap-3">
        <input
          v-model="search"
          placeholder="Cari buku..."
          class="w-full md:w-1/2 px-3 py-2 border rounded focus:ring focus:border-blue-400"
        />
        <button
          v-if="selected.length && can('books.delete')"
          @click="bulkDeleteBooks"
          class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600"
        >
          Hapus Terpilih ({{ selected.length }})
        </button>
      </div>

      <!-- Card Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="book in filteredBooks"
          :key="book.id"
          class="bg-white shadow-md rounded-lg overflow-hidden flex flex-col relative"
        >
          <!-- Checkbox Select -->
          <input
            type="checkbox"
            class="absolute top-2 left-2 w-4 h-4 accent-red-500"
            :checked="selected.includes(book.id)"
            @change="toggleSelect(book.id)"
          />

          <!-- Cover -->
 <!-- Cover -->
<div
  class="h-56 w-full flex items-center justify-center bg-gray-200 text-center px-2"
>
  <img
    v-if="book.cover_path"
    :src="book.cover_path"
    alt="Cover"
    class="h-56 w-full object-cover"
  />
  <span
    v-else
    class="text-gray-700 font-semibold text-sm line-clamp-3"
  >
    {{ book.title }}
  </span>
</div>


          <!-- Info -->
          <div class="p-4 flex flex-col flex-1">
            <h2 class="text-lg font-semibold truncate">{{ book.title }}</h2>
            <p class="text-sm text-gray-600">✍️ {{ book.author ?? '-' }}</p>
            <p class="text-xs text-gray-500 mt-1">🏷️ {{ book.category ?? '-' }}</p>

            <div class="mt-auto flex gap-2 pt-3">
              <button
                v-if="can('books.preview')"
                @click="router.get(route('books.preview', book.id))"
                class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600"
              >
                Preview
              </button>
              <button
                v-if="can('books.edit')"
                @click="router.get(route('books.edit', book.id))"
                class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
              >
                Edit
              </button>
              <button
                v-if="can('books.delete')"
                @click="deleteBook(book.id)"
                class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
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
