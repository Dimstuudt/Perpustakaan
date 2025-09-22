<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, router, useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

// PrimeVue
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import Tag from 'primevue/tag'

const page = usePage()
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Permissions', href: '/permissions' }
]

const props = defineProps<{
  permissions: {
    data: any[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  },
  roles: any[],
  filters: {
    search?: string,
    role?: string | null,
  }
}>()

// flash message
const flash = page.props?.flash?.message
if (flash) {
  Swal.fire({
    title: 'Success!',
    text: flash,
    icon: 'success',
    timer: 1200,
    showConfirmButton: false,
  })
}

// Delete permission
function deletePermission(id: number) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This permission will be deleted!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(route('permissions.destroy', id), {
        onSuccess: () => Swal.fire('Deleted!', 'Permission has been deleted.', 'success'),
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

// Filters
const perPage = ref(Number(new URLSearchParams(window.location.search).get('per_page')) || 10)
const form = useForm({
  search: props.filters.search || '',
  role: props.filters.role || null,
})

// Gabungkan semua query parameter
const queryParams = computed(() => ({
  search: form.search,
  role: form.role,
  per_page: perPage.value,
}))

// Watch semua filter dan perPage
watch([() => form.search, () => form.role, perPage], () => {
  router.get(route('permissions.index'), { ...queryParams.value, page: 1 }, { preserveState: true, replace: true })
  first.value = 0
  rows.value = perPage.value
})

// Paginator
const first = ref((props.permissions.current_page - 1) * props.permissions.per_page)
const rows = ref(props.permissions.per_page)

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1
  router.get(route('permissions.index'), { ...queryParams.value, page: pageNumber }, { preserveState: true, replace: true })
  first.value = event.first
  rows.value = event.rows
}
</script>

<template>
  <Head title="Permissions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Permissions</h2>
        <Button v-if="can('permissions.create')"
          label="Add Permission"
          icon="pi pi-plus"
          severity="success"
          @click="router.get(route('permissions.create'))"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3 items-center">
        <!-- Per Page -->
        <Dropdown
          v-model="perPage"
          :options="[5,10,20]"
          placeholder="Per page"
          class="w-32 text-sm text-gray-700"
        />

        <!-- Role Filter -->
        <Dropdown
          v-model="form.role"
          :options="[{ label: 'All', value: null }, ...roles]"
          optionLabel="name"
          optionValue="name"
          placeholder="Filter by Role"
          class="w-48 text-sm text-gray-700 truncate"
        />

        <!-- Search Input -->
        <span class="p-input-icon-left">
          <InputText
            v-model="form.search"
            placeholder="Search permission..."
            class="w-64 text-sm text-gray-700"
          />
        </span>
      </div>

      <!-- Table -->
     <DataTable
  :value="permissions.data"
  :paginator="true"
  :first="first"
  :rows="rows"
  :totalRecords="permissions.total"
  :rowsPerPageOptions="[5,10,20]"
  :lazy="true"
  :loading="page.props.permissionsLoading" 
  @page="onPage"
  :key="permissions.current_page"
>

        <Column field="id" header="ID" />
        <Column field="name" header="Name" />
        <Column header="Roles">
          <template #body="slotProps">
            <Tag
              v-for="role in slotProps.data.roles"
              :key="role.id"
              :value="role.name"
              severity="success"
              class="mr-1"
            />
          </template>
        </Column>
        <Column header="Action">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Button
                v-if="can('permissions.edit')"
                label="Edit"
                icon="pi pi-pencil"
                size="small"
                severity="info"
                as="a"
                :href="route('permissions.edit', slotProps.data.id)"
              />
              <Button
                v-if="can('permissions.delete')"
                label="Delete"
                icon="pi pi-trash"
                size="small"
                severity="danger"
                @click="deletePermission(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
