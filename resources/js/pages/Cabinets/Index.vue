<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  cabinets: {
    data: any[],
    current_page: number,
    last_page: number,
    per_page: number,
    total: number,
    next_page_url: string | null,
    prev_page_url: string | null
  },
  filters: {
    search?: string,
  }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cabinets', href: route('cabinets.index') },
]

const perPage = ref(Number(new URLSearchParams(window.location.search).get('per_page')) || 10)
const form = useForm({
  search: props.filters.search || '',
})

const queryParams = computed(() => ({
  search: form.search,
  per_page: perPage.value,
}))

watch([() => form.search, perPage], () => {
  router.get(route('cabinets.index'), { ...queryParams.value, page: 1 }, { preserveState: true, replace: true })
  first.value = 0
  rows.value = perPage.value
})

const first = ref((props.cabinets.current_page - 1) * props.cabinets.per_page)
const rows = ref(props.cabinets.per_page)

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1
  router.get(route('cabinets.index'), { ...queryParams.value, page: pageNumber }, { preserveState: true, replace: true })
  first.value = event.first
  rows.value = event.rows
}

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
          })
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
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Cabinets Management</h1>
              <p class="text-gray-600 mt-1">Manage cabinets and their racks</p>
            </div>
            <Button
              label="Add Cabinet"
              icon="pi pi-plus"
              class="bg-indigo-600 hover:bg-indigo-700"
              @click="router.get(route('cabinets.create'))"
            />
          </div>

          <!-- Stats -->
          <div class="bg-white rounded-xl p-4 border border-gray-200 mb-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Cabinets</p>
                <p class="text-2xl font-bold text-gray-900">{{ cabinets.total }}</p>
              </div>
              <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="pi pi-inbox text-indigo-600 text-xl"></i>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex flex-wrap gap-3 items-center">
              <div class="flex items-center gap-2">
                <i class="pi pi-filter text-gray-500"></i>
                <span class="text-sm font-medium text-gray-700">Filters:</span>
              </div>

              <!-- Per Page -->
              <Dropdown
                v-model="perPage"
                :options="[1,5,10,20,50]"
                placeholder="Per page"
                class="w-28"
              />

              <!-- Search Input -->
              <div class="flex-1 min-w-[200px]">
                <span class="p-input-icon-left w-full">
                  <InputText
                    v-model="form.search"
                    placeholder="Search cabinet..."
                    class="w-full"
                  />
                </span>
              </div>

              <Button
                v-if="form.search"
                label="Reset"
                icon="pi pi-times"
                text
                size="small"
                @click="() => { form.search = '' }"
              />
            </div>
          </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <DataTable
            :value="cabinets.data"
            :paginator="true"
            :first="first"
            :rows="rows"
            :totalRecords="cabinets.total"
            :lazy="true"
            @page="onPage"
            class="custom-datatable"
            stripedRows
          >
            <Column field="code" header="Code" class="min-w-[120px]">
              <template #body="slotProps">
                <span class="font-medium text-gray-900">{{ slotProps.data.code }}</span>
              </template>
            </Column>

            <Column field="name" header="Name" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <i class="pi pi-inbox text-indigo-600"></i>
                  <span class="font-semibold text-gray-900">{{ slotProps.data.name }}</span>
                </div>
              </template>
            </Column>

            <Column field="description" header="Description" class="min-w-[250px]">
              <template #body="slotProps">
                <span class="text-gray-600">{{ slotProps.data.description || '-' }}</span>
              </template>
            </Column>

            <Column field="racks_count" header="Racks" class="min-w-[100px]">
              <template #body="slotProps">
                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                  {{ slotProps.data.racks_count || 0 }}
                </span>
              </template>
            </Column>

            <Column header="Actions" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-2">
                  <Button
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    @click="router.get(route('cabinets.edit', slotProps.data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    @click="deleteCabinet(slotProps.data.id)"
                    v-tooltip.top="'Delete'"
                  />
                </div>
              </template>
            </Column>

            <template #empty>
              <div class="text-center py-12">
                <i class="pi pi-inbox text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">No cabinets found</p>
              </div>
            </template>
          </DataTable>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
:deep(.custom-datatable .p-datatable-header) {
  background: transparent;
  border: none;
  padding: 0;
}

:deep(.custom-datatable .p-datatable-thead > tr > th) {
  background: #f9fafb;
  color: #374151;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

:deep(.custom-datatable .p-datatable-tbody > tr) {
  transition: all 0.2s;
}

:deep(.custom-datatable .p-datatable-tbody > tr:hover) {
  background: #f9fafb;
}

:deep(.custom-datatable .p-datatable-tbody > tr > td) {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

:deep(.p-paginator) {
  background: transparent;
  border: none;
  padding: 1rem;
}
</style>
