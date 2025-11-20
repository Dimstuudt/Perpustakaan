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

// Delete permission
function deletePermission(id: number, permissionName: string) {
  Swal.fire({
    title: 'Delete Permission?',
    html: `Are you sure you want to delete <strong>${permissionName}</strong>? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then(result => {
    if (result.isConfirmed) {
      router.post(route('permissions.destroy', id), {
        onSuccess: () => Swal.fire('Deleted!', 'Permission has been deleted.', 'success'),
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

// Show roles (popup)
function showRoles(permission: any) {
  const rolesList = permission.roles.map((r: any) => `
    <div class="flex items-center gap-2 py-2 px-3 bg-gray-50 rounded-lg">
      <i class="pi pi-shield text-indigo-600"></i>
      <span class="font-medium">${r.name}</span>
    </div>
  `).join('')

  Swal.fire({
    title: `<div class="text-xl font-bold">${permission.name}'s Roles</div>`,
    html: `<div class="space-y-2 mt-4">${rolesList}</div>` || '<p class="text-gray-500">No roles assigned</p>',
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
  role: props.filters.role || null,
})

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

// Get permission type severity
const getPermissionTypeSeverity = (permissionName: string) => {
  if (permissionName.toLowerCase().includes('delete')) return 'danger'
  if (permissionName.toLowerCase().includes('edit') || permissionName.toLowerCase().includes('create')) return 'warning'
  if (permissionName.toLowerCase().includes('view') || permissionName.toLowerCase().includes('show')) return 'info'
  return 'success'
}

// Get dominant role
const getDominantRole = (roles: any[]) => {
  if (!roles || roles.length === 0) return null

  const priority = ['super admin', 'admin', 'user']

  for (const p of priority) {
    const found = roles.find(r => r.name.toLowerCase().includes(p))
    if (found) return found
  }

  return roles[0]
}

const truncateRole = (roleName: string) => {
  if (!roleName) return ''
  return roleName.substring(0, 5)
}
</script>

<template>
  <Head title="Permissions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100">Permissions Management</h1>
              <p class="text-gray-600 dark:text-slate-400 mt-1">Manage system permissions and assign to roles</p>
            </div>
            <Button
              v-if="can('permissions.create')"
              label="Create Permission"
              icon="pi pi-plus"
              class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600"
              @click="router.get(route('permissions.create'))"
            />
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 dark:text-slate-400">Total Permissions</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ permissions.total }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                  <i class="pi pi-key text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 dark:text-slate-400">Total Roles</p>
                  <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ roles.length }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                  <i class="pi pi-shield text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700">
            <div class="flex flex-wrap gap-3 items-center">
              <div class="flex items-center gap-2">
                <i class="pi pi-filter text-gray-500 dark:text-slate-400"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-slate-300">Filters:</span>
              </div>

              <!-- Per Page -->
              <Dropdown
                v-model="perPage"
                :options="[5,10,20,50]"
                placeholder="Per page"
                class="w-28 dark-dropdown"
              />

              <!-- Role Filter -->
              <Dropdown
                v-model="form.role"
                :options="[{ label: 'All Roles', value: null }, ...roles.map(r => ({ label: r.name, value: r.name }))]"
                optionLabel="label"
                optionValue="value"
                placeholder="Filter by Role"
                class="w-48 dark-dropdown"
              />

              <!-- Search Input -->
              <div class="flex-1 min-w-[200px]">
                <span class="p-input-icon-left w-full">
                  <InputText
                    v-model="form.search"
                    placeholder="Search by permission name..."
                    class="w-full dark-input"
                  />
                </span>
              </div>

              <Button
                v-if="form.search || form.role"
                label="Reset"
                icon="pi pi-times"
                text
                size="small"
                class="dark:text-slate-300 dark:hover:bg-slate-700"
                @click="() => { form.search = ''; form.role = null }"
              />
            </div>
          </div>
        </div>

        <!-- Table Card - TETAP PUTIH DI DARK MODE -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <DataTable
            :value="permissions.data"
            :paginator="true"
            :first="first"
            :rows="rows"
            :totalRecords="permissions.total"
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

            <!-- Permission Name Column -->
            <Column field="name" header="Permission Name" class="min-w-[250px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <i class="pi pi-key text-indigo-600"></i>
                  <span class="font-semibold text-gray-900">{{ slotProps.data.name }}</span>
                </div>
              </template>
            </Column>

            <!-- Permission Type Badge -->
            <Column header="Type" class="min-w-[120px]">
              <template #body="slotProps">
                <Tag
                  :value="slotProps.data.name.split('.')[1] || 'general'"
                  :severity="getPermissionTypeSeverity(slotProps.data.name)"
                  class="text-xs font-medium"
                />
              </template>
            </Column>

            <!-- Roles Column -->
            <Column header="Roles" class="min-w-[280px]">
              <template #body="slotProps">
                <div v-if="slotProps.data.roles && slotProps.data.roles.length > 0"
                     class="flex items-center gap-1">
                  <!-- Role utama -->
                  <Tag
                    :value="truncateRole(getDominantRole(slotProps.data.roles).name)"
                    severity="success"
                    class="w-20 text-center px-2 py-1 text-xs rounded-md"
                  />

                  <!-- +more dengan style konsisten -->
                  <button
                    v-if="slotProps.data.roles.length > 1"
                    @click="showRoles(slotProps.data)"
                    class="w-20 text-center px-2 py-1 text-xs rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 font-medium"
                  >
                    +{{ slotProps.data.roles.length - 1 }} more
                  </button>
                </div>

                <!-- Kalau gak ada role -->
                <Tag v-else value="No Roles" severity="secondary" class="w-20 text-center px-2 py-1 text-xs rounded-md" />
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Actions" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-2">
                  <Button
                    v-if="can('permissions.edit')"
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    @click="router.get(route('permissions.edit', slotProps.data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button
                    v-if="can('permissions.delete')"
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    @click="deletePermission(slotProps.data.id, slotProps.data.name)"
                    v-tooltip.top="'Delete'"
                  />
                </div>
              </template>
            </Column>

            <!-- Empty State -->
            <template #empty>
              <div class="text-center py-12">
                <i class="pi pi-inbox text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">No permissions found</p>
              </div>
            </template>
          </DataTable>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Dark mode untuk input & dropdown */
.dark :deep(.dark-input.p-inputtext) {
  background: #334155;
  border-color: #475569;
  color: #e2e8f0;
}

.dark :deep(.dark-dropdown .p-dropdown) {
  background: #334155;
  border-color: #475569;
  color: #e2e8f0;
}

.dark :deep(.dark-dropdown .p-dropdown-trigger) {
  color: #94a3b8;
}

.dark :deep(.p-dropdown-panel) {
  background: #334155;
  border-color: #475569;
}

.dark :deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item) {
  color: #e2e8f0;
}

.dark :deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item:hover) {
  background: #475569;
}

.dark :deep(.p-dropdown-panel .p-dropdown-items .p-dropdown-item.p-highlight) {
  background: #6366f1;
  color: white;
}

/* DataTable styling - TETAP LIGHT MODE */
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
