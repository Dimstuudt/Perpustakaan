<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'
import { ref, watch, computed } from 'vue'

// PrimeVue
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Tag from 'primevue/tag'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' }
]

const props = defineProps<{
  categories: {
    data: {
      id: number
      name: string
      books_count: number
    }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
}>()

// State
const search = ref('')
const perPage = ref(props.categories.per_page)
const first = ref((props.categories.current_page - 1) * props.categories.per_page)
const rows = ref(props.categories.per_page)
const selected = ref<any[]>([])

// Computed untuk stats yang selalu fresh dari props
const totalBooksCount = computed(() =>
  props.categories.data.reduce((sum, cat) => sum + cat.books_count, 0)
)

// Watchers - Server-side filtering dan pagination
watch(search, () => {
  router.get(
    route('categories.index'),
    { search: search.value, per_page: perPage.value, page: 1 },
    { preserveState: true, replace: true }
  )
  first.value = 0
  rows.value = perPage.value
  selected.value = []
})

watch(perPage, () => {
  router.get(
    route('categories.index'),
    { search: search.value, per_page: perPage.value, page: 1 },
    { preserveState: true, replace: true }
  )
  first.value = 0
  rows.value = perPage.value
  selected.value = []
})

// Functions
function deleteCategory(id: number, name: string) {
  Swal.fire({
    title: 'Delete Category?',
    html: `Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('categories.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Category has been deleted.', 'success')
          router.reload()
        },
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

function bulkDelete() {
  if (!selected.value.length) return

  Swal.fire({
    title: 'Delete Selected Categories?',
    html: `You are about to delete <strong>${selected.value.length}</strong> categor${selected.value.length > 1 ? 'ies' : 'y'}. This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete them!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('categories.bulkDelete'), {
        ids: selected.value.map((c) => c.id)
      }, {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Categories have been deleted.', 'success')
          selected.value = []
          router.reload()
        },
        onError: () => Swal.fire('Failed!', 'Something went wrong.', 'error')
      })
    }
  })
}

function onPage(event: { first: number; rows: number; page: number }) {
  const pageNumber = event.page + 1
  router.get(
    route('categories.index'),
    { search: search.value, per_page: perPage.value, page: pageNumber },
    { preserveState: true, replace: true }
  )
  selected.value = []
}

function getStatusSeverity(count: number) {
  if (count === 0) return 'secondary'
  if (count < 5) return 'warning'
  return 'success'
}
</script>

<template>
  <Head title="Categories Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Categories Management</h1>
              <p class="text-gray-600 mt-1">Manage book categories</p>
            </div>
            <div class="flex gap-3">
              <Button
                v-if="can('categories.create')"
                label="Create Category"
                icon="pi pi-plus"
                class="bg-indigo-600 hover:bg-indigo-700"
                @click="router.get(route('categories.create'))"
              />
              <Button
                label="Trash"
                icon="pi pi-trash"
                severity="secondary"
                @click="router.get(route('categories.trashed'))"
              />
            </div>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Categories</p>
                  <p class="text-2xl font-bold text-gray-900">{{ categories.total }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-folder text-indigo-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Total Books</p>
                  <p class="text-2xl font-bold text-indigo-600">
                    {{ totalBooksCount }}
                  </p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-book text-indigo-600 text-xl"></i>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Selected</p>
                  <p class="text-2xl font-bold text-green-600">{{ selected.length }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <i class="pi pi-check-circle text-green-600 text-xl"></i>
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
                :options="[5, 10, 20, 50]"
                placeholder="Per page"
                class="w-28"
              />

              <div class="flex-1 min-w-[200px]">
                <span class="p-input-icon-left w-full">
                  <InputText
                    v-model="search"
                    placeholder="Search by category name..."
                    class="w-full"
                  />
                </span>
              </div>

              <Button
                v-if="selected.length && can('categories.delete')"
                label="Delete Selected"
                icon="pi pi-trash"
                severity="danger"
                @click="bulkDelete"
                :badge="String(selected.length)"
              />

              <Button
                v-if="search"
                label="Reset"
                icon="pi pi-times"
                text
                size="small"
                @click="search = ''"
              />
            </div>
          </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <DataTable
            v-model:selection="selected"
            :value="categories.data"
            :paginator="true"
            :first="first"
            :rows="rows"
            :totalRecords="categories.total"
            :lazy="true"
            dataKey="id"
            @page="onPage"
            class="custom-datatable"
            stripedRows
          >
            <!-- Checkbox Selection Column -->
            <Column selectionMode="multiple" style="width: 3rem" />

            <!-- ID Column -->
            <Column field="id" header="ID" class="min-w-[70px]">
              <template #body="slotProps">
                <span class="font-medium text-gray-900">#{{ slotProps.data.id }}</span>
              </template>
            </Column>

            <!-- Category Name Column -->
            <Column header="Category Name" class="min-w-[250px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <i class="pi pi-folder text-indigo-600"></i>
                  <span class="font-semibold text-gray-900">{{ slotProps.data.name }}</span>
                </div>
              </template>
            </Column>

            <!-- Books Count Column -->
            <Column header="Books" class="min-w-[120px]">
              <template #body="slotProps">
                <div class="flex items-center gap-2">
                  <Tag
                    :value="slotProps.data.books_count"
                    :severity="getStatusSeverity(slotProps.data.books_count)"
                    class="text-sm font-semibold"
                  />
                  <span class="text-sm text-gray-600">
                    {{ slotProps.data.books_count === 1 ? 'book' : 'books' }}
                  </span>
                </div>
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Actions" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-2">
                  <Button
                    v-if="can('categories.edit')"
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    @click="router.get(route('categories.edit', slotProps.data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button
                    v-if="can('categories.delete')"
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    @click="deleteCategory(slotProps.data.id, slotProps.data.name)"
                    v-tooltip.top="'Delete'"
                  />
                </div>
              </template>
            </Column>

            <!-- Empty State -->
            <template #empty>
              <div class="text-center py-12">
                <i class="pi pi-inbox text-4xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500">No categories found</p>
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
