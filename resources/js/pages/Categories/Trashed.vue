<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { can } from '@/lib/can'

import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import { ref, computed } from 'vue'

const props = defineProps<{
  categories: {
    data: { id: number; name: string; deleted_at: string }[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    next_page_url: string | null
    prev_page_url: string | null
  }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Categories', href: '/categories' },
  { title: 'Trashed', href: '/categories/trashed' },
]

const search = ref('')
const selected = ref<any[]>([])

const filteredCategories = computed(() =>
  props.categories.data.filter(cat =>
    cat.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

// Bulk Restore
function bulkRestore() {
  if (!selected.value.length) return

  Swal.fire({
    title: 'Restore kategori terpilih?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, restore!',
    confirmButtonColor: '#28a745'
  }).then(result => {
    if (result.isConfirmed) {
      // Gunakan POST sesuai rute backend kamu
      router.post(route('categories.bulkRestore'), {
        ids: selected.value.map(c => c.id)
      }, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Kategori berhasil direstore.', 'success')
          selected.value = []
          router.reload()
        },
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}

// Bulk Force Delete
function bulkForceDelete() {
  if (!selected.value.length) return

  Swal.fire({
    title: 'Hapus permanen kategori terpilih?',
    text: 'Data tidak bisa dikembalikan lagi!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen!',
    confirmButtonColor: '#d33'
  }).then(result => {
    if (result.isConfirmed) {
      // Gunakan POST sesuai rute backend kamu
      router.post(route('categories.bulkForceDelete'), {
        ids: selected.value.map(c => c.id)
      }, {
        onSuccess: () => {
          Swal.fire('Terhapus!', 'Kategori dihapus permanen.', 'success')
          selected.value = []
          router.reload()
        },
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error')
      })
    }
  })
}


// Single Actions
function restoreCategory(id: number) {
  router.put(route('categories.restore', id), {}, {
    onSuccess: () => {
      Swal.fire('Berhasil!', 'Kategori berhasil direstore.', 'success')
      router.reload()
    },
    onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
  })
}

function forceDeleteCategory(id: number) {
  Swal.fire({
    title: 'Yakin hapus permanen?',
    text: 'Data ini tidak bisa dikembalikan lagi!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus permanen!',
    confirmButtonColor: '#d33',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('categories.forceDelete', id), {
        onSuccess: () => Swal.fire('Terhapus!', 'Kategori dihapus permanen.', 'success')
      })
    }
  })
}
</script>

<template>
  <Head title="Trashed Categories" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">🗑️ Kategori Terhapus</h1>
        <Link
          :href="route('categories.index')"
          class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600"
        >
          ⬅️ Kembali
        </Link>
      </div>

      <!-- Bulk Action + Search -->
      <div class="mb-3 flex items-center gap-2">
        <Button
          v-if="selected.length && can('categories.restore')"
          label="Restore Terpilih"
          class="p-button-sm p-button-success"
          @click="bulkRestore"
        />
        <Button
          v-if="selected.length && can('categories.forceDelete')"
          label="Hapus Permanen Terpilih"
          class="p-button-sm p-button-danger"
          @click="bulkForceDelete"
        />
        <InputText v-model="search" placeholder="Cari kategori..." class="w-1/2" />
      </div>

      <!-- DataTable -->
      <DataTable
        v-model:selection="selected"
        :value="filteredCategories"
        dataKey="id"
        responsiveLayout="scroll"
        class="p-datatable-sm p-datatable-gridlines text-sm"
      >
        <Column selectionMode="multiple" style="width: 3rem" />
        <Column field="name" header="Nama Kategori" sortable />
        <Column field="deleted_at" header="Dihapus Pada" sortable>
          <template #body="slotProps">
            {{ slotProps.data.deleted_at ? new Date(slotProps.data.deleted_at).toLocaleString('id-ID') : '-' }}
          </template>
        </Column>
        <Column header="Aksi">
          <template #body="slotProps">
            <div class="flex gap-1 justify-center">
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
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- Pagination -->
      <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between mt-4">
        <span class="text-xs">
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
