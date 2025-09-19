<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

// PrimeVue
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { ref, computed } from 'vue'

const props = defineProps<{
  categories: {
    data: { id: number; name: string }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
  trashed?: boolean
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
]

// Search
const search = ref('')
const filteredCategories = computed(() =>
  props.categories.data.filter((cat) =>
    cat.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

function deleteCategory(id: number) {
  Swal.fire({
    title: 'Yakin hapus?',
    text: 'Kategori akan dipindahkan ke sampah.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('categories.destroy', id), {
        onSuccess: () => Swal.fire('Terhapus!', 'Kategori masuk ke sampah.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
      })
    }
  })
}

function restoreCategory(id: number) {
  Swal.fire({
    title: 'Yakin restore?',
    text: 'Kategori akan dikembalikan.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('categories.restore', id), {}, {
        onSuccess: () => Swal.fire('Berhasil!', 'Kategori dipulihkan.', 'success'),
      })
    }
  })
}

function forceDeleteCategory(id: number) {
  Swal.fire({
    title: 'Hapus permanen?',
    text: 'Kategori akan dihapus selamanya!',
    icon: 'error',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen!',
    confirmButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('categories.forceDelete', id), {
        onSuccess: () => Swal.fire('Terhapus!', 'Kategori dihapus permanen.', 'success'),
      })
    }
  })
}
</script>

<template>
  <Head :title="props.trashed ? 'Trash Categories' : 'Categories'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">
          {{ props.trashed ? '🗑️ Sampah Kategori' : '📂 Daftar Kategori' }}
        </h1>
        <div class="flex gap-2">
          <Link
            v-if="!props.trashed && can('categories.create')"
            :href="route('categories.create')"
            class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600"
          >
            + Tambah
          </Link>
          <Link
            v-if="!props.trashed"
            :href="route('categories.trashed')"
            class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600"
          >
            🗑️ Lihat Sampah
          </Link>
          <Link
            v-else
            :href="route('categories.index')"
            class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600"
          >
            ⬅️ Kembali
          </Link>
        </div>
      </div>

      <!-- Search -->
      <div class="mb-3 w-1/2">
        <InputText v-model="search" placeholder="Cari kategori..." class="w-full" />
      </div>

      <!-- DataTable -->
      <DataTable
        :value="filteredCategories"
        responsiveLayout="scroll"
        class="p-datatable-sm p-datatable-gridlines text-sm"
      >
        <Column field="name" header="Nama Kategori" sortable />
        <Column header="Aksi" style="width: 250px">
          <template #body="slotProps">
            <div class="flex gap-1 justify-center">
              <template v-if="!props.trashed">
                <Button
                  v-if="can('categories.edit')"
                  label="Edit"
                  class="p-button-sm p-button-info text-xs"
                  @click="() => router.get(route('categories.edit', slotProps.data.id))"
                />
                <Button
                  v-if="can('categories.delete')"
                  label="Hapus"
                  class="p-button-sm p-button-danger text-xs"
                  @click="deleteCategory(slotProps.data.id)"
                />
              </template>
              <template v-else>
                <Button
                  v-if="can('categories.restore')"
                  label="Restore"
                  class="p-button-sm p-button-success text-xs"
                  @click="restoreCategory(slotProps.data.id)"
                />
                <Button
                  v-if="can('categories.forceDelete')"
                  label="Hapus Permanen"
                  class="p-button-sm p-button-danger text-xs"
                  @click="forceDeleteCategory(slotProps.data.id)"
                />
              </template>
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- Pagination -->
      <div
        class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between mt-4"
      >
        <span class="text-xs xs:text-sm text-gray-900">
          Showing
          {{ (props.categories.current_page - 1) * props.categories.per_page + 1 }}
          to
          {{
            props.categories.current_page * props.categories.per_page > props.categories.total
              ? props.categories.total
              : props.categories.current_page * props.categories.per_page
          }}
          of {{ props.categories.total }} entries
        </span>
        <div class="inline-flex mt-2 xs:mt-0">
          <button
            @click="router.get(props.categories.prev_page_url)"
            :disabled="!props.categories.prev_page_url"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l"
          >
            Prev
          </button>
          <button
            @click="router.get(props.categories.next_page_url)"
            :disabled="!props.categories.next_page_url"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
