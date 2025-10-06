<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { watch, ref, computed } from 'vue'
import Tag from 'primevue/tag'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

interface Book {
  id: number
  title: string
  author: string
  stock: number
  fee: number
  cover_path?: string
}

const props = defineProps<{
  books: Book[]
  hasPendingLoan: boolean
}>()

const search = ref('')

const page = usePage()
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.message) Swal.fire('Berhasil', flash.message, 'success')
    if (flash?.error) Swal.fire('Gagal', flash.error, 'error')
  },
  { immediate: true, deep: true }
)

const breadcrumbs = [
  { title: 'Dashboard', href: '/' },
  { title: 'Ajukan Peminjaman', href: '/user/loansuser' },
]

const filteredBooks = computed(() => {
  const q = search.value.toLowerCase()
  return props.books.filter(
    (book) =>
      book.title.toLowerCase().includes(q) ||
      book.author.toLowerCase().includes(q)
  )
})
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Pilih Buku" />

    <div class="p-6 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Pilih Buku untuk Dipinjam</h1>

        <div v-if="props.hasPendingLoan" class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg shadow-sm">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Anda hanya bisa merequest pinjam 1 buku.
          </div>
        </div>

        <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div class="relative">
            <InputText
              v-model="search"
              placeholder="Cari judul atau penulis..."
              class="w-full lg:w-96 pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm"
            />
          </div>
          <Button
            label="Lihat Status Peminjaman"
            severity="primary"
            class="w-full lg:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200"
            @click="router.get(route('user.loans.status'))"
          />
        </div>

        <!-- Grid Katalog -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="book in filteredBooks"
            :key="book.id"
            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1"
          >
            <!-- Cover -->
            <div class="h-48 md:h-56 bg-gray-100 flex items-center justify-center overflow-hidden">
              <img
                :src="book.cover_path ? '/storage/' + book.cover_path : 'https://via.placeholder.com/200x280?text=No+Cover'"
                alt="Cover Buku"
                class="w-full h-full object-cover transition duration-300 hover:scale-105"
              />
            </div>

            <!-- Keterangan -->
            <div class="p-5">
              <h3 class="font-bold text-lg text-gray-900 line-clamp-2 mb-2 leading-tight">{{ book.title }}</h3>
              <p class="text-gray-600 text-sm mb-3 italic">{{ book.author }}</p>
              <div class="space-y-2 mb-4">
                <p class="text-sm font-semibold text-gray-800">
                  Fee: <span class="text-emerald-600">Rp {{ book.fee.toLocaleString('id-ID') }}</span>
                </p>
                <Tag
                  :value="book.stock > 0 ? book.stock + ' tersedia' : 'Habis'"
                  :severity="book.stock > 0 ? 'success' : 'danger'"
                  class="w-fit px-3 py-1 text-xs font-medium rounded-full"
                />
              </div>

              <Button
                label="Lihat Detail"
                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition duration-200"
                @click="router.get(route('user.loans.show', book.id))"
              />
            </div>
          </div>
        </div>

        <div v-if="props.books.length === 0" class="mt-12 text-center py-12 bg-white rounded-lg shadow-md">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada buku tersedia</h3>
          <p class="text-gray-500">Coba periksa kembali nanti atau hubungi administrator.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
