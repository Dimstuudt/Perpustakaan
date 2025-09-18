<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
  { title: 'Tambah Kategori', href: route('categories.create') },
]

const form = useForm({
  name: '',
})

const submit = () => {
  form.post(route('categories.store'))
}
</script>

<template>
  <Head title="Tambah Kategori" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-md">
      <h1 class="text-xl font-bold mb-4">Tambah Kategori</h1>
      <form @submit.prevent="submit" class="space-y-3">
        <div>
          <label class="block mb-1">Nama Kategori</label>
          <input v-model="form.name" type="text" class="border p-2 w-full" />
        </div>

        <div class="flex items-center space-x-2 mt-4">
          <button type="submit" class="bg-green-600 text-white px-3 py-2 rounded">
            Simpan
          </button>
          <Link :href="route('categories.index')" class="text-gray-600">Batal</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
