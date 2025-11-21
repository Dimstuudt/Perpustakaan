<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import { watch, computed } from 'vue'

interface Book {
  id: number
  title: string
  author: string
  stock: number
  fee: number
  cover_url?: string
  publisher?: string
  year?: string
  category?: { name: string }
  description?: string
}

const props = defineProps<{
  book: Book
  hasPendingLoan: boolean
}>()

// Computed property for breadcrumbs
const breadcrumbs = computed(() => [
  { title: 'Dashboard', href: '/' },
  { title: 'Ajukan Peminjaman', href: '/user/loansuser' },
  { title: props.book.title, active: true }
])

// Helper function to format fee (remove trailing .00)
const formatFee = (fee: any) => {
  const numFee = Number(fee) || 0
  // Format dengan toLocaleString untuk separator ribuan
  const formatted = numFee.toLocaleString('id-ID')
  // Hapus .00 di belakang jika ada
  return formatted.replace(/,00$/, '')
}

// Flash message handler
const page = usePage()
watch(
  () => page.props.flash,
  (flash) => {
    const isDark = document.documentElement.classList.contains('dark')

    if (flash?.message) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: flash.message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#10B981',
        background: isDark ? '#1f2937' : '#fff',
        color: isDark ? '#f3f4f6' : '#000',
      })
    }
    if (flash?.error) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: flash.error,
        confirmButtonText: 'OK',
        confirmButtonColor: '#EF4444',
        background: isDark ? '#1f2937' : '#fff',
        color: isDark ? '#f3f4f6' : '#000',
      })
    }
  },
  { immediate: true, deep: true }
)

