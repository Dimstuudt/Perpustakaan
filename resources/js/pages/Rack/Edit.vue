<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import MultiSelect from 'primevue/multiselect'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types'

// Props
const props = defineProps<{
  rack: { id: number; name: string; books: any[] }
  books: any[]
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Racks', href: route('racks.index') },
  { title: 'Edit Rack', href: route('racks.edit', props.rack.id) },
]

// Form state
const form = useForm({
  name: props.rack?.name || '',
  book_ids: [] as number[]
})

// Submit update nama + assign baru
function submit() {
  form.put(route('racks.update', props.rack.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: 'Rack has been updated successfully.',
        timer: 2000,
        showConfirmButton: false,
      })
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Failed!',
        text: 'Something went wrong while updating the rack.',
      })
    }
  })
}

// Hapus buku tertentu dari rak
function removeBook(id: number) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This book will be removed from the rack!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, remove it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('racks.update', props.rack.id), {
        name: form.name,
        remove_id: id
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Removed!',
            text: 'Book has been removed from the rack.',
            timer: 2000,
            showConfirmButton: false,
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Something went wrong while removing the book.',
          })
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Edit Rack" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6" v-if="rack">
      <!-- Header -->
      <h2 class="text-2xl font-semibold">Edit Rack</h2>

      <div class="grid gap-4 max-w-lg">
        <!-- Nama Rak -->
        <div>
          <label for="name" class="block text-sm font-medium mb-1">Rack Name</label>
          <InputText id="name" v-model="form.name" placeholder="Enter rack name" class="w-full" />
        </div>

        <!-- Tambah Buku Baru -->
        <div>
          <label for="books" class="block text-sm font-medium mb-1">Assign New Books</label>
          <MultiSelect
            id="books"
            v-model="form.book_ids"
            :options="books || []"
            optionLabel="title"
            optionValue="id"
            placeholder="Select books"
            class="w-full"
          />
        </div>

        <!-- Buku yg sudah ada di rak -->
        <div>
          <h3 class="font-medium mb-2">Books in this Rack</h3>
          <ul class="divide-y border rounded-md">
            <li
              v-for="book in rack.books || []"
              :key="book.id"
              class="flex justify-between items-center p-2"
            >
              <span>{{ book.title }}</span>
              <Button
                label="Remove"
                icon="pi pi-times"
                severity="danger"
                text
                @click="removeBook(book.id)"
              />
            </li>
            <li v-if="!rack.books || rack.books.length === 0" class="p-2 text-gray-500 text-sm">
              No books in this rack.
            </li>
          </ul>
        </div>

        <!-- Tombol -->
        <div class="flex gap-2">
          <Button
            label="Save"
            icon="pi pi-save"
            severity="success"
            @click="submit"
          />
          <Button
            label="Back"
            icon="pi pi-arrow-left"
            severity="secondary"
            @click="router.get(route('racks.index'))"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
