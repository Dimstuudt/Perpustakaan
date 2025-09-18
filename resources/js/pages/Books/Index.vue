<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Books',
    href: '/books',
  },
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

function deleteBook(id: number) {
  Swal.fire({
    title: 'Yakin hapus?',
    text: 'Buku akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('books.destroy', id), {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Buku berhasil dihapus.', 'success')
        },
        onError: () => {
          Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error')
        },
      })
    }
  })
}
</script>

<template>
  <Head title="Books" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">📚 Daftar Buku</h1>
        <Link
          v-if="can('books.create')"
          :href="route('books.create')"
          class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600"
        >
          + Tambah Buku
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 border">ISBN</th>
              <th class="p-2 border">Judul</th>
              <th class="p-2 border">Penulis</th>
              <th class="p-2 border">Penerbit</th>
              <th class="p-2 border">Tahun</th>
              <th class="p-2 border">Halaman</th>
              <th class="p-2 border">Kategori</th>
              <th class="p-2 border">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="book in props.books.data" :key="book.id">
              <td class="p-2 border">{{ book.isbn }}</td>
              <td class="p-2 border">{{ book.title }}</td>
              <td class="p-2 border">{{ book.author }}</td>
              <td class="p-2 border">{{ book.publisher }}</td>
              <td class="p-2 border">{{ book.year }}</td>
              <td class="p-2 border">{{ book.pages }}</td>
              <td class="p-2 border">{{ book.category }}</td>
              <td class="p-2 border space-x-2">
                <Link
                  v-if="can('books.edit')"
                  :href="route('books.edit', book.id)"
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </Link>
                <button
                  v-if="can('books.delete')"
                  @click="deleteBook(book.id)"
                  class="text-red-600 hover:underline"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- pagination -->
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
