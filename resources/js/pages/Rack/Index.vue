<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, useForm, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types' // ⬅️ kalau kamu sudah punya tipe ini

const props = defineProps<{
  racks: {
    data: any[],
    current_page: number,
    last_page: number,
    per_page: number,
    total: number,

  },
  filters: {
    search?: string,


  }
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Racks', href: route('racks.index') },
]

// search form
const form = useForm({
  search: props.filters.search || '',
})

watch(() => form.search, () => {
  form.get(route('racks.index'), { preserveScroll: true, preserveState: true })
})

// delete rack
function deleteRack(id: number) {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: 'Rak ini akan dihapus secara permanen!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('racks.destroy', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Rak berhasil dihapus.',
            timer: 1500,
            showConfirmButton: false,
          }).then(() => router.reload()) // 🔁 reload halaman setelah swal ditutup
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menghapus rak.',
          })
        },
      })
    }
  })
}


</script>

<template>
  <Head title="Racks" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Racks</h2>
        <Button
          label="Add Rack"
          icon="pi pi-plus"
          severity="success"
          @click="router.get(route('racks.create'))"
        />
      </div>

      <!-- Search -->
      <div class="mb-4">
        <InputText v-model="form.search" placeholder="Search rack..." class="w-64" />
      </div>

      <!-- Table -->
      <DataTable :value="racks.data" :paginator="true" :rows="10" :totalRecords="racks.total">
        <Column field="code" header="Code" />
        <Column field="name" header="Name" />
        <Column field="description" header="Description" />
        <Column header="Books">
          <template #body="slotProps">
            <span
              class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"
            >
              {{ slotProps.data.books?.length || 0 }}
            </span>
          </template>
        </Column>

        <Column header="Action">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Button
                label="Edit"
                icon="pi pi-pencil"
                size="small"
                severity="info"
                as="a"
                :href="route('racks.edit', slotProps.data.id)"
              />
              <Button
                label="Delete"
                icon="pi pi-trash"
                size="small"
                severity="danger"
                @click="deleteRack(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
