<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'

const props = defineProps({
  category: Object,
})

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
  { title: 'Edit Kategori', href: route('categories.edit', props.category.id) },
]

const form = useForm({
  name: props.category.name,
})

const submit = () => {
  form.put(route('categories.update', props.category.id))
}

</script>

<template>
  <Head title="Edit Kategori" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-md">
      <h1 class="text-xl font-bold mb-4">Edit Kategori</h1>
      <form @submit.prevent="submit" class="space-y-3">
        <div>
          <label class="block mb-1">Nama Kategori</label>
          <input v-model="form.name" type="text" class="border p-2 w-full" />
        </div>

        <div class="flex items-center space-x-2 mt-4">
          <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">
            Update
          </button>
          <Link :href="route('categories.index')" class="text-gray-600">Batal</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