// Function to handle loan request
const pinjam = () => {
  const isDark = document.documentElement.classList.contains('dark')

  if (props.hasPendingLoan) {
    Swal.fire({
      icon: 'info',
      title: 'Peminjaman Tertunda',
      text: 'Anda memiliki peminjaman yang masih pending. Selesaikan terlebih dahulu.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#3B82F6',
      background: isDark ? '#1f2937' : '#fff',
      color: isDark ? '#f3f4f6' : '#000',
    })
    return
  }

  if (props.book.stock === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Stok Habis',
      text: 'Buku ini sedang habis. Silakan pilih buku lain.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#F59E0B',
      background: isDark ? '#1f2937' : '#fff',
      color: isDark ? '#f3f4f6' : '#000',
    })
    return
  }

  Swal.fire({
    title: 'Konfirmasi Peminjaman',
    html: `
      <div class="text-left space-y-2">
        <div><strong>Buku:</strong> ${props.book.title}</div>
        <div><strong>Fee:</strong> Rp ${formatFee(props.book.fee)}</div>
        <div class="text-red-600 font-semibold mt-2">
          ⚠️ Peringatan: Denda Rp 1.000/hari jika telat
        </div>
        <div class="text-sm text-gray-600 mt-2">
          Pastikan Anda dapat mengembalikan buku dalam waktu 7 hari.
        </div>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, ajukan peminjaman!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#3B82F6',
    cancelButtonColor: '#6B7280',
    background: isDark ? '#1f2937' : '#fff',
    color: isDark ? '#f3f4f6' : '#000',
    customClass: {
      htmlContainer: 'text-sm leading-relaxed',
      popup: 'p-6'
    },
    buttonsStyling: true
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('user.loans.store'), {
        book_id: props.book.id,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }, {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Permintaan Terkirim!',
            text: 'Peminjaman Anda telah diajukan. Tunggu konfirmasi admin.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#10B981',
            background: isDark ? '#1f2937' : '#fff',
            color: isDark ? '#f3f4f6' : '#000',
          }).then(() => {
            router.get(route('user.loans.index'))
          })
        },
        onError: (errors) => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal Mengajukan',
            text: errors[0] || 'Terjadi kesalahan. Coba lagi.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#EF4444',
            background: isDark ? '#1f2937' : '#fff',
            color: isDark ? '#f3f4f6' : '#000',
          })
        }
      })
    }
  })
}

// Function to handle back navigation
const goBack = () => {
  router.get(route('user.loans.index'))
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.book.title" />

    <div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
      <div class="max-w-4xl mx-auto">
        <!-- Main Book Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
          <div class="p-6 lg:p-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
              <!-- Book Cover -->
              <div class="flex-shrink-0">
                <div class="relative">
                  <img
                    :src="props.book.cover_url || 'https://via.placeholder.com/250x350/FAF3E0/8B5E3C?text=No+Cover'"
                    :alt="`${props.book.title} Cover`"
                    class="w-48 h-64 lg:w-56 lg:h-72 object-cover rounded-lg shadow-lg transition-transform duration-300 hover:scale-105"
                  />
                  <div
                    v-if="!props.book.cover_url"
                    class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg flex items-center justify-center"
                  >
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">No Cover</span>
                  </div>
                </div>
              </div>

              <!-- Book Details -->
              <div class="flex-1 min-w-0">
                <div class="space-y-4">
                  <!-- Title -->
                  <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                    {{ props.book.title }}
                  </h1>

                  <!-- Basic Info -->
                  <div class="space-y-2">
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                      <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                      <span class="font-medium">Penulis: {{ props.book.author }}</span>
                    </div>

                    <div v-if="props.book.publisher" class="flex items-center text-gray-600 dark:text-gray-400">
                      <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span>Publisher: {{ props.book.publisher }}</span>
                    </div>

                    <div v-if="props.book.year" class="flex items-center text-gray-600 dark:text-gray-400">
                      <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span>Tahun Terbit: {{ props.book.year }}</span>
                    </div>

                    <div v-if="props.book.category" class="flex items-center text-gray-600 dark:text-gray-400">
                      <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                      <span>Kategori: <Tag :value="props.book.category.name" severity="info" class="ml-1" small /></span>
                    </div>
                  </div>

                  <!-- Fee and Stock -->
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                      <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Fee: <span class="text-emerald-600 dark:text-emerald-400">Rp {{ formatFee(props.book.fee) }}</span>
                      </p>
                    </div>

                    <Tag
                      :value="props.book.stock > 0 ? props.book.stock + ' tersedia' : 'Habis'"
                      :severity="props.book.stock > 0 ? 'success' : 'danger'"
                      class="px-3 py-1 text-sm font-medium rounded-full shadow-sm"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Warning Alert -->
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 mb-8 shadow-sm">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                ⚠️ Denda Rp 1.000/hari jika peminjaman lebih dari 7 hari
              </p>
              <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                Pastikan buku dikembalikan tepat waktu untuk menghindari denda.
              </p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
          <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <Button
              label="Kembali ke Daftar Buku"
              icon="pi pi-arrow-left"
              class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg shadow-sm transition duration-200"
              @click="goBack"
            />
            <Button
              v-if="props.book.stock > 0 && !props.hasPendingLoan"
              label="Ajukan Peminjaman"
              icon="pi pi-shopping-cart"
              class="w-full sm:w-auto px-8 py-3 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200"
              @click="pinjam"
            />
            <div v-else-if="props.hasPendingLoan" class="text-center sm:text-left">
              <p class="text-sm text-gray-600 dark:text-gray-400">Anda memiliki peminjaman pending. Selesaikan dulu.</p>
            </div>
            <Tag
              v-else
              value="Stok Habis"
              severity="danger"
              class="px-4 py-2 text-sm font-semibold"
            />
          </div>
        </div>

        <!-- Book Description -->
        <div v-if="props.book.description" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="p-6 lg:p-8">
            <div class="flex items-center mb-4">
              <svg class="w-6 h-6 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Deskripsi Buku</h2>
            </div>
            <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
              <p class="whitespace-pre-wrap break-words">{{ props.book.description }}</p>
            </div>
          </div>
        </div>

        <!-- Empty Description Fallback -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
          <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Deskripsi Tidak Tersedia</h3>
          <p class="text-gray-500 dark:text-gray-400">Informasi deskripsi buku belum ditambahkan.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
