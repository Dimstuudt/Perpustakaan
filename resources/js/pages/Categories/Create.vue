<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import Swal from 'sweetalert2'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
  { title: 'Tambah Kategori', href: route('categories.create') },
]

// Form Inertia
const form = useForm({
  name: '',
})

// Submit form dengan SweetAlert
const submit = () => {
  form.post(route('categories.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil!',
        text: 'Kategori berhasil ditambahkan.',
        icon: 'success',
        confirmButtonColor: '#3085d6'
      })
      form.reset() // reset input setelah sukses
    },
    onError: () => {
      Swal.fire({
        title: 'Gagal!',
        text: 'Terjadi kesalahan, cek input kamu.',
        icon: 'error',
        confirmButtonColor: '#d33'
      })
    }
  })
}
</script>

<template>
  <Head title="Tambah Kategori" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-md">
      <h1 class="text-xl font-bold mb-4">Tambah Kategori</h1>
      <form @submit.prevent="submit" class="space-y-3">
        <div>
          <label class="block mb-1 font-medium">Nama Kategori</label>
          <input
            v-model="form.name"
            type="text"
            class="border p-2 w-full rounded"
            placeholder="Masukkan nama kategori"
          />
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
            {{ form.errors.name }}
          </div>
        </div>

        <div class="flex items-center space-x-2 mt-4">
          <button
            type="submit"
            class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700"
          >
            Simpan
          </button>
          <Link
            :href="route('categories.index')"
            class="bg-gray-200 px-3 py-2 rounded hover:bg-gray-300 text-gray-700"
          >
            Batal
          </Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
