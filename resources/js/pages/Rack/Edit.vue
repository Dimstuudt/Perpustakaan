<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import MultiSelect from 'primevue/multiselect'

// Props
const props = defineProps<{
  rack: { id: number; name: string; books: any[] }
  books: any[]
}>()

// Form state (default aman)
const form = useForm({
  name: props.rack?.name || '',
  book_ids: [] as number[] // kosong, hanya buat assign baru
})

// Submit update nama + assign baru
function submit() {
  form.put(route('racks.update', props.rack.id))
}

// Hapus buku tertentu dari rak
function removeBook(id: number) {
  router.put(route('racks.update', props.rack.id), {
    name: form.name,
    remove_id: id
  })
}
</script>

<template>
  <Head title="Edit Rack" />
  <AppLayout>
    <div class="p-6 space-y-6" v-if="rack">
      <h2 class="text-2xl font-semibold">Edit Rack</h2>

      <div class="grid gap-4 max-w-lg">
        <!-- Nama Rak -->
        <div>
          <label for="name">Rack Name </label>
          <InputText id="name" v-model="form.name" placeholder="Enter rack name" />
        </div>

        <!-- Tambah Buku Baru -->
        <div>
          <label for="books">Assign New Books</label>
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
          <h3 class="font-medium">Books in this Rack</h3>
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
            <li v-if="!rack.books || rack.books.length === 0" class="p-2 text-gray-500">
              No books in this rack.
            </li>
          </ul>
        </div>

        <!-- Tombol -->
        <div class="flex gap-2">
          <Button label="Save" icon="pi pi-save" severity="success" @click="submit" />
          <Button label="Back" icon="pi pi-arrow-left" severity="secondary" @click="router.get(route('racks.index'))" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
