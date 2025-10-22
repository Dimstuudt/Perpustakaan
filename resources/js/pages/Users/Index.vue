<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'
import { useInitials } from '@/composables/useInitials'

// PrimeVue
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import Tag from 'primevue/tag'

const page = usePage()
const { getInitials } = useInitials()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' }
]

const props = defineProps<{
  users: {
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

// Delete user
function deleteUser(id: number) {
  Swal.fire({
    title: 'Delete User?',
    text: 'This action cannot be undone!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then(result => {
    if (result.isConfirmed) {
    router.post(route('users.destroy', id), {}, {
  onSuccess: () => Swal.fire('Deleted!', 'User has been deleted.', 'success'),
  onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
})

    }
  })
}

// Toggle status
function toggleStatus(id: number) {
router.post(route('users.toggleStatus', id), {}, {
  onError: (errors) => console.error(errors)
})
}

// Show roles (popup)
function showRoles(user: any) {
  const rolesList = user.roles.map((r: any) => `
    <div class="flex items-center gap-2 py-2 px-3 bg-gray-50 rounded-lg">
      <i class="pi pi-shield text-indigo-600"></i>
      <span class="font-medium">${r.name}</span>
    </div>
  `).join('')

  Swal.fire({
    title: `<div class="text-xl font-bold">${user.name}'s Roles</div>`,
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

// Auto reload saat filter berubah
watch([() => form.search, () => form.role, perPage], () => {
  router.get(route('users.index'), { ...queryParams.value, page: 1 }, { preserveState: true, replace: true })
  first.value = 0
  rows.value = perPage.value
})

// Paginator
const first = ref((props.users.current_page - 1) * props.users.per_page)
const rows = ref(props.users.per_page)

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1
  router.get(route('users.index'), { ...queryParams.value, page: pageNumber }, { preserveState: true, replace: true })
  first.value = event.first
  rows.value = event.rows
}

const getRoleSeverity = (roleName: string) => {
  if (roleName.toLowerCase().includes('super admin')) return 'danger'
  if (roleName.toLowerCase().includes('admin')) return 'warning'
  return 'info'
}

// Get dominant role based on priority
const getDominantRole = (roles: any[]) => {
  if (!roles || roles.length === 0) return null

  // Priority order
  const priority = ['super admin', 'admin', 'user']

  for (const p of priority) {
    const found = roles.find(r => r.name.toLowerCase().includes(p))
    if (found) return found
  }

  // If no priority match, return first role
  return roles[0]
}

const truncateRole = (roleName) => {
  if (!roleName) return '';
  return roleName.substring(0, 5); // ambil 5 huruf pertama
};

</script>

<template>
  <Head title="Users" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
              <p class="text-gray-600 mt-1">Manage user accounts and permissions</p>
            </div>
            <Button
              v-if="can('users.create')"
              label="Add User"
              icon="pi pi-plus"
              class="bg-indigo-600 hover:bg-indigo-700"
              @click="router.get(route('users.create'))"
            />
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Users</p>
                  <p class="text-2xl font-bold text-gray-900">{{ users.total }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-users text-indigo-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Active Users</p>
                  <p class="text-2xl font-bold text-green-600">
                    {{ users.data.filter(u => u.is_active).length }}
                  </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-check-circle text-green-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Inactive Users</p>
                  <p class="text-2xl font-bold text-red-600">
                    {{ users.data.filter(u => !u.is_active).length }}
                  </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-times-circle text-red-600 text-xl"></i>
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
                :options="[ 5,10,20,50]"
                placeholder="Per page"
                class="w-28"
              />

              <Dropdown
                v-model="form.role"
                :options="[{ label: 'All Roles', value: null }, ...roles.map(r => ({ label: r.name, value: r.name }))]"
                optionLabel="label"
                optionValue="value"
                placeholder="Filter by Role"
                class="w-48"
              />

              <div class="flex-1 min-w-[200px]">
                <span class="p-input-icon-left w-full">
                  <InputText
                    v-model="form.search"
                    placeholder="Search by name, username, or email..."
                    class="w-full"
                  />
                </span>
              </div>

              <Button
                v-if="form.search || form.role"
                label="Reset"
                icon="pi pi-times"
                text
                size="small"
                @click="() => { form.search = ''; form.role = null }"
              />
            </div>
          </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <DataTable
            :value="users.data"
            :paginator="true"
            :first="first"
            :rows="rows"
            :totalRecords="users.total"
            :lazy="true"
            @page="onPage"
            class="custom-datatable"
            stripedRows
          >
            <!-- User Column -->
            <Column header="User" class="min-w-[250px]">
              <template #body="slotProps">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-gray-200">
                    <img
                      class="w-full h-full object-cover"
                      :src="slotProps.data.avatar || `https://ui-avatars.com/api/?name=${getInitials(slotProps.data.username)}&background=6366f1&color=fff`"
                      :alt="slotProps.data.username"
                    />
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ slotProps.data.name }}</p>
                    <p class="text-sm text-gray-500 truncate">@{{ slotProps.data.username }}</p>
                  </div>
                </div>
              </template>
            </Column>

            <!-- Email Column -->
            <Column field="email" header="Email" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <i class="pi pi-envelope text-gray-400 text-sm"></i>
                  <span class="text-sm text-gray-700 truncate">{{ slotProps.data.email }}</span>
                </div>
              </template>
            </Column>

         <Column header="Role" class="min-w-[200px]">
  <template #body="slotProps">
    <div v-if="slotProps.data.roles && slotProps.data.roles.length > 0"
         class="flex items-center gap-1">
      <!-- Role utama -->
      <Tag
        :value="truncateRole(getDominantRole(slotProps.data.roles).name)"
        :severity="getRoleSeverity(getDominantRole(slotProps.data.roles).name)"
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
    <Tag v-else value="No Role" severity="secondary" class="w-20 text-center px-2 py-1 text-xs rounded-md" />
  </template>
</Column>


            <!-- Status Column -->
            <Column header="Status" class="min-w-[100px]">
              <template #body="slotProps">
                <button
                  v-if="can('users.toggleStatus')"
                  @click="toggleStatus(slotProps.data.id)"
                  class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold transition-all hover:scale-105"
                  :class="slotProps.data.is_active
                    ? 'bg-green-100 text-green-800 hover:bg-green-200'
                    : 'bg-red-100 text-red-800 hover:bg-red-200'"
                >
                  <span class="w-2 h-2 rounded-full" :class="slotProps.data.is_active ? 'bg-green-600' : 'bg-red-600'"></span>
                  {{ slotProps.data.is_active ? 'Active' : 'Inactive' }}
                </button>
                <span
                  v-else
                  class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
                  :class="slotProps.data.is_active
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'"
                >
                  <span class="w-2 h-2 rounded-full" :class="slotProps.data.is_active ? 'bg-green-600' : 'bg-red-600'"></span>
                  {{ slotProps.data.is_active ? 'Active' : 'Inactive' }}
                </span>
              </template>
            </Column>

            <!-- Action Column -->
            <Column header="Actions" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-2">
                  <Button
                    v-if="can('users.show')"
                    icon="pi pi-eye"
                    size="small"
                    severity="info"
                    outlined
                    @click="router.get(route('users.show', slotProps.data.id))"
                    v-tooltip.top="'View'"
                  />
                  <Button
                    v-if="can('users.edit')"
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    @click="router.get(route('users.edit', slotProps.data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button
                    v-if="can('users.delete')"
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    @click="deleteUser(slotProps.data.id)"
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
