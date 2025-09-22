<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { can } from '@/lib/can';
import { ref, watch } from 'vue';

// PrimeVue components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Roles', href: '/roles' }];

const props = defineProps<{
  roles: {
    data: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
  };
  permissions: any[];
  filters: {
    search?: string;
    permission?: string | null;
  };
}>();

// flash message
const flash = page.props?.flash?.message;
if (flash) {
  Swal.fire({
    title: 'Success!',
    text: flash,
    icon: 'success',
    timer: 1200,
    showConfirmButton: false,
  });
}

// Delete role
function deleteRole(id: number) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This role will be deleted!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('roles.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Role has been deleted.', 'success');
        },
        onError: () => {
          Swal.fire('Failed!', 'Something went wrong.', 'error');
        },
      });
    }
  });
}

// Pagination & rows per page
const perPage = ref(Number(new URLSearchParams(window.location.search).get('per_page')) || 10);
const first = ref((props.roles.current_page - 1) * props.roles.per_page);
const rows = ref(props.roles.per_page);

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1;
  router.get(route('roles.index'), { page: pageNumber, per_page: event.rows }, { preserveState: true, replace: true });
  first.value = event.first;
  rows.value = event.rows;
}

watch(perPage, (value) => {
  router.get(route('roles.index'), { page: 1, per_page: value }, { preserveState: true, replace: true });
  rows.value = value;
  first.value = 0;
});

// Filters
const form = useForm({
  search: props.filters.search || '',
  permission: props.filters.permission || null,
});

watch(() => form.search, () => {
  form.get(route('roles.index'), { preserveScroll: true, preserveState: true });
});

watch(() => form.permission, () => {
  form.get(route('roles.index'), { preserveState: true, replace: true });
});
</script>

<template>
  <Head title="Roles" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold">Roles</h2>
        <Button
          v-if="can('roles.create')"
          label="Add Role"
          icon="pi pi-plus"
          severity="success"
          @click="router.get(route('roles.create'))"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3 items-center">
        <Dropdown
          v-model="perPage"
          :options="[5,10,20]"
          placeholder="Per page"
          class="w-32"
        />
      <Dropdown
  v-model="form.permission"
  :options="[{label:'All', value:null}, ...permissions]"
  optionLabel="name"
  optionValue="name"
  placeholder="Filter by Permission"
  class="w-48 text-sm text-gray-700"
/>

        <span class="p-input-icon-left">
          <InputText
            v-model="form.search"
            placeholder="Search role..."
            class="w-64"
          />
        </span>
      </div>

      <!-- Roles Table -->
      <DataTable
        :value="roles.data"
        :paginator="true"
        :first="first"
        :rows="rows"
        :totalRecords="roles.total"
        :rowsPerPageOptions="[5,10,20]"
        lazy
        @page="onPage"
      >
        <Column field="id" header="ID" />
        <Column field="name" header="Name" />
        <Column header="Permissions">
  <template #body="slotProps">
    <div class="flex flex-wrap gap-1">
      <Tag
        v-for="perm in slotProps.data.permissions.slice(0,5)"
        :key="perm.id"
        :value="perm.name"
        severity="success"
        class="mr-1 mb-1"
      />
      <Tag
        v-if="slotProps.data.permissions.length > 5"
        :value="'+' + (slotProps.data.permissions.length - 5) + ' more'"
        class="bg-gray-100 text-gray-600 mb-1"
        :title="slotProps.data.permissions.map(p => p.name).join(', ')"
      />
    </div>
  </template>
</Column>

        <Column header="Action">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Button
                v-if="can('roles.edit')"
                label="Edit"
                icon="pi pi-pencil"
                size="small"
                severity="info"
                as="a"
                :href="route('roles.edit', slotProps.data.id)"
              />
              <Button
                v-if="can('roles.delete')"
                label="Delete"
                icon="pi pi-trash"
                size="small"
                severity="danger"
                @click="deleteRole(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
