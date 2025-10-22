<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  cabinets: {
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
  { title: 'Cabinets', href: route('cabinets.index') },
]

// Search form
const form = useForm({
  search: props.filters.search || '',
})

watch(() => form.search, () => {
  form.get(route('cabinets.index'), { preserveScroll: true, preserveState: true })
})

// Delete cabinet
function deleteCabinet(id: number) {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: 'Semua rak di dalam cabinet ini akan dilepaskan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('cabinets.destroy', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Cabinet berhasil dihapus.',
            timer: 1500,
            showConfirmButton: false,
          }).then(() => router.reload())
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menghapus cabinet.',
          })
        },
      })
    }
  })
}

</script>

<template>
  <Head title="Cabinets" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Cabinets</h2>
        <Button
          label="Add Cabinet"
          icon="pi pi-plus"
          severity="success"
          @click="router.get(route('cabinets.create'))"
        />
      </div>

      <!-- Search -->
      <div class="mb-4">
        <InputText v-model="form.search" placeholder="Search cabinet..." class="w-64" />
      </div>

      <!-- Table -->
      <DataTable :value="cabinets.data" :paginator="true" :rows="10" :totalRecords="cabinets.total">
        <Column field="code" header="Code" />
        <Column field="name" header="Name" />
        <Column field="description" header="Description" />
        <Column field="racks_count" header="Racks">
          <template #body="slotProps">
            <span
              class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"
            >
              {{ slotProps.data.racks_count || 0 }}
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
                :href="route('cabinets.edit', slotProps.data.id)"
              />
              <Button
                label="Delete"
                icon="pi pi-trash"
                size="small"
                severity="danger"
                @click="deleteCabinet(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
