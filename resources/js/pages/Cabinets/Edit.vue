<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import MultiSelect from 'primevue/multiselect'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  cabinet: { id: number; code: string; name: string; description?: string; racks: any[] }
  racks: any[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cabinets', href: route('cabinets.index') },
  { title: 'Edit Cabinet', href: route('cabinets.edit', props.cabinet.id) },
]

const form = useForm({
  code: props.cabinet?.code || '',
  name: props.cabinet?.name || '',
  description: props.cabinet?.description || '',
  rack_ids: [] as number[]
})

function submit() {
  form.put(route('cabinets.update', props.cabinet.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: 'Cabinet has been updated successfully.',
        timer: 2000,
        showConfirmButton: false,
      })
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Failed!',
        text: 'Something went wrong while updating the cabinet.',
      })
    }
  })
}

function removeRack(id: number) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This rack will be removed from the cabinet!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, remove it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('cabinets.update', props.cabinet.id), {
        code: form.code,
        name: form.name,
        description: form.description,
        remove_id: id
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Removed!',
            text: 'Rack has been removed from the cabinet.',
            timer: 2000,
            showConfirmButton: false,
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Something went wrong while removing the rack.',
          })
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Edit Cabinet" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6" v-if="cabinet">
      <h2 class="text-2xl font-semibold">Edit Cabinet</h2>

      <div class="grid gap-4 max-w-lg">
        <div>
          <label for="code" class="block text-sm font-medium mb-1">Code</label>
          <InputText id="code" v-model="form.code" placeholder="Enter cabinet code" class="w-full" />
        </div>

        <div>
          <label for="name" class="block text-sm font-medium mb-1">Name</label>
          <InputText id="name" v-model="form.name" placeholder="Enter cabinet name" class="w-full" />
        </div>

        <div>
          <label for="description" class="block text-sm font-medium mb-1">Description</label>
          <InputText id="description" v-model="form.description" placeholder="Enter description" class="w-full" />
        </div>

        <div>
          <label for="racks" class="block text-sm font-medium mb-1">Assign New Racks</label>
          <MultiSelect
            id="racks"
            v-model="form.rack_ids"
            :options="racks || []"
            optionLabel="name"
            optionValue="id"
            placeholder="Select racks"
            class="w-full"
          />
        </div>

        <div>
          <h3 class="font-medium mb-2">Racks in this Cabinet</h3>
          <ul class="divide-y border rounded-md">
            <li
              v-for="rack in cabinet.racks || []"
              :key="rack.id"
              class="flex justify-between items-center p-2"
            >
              <span>{{ rack.name }}</span>
              <Button
                label="Remove"
                icon="pi pi-times"
                severity="danger"
                text
                @click="removeRack(rack.id)"
              />
            </li>
            <li v-if="!cabinet.racks || cabinet.racks.length === 0" class="p-2 text-gray-500 text-sm">
              No racks in this cabinet.
            </li>
          </ul>
        </div>

        <div class="flex gap-2">
          <Button label="Save" icon="pi pi-save" severity="success" @click="submit" />
          <Button label="Back" icon="pi pi-arrow-left" severity="secondary" @click="router.get(route('cabinets.index'))" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
