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
    timer: 1200,
    showConfirmButton: false,
  })
}

// Delete user
function deleteUser(id: number) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This user will be deleted!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(route('users.destroy', id), {
        onSuccess: () => Swal.fire('Deleted!', 'User has been deleted.', 'success'),
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

// Toggle status
function toggleStatus(id: number) {
  router.put(route('users.toggleStatus', id), {}, {
    onError: (errors) => console.error(errors)
  })
}

// Show roles (popup)
function showRoles(user: any) {
  const rolesList = user.roles.map((r: any) => `• ${r.name}`).join('<br>')
  Swal.fire({
    title: `${user.name}'s Roles`,
    html: rolesList || 'No roles assigned',
    icon: 'info',
    confirmButtonText: 'Close'
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
</script>

<template>
  <Head title="Users" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Users</h2>
        <Button v-if="can('users.create')"
          label="Add User"
          icon="pi pi-plus"
          severity="success"
          @click="router.get(route('users.create'))"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3 items-center">
        <Dropdown
          v-model="perPage"
          :options="[5,10,20]"
          placeholder="Per page"
          class="w-32 text-sm text-gray-700"
        />

        <Dropdown
          v-model="form.role"
          :options="[{ label: 'All', value: null }, ...roles]"
          optionLabel="name"
          optionValue="name"
          placeholder="Filter by Role"
          class="w-48 text-sm text-gray-700 truncate"
        />

        <span class="p-input-icon-left">
          <InputText
            v-model="form.search"
            placeholder="Search user..."
            class="w-64 text-sm text-gray-700"
          />
        </span>
      </div>

      <!-- Table -->
      <DataTable
        :value="users.data"
        :paginator="true"
        :first="first"
        :rows="rows"
        :totalRecords="users.total"
        :rowsPerPageOptions="[5,10,20]"
        :lazy="true"
        :key="users.current_page"
        @page="onPage"
      >
      <Column header="Avatar & Name">
  <template #body="slotProps">
    <div class="flex items-center gap-3">
     <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
    <img
        class="w-full h-full object-cover"
        :src="slotProps.data.avatar
            ? slotProps.data.avatar 
            : `https://ui-avatars.com/api/?name=${getInitials(slotProps.data.username)}&background=random`"
        :alt="slotProps.data.username + ' avatar'"
    />
</div>

      <span class="whitespace-nowrap overflow-hidden text-ellipsis block max-w-[200px]">
        {{ slotProps.data.name }}
      </span>
    </div>
  </template>
</Column>


        <Column field="username" header="Username" />
        <Column header="Role">
          <template #body="slotProps">
            <!-- 1 role -->
            <div v-if="slotProps.data.roles.length === 1">
              <button
                class="text-sm bg-indigo-500 hover:bg-indigo-700 text-white py-1 px-4 rounded"
              >
                {{ slotProps.data.roles[0].name }}
              </button>
            </div>

            <!-- >1 role -->
            <div v-else>
              <button
                @click="showRoles(slotProps.data)"
                :class="[
                  'text-sm text-white py-1 px-3 rounded',
                  slotProps.data.roles.some(r => r.name === 'Super Admin')
                    ? 'bg-orange-500 hover:bg-orange-700'
                    : slotProps.data.roles.some(r => r.name === 'Admin')
                      ? 'bg-yellow-500 hover:bg-yellow-700'
                      : 'bg-indigo-500 hover:bg-indigo-700'
                ]"
              >
                Roles
              </button>
            </div>
          </template>
        </Column>
        <Column field="email" header="Email" />

        <Column header="Status">
          <template #body="slotProps">
            <span v-if="can('users.toggleStatus')" @click="toggleStatus(slotProps.data.id)"
              class="cursor-pointer relative inline-block px-3 py-1 font-semibold leading-tight"
              :class="slotProps.data.is_active ? 'text-green-900' : 'text-red-900'">
              <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"
                :class="slotProps.data.is_active ? 'bg-green-200' : 'bg-red-200'"></span>
              <span class="relative">{{ slotProps.data.is_active ? 'Active' : 'Inactive' }}</span>
            </span>
            <span v-else
              class="relative inline-block px-3 py-1 font-semibold leading-tight"
              :class="slotProps.data.is_active ? 'text-green-900' : 'text-red-900'">
              <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"
                :class="slotProps.data.is_active ? 'bg-green-200' : 'bg-red-200'"></span>
              <span class="relative">{{ slotProps.data.is_active ? 'Active' : 'Inactive' }}</span>
            </span>
          </template>
        </Column>

        <Column header="Action">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Link
                v-if="can('users.show')"
                :href="route('users.show', slotProps.data.id)"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600"
              >
                Show
              </Link>
              <Link
                v-if="can('users.edit')"
                :href="route('users.edit', slotProps.data.id)"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600"
              >
                Edit
              </Link>
              <button
                v-if="can('users.delete')"
                @click="deleteUser(slotProps.data.id)"
                class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600"
              >
                Delete
              </button>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
