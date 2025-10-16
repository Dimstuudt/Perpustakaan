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
  { title: 'Roles', href: '/roles' }
]

const props = defineProps<{
  roles: {
    data: any[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  },
  permissions: any[],
  filters: {
    search?: string,
    permission?: string | null,
  }
}>()

// Flash message
const flash = page.props?.flash?.message
if (flash) {
  Swal.fire({
    title: 'Success!',
    text: flash,
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
  })
}

// Delete role
function deleteRole(id: number, roleName: string) {
  Swal.fire({
    title: 'Delete Role?',
    html: `Are you sure you want to delete <strong>${roleName}</strong>? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(route('roles.destroy', id), {
        onSuccess: () => Swal.fire('Deleted!', 'Role has been deleted.', 'success'),
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

// Show permissions (popup)
function showPermissions(role: any) {
  const permsList = role.permissions.map((p: any) => `
    <div class="flex items-center gap-2 py-2 px-3 bg-gray-50 rounded-lg">
      <i class="pi pi-key text-indigo-600"></i>
      <span class="font-medium">${p.name}</span>
    </div>
  `).join('')

  Swal.fire({
    title: `<div class="text-xl font-bold">${role.name}'s Permissions</div>`,
    html: `<div class="space-y-2 mt-4">${permsList}</div>` || '<p class="text-gray-500">No permissions assigned</p>',
    icon: 'info',
    confirmButtonText: 'Close',
    confirmButtonColor: '#6366f1',
    customClass: {
      popup: 'rounded-xl'
    }
  })
}

// Filters
const perPage = ref(Number(new URLSearchParams(window.location.search).get('per_page')) || 10)
const form = useForm({
  search: props.filters.search || '',
  permission: props.filters.permission || null,
})

const queryParams = computed(() => ({
  search: form.search,
  permission: form.permission,
  per_page: perPage.value,
}))

// Auto reload saat filter berubah
watch([() => form.search, () => form.permission, perPage], () => {
  router.get(route('roles.index'), { ...queryParams.value, page: 1 }, { preserveState: true, replace: true })
  first.value = 0
  rows.value = perPage.value
})

// Paginator
const first = ref((props.roles.current_page - 1) * props.roles.per_page)
const rows = ref(props.roles.per_page)

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1
  router.get(route('roles.index'), { ...queryParams.value, page: pageNumber }, { preserveState: true, replace: true })
  first.value = event.first
  rows.value = event.rows
}

const getPermissionSeverity = (permissionName: string) => {
  if (permissionName.toLowerCase().includes('delete')) return 'danger'
  if (permissionName.toLowerCase().includes('edit') || permissionName.toLowerCase().includes('create')) return 'warning'
  return 'success'
}

// Get dominant permission based on priority
const getDominantPermission = (permissions: any[]) => {
  if (!permissions || permissions.length === 0) return null

  const priority = ['delete', 'edit', 'create', 'view']

  for (const p of priority) {
    const found = permissions.find(r => r.name.toLowerCase().includes(p))
    if (found) return found
  }

  return permissions[0]
}

const truncatePermission = (permissionName: string) => {
  if (!permissionName) return ''
  return permissionName.substring(0, 8)
}
</script>

<template>
  <Head title="Roles" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Roles Management</h1>
              <p class="text-gray-600 mt-1">Manage system roles and permissions</p>
            </div>
            <Button
              v-if="can('roles.create')"
              label="Create Role"
              icon="pi pi-plus"
              class="bg-indigo-600 hover:bg-indigo-700"
              @click="router.get(route('roles.create'))"
            />
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Roles</p>
                  <p class="text-2xl font-bold text-gray-900">{{ roles.total }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-shield text-indigo-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Permissions</p>
                  <p class="text-2xl font-bold text-indigo-600">{{ permissions.length }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-key text-indigo-600 text-xl"></i>
                </div>
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

              <Dropdown
                v-model="perPage"
                :options="[5,10,20,50]"
                placeholder="Per page"
                class="w-28"
              />

              <Dropdown
                v-model="form.permission"
                :options="[{ label: 'All Permissions', value: null }, ...permissions.map(p => ({ label: p.name, value: p.name }))]"
                optionLabel="label"
                optionValue="value"
                placeholder="Filter by Permission"
                class="w-48"
              />

              <div class="flex-1 min-w-[200px]">
                <span class="p-input-icon-left w-full">
                  <InputText
                    v-model="form.search"
                    placeholder="Search by role name..."
                    class="w-full"
                  />
                </span>
              </div>

              <Button
                v-if="form.search || form.permission"
                label="Reset"
                icon="pi pi-times"
                text
                size="small"
                @click="() => { form.search = ''; form.permission = null }"
              />
            </div>
          </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <DataTable
            :value="roles.data"
            :paginator="true"
            :first="first"
            :rows="rows"
            :totalRecords="roles.total"
            :rowsPerPageOptions="[5,10,20,50]"
            :lazy="true"
            @page="onPage"
            class="custom-datatable"
            stripedRows
          >
            <!-- ID Column -->
            <Column field="id" header="ID" class="min-w-[80px]">
              <template #body="slotProps">
                <span class="font-medium text-gray-900">#{{ slotProps.data.id }}</span>
              </template>
            </Column>

            <!-- Role Name Column -->
            <Column field="name" header="Role Name" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <i class="pi pi-shield text-indigo-600"></i>
                  <span class="font-semibold text-gray-900">{{ slotProps.data.name }}</span>
                </div>
              </template>
            </Column>

            <!-- Permissions Column -->
            <Column header="Permissions" class="min-w-[300px]">
              <template #body="slotProps">
                <div v-if="slotProps.data.permissions && slotProps.data.permissions.length > 0"
                     class="flex items-center gap-1">
                  <!-- Permission utama -->
                  <Tag
                    :value="truncatePermission(getDominantPermission(slotProps.data.permissions).name)"
                    :severity="getPermissionSeverity(getDominantPermission(slotProps.data.permissions).name)"
                    class="w-24 text-center px-2 py-1 text-xs rounded-md"
                  />

                  <!-- +more dengan style konsisten -->
                  <button
                    v-if="slotProps.data.permissions.length > 1"
                    @click="showPermissions(slotProps.data)"
                    class="w-24 text-center px-2 py-1 text-xs rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 font-medium"
                  >
                    +{{ slotProps.data.permissions.length - 1 }} more
                  </button>
                </div>

                <!-- Kalau gak ada permission -->
                <Tag v-else value="No Permissions" severity="secondary" class="w-24 text-center px-2 py-1 text-xs rounded-md" />
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Actions" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-2">
                  <Button
                    v-if="can('roles.edit')"
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    @click="router.get(route('roles.edit', slotProps.data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button
                    v-if="can('roles.delete')"
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    @click="deleteRole(slotProps.data.id, slotProps.data.name)"
                    v-tooltip.top="'Delete'"
                  />
                </div>
              </template>
            </Column>
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
