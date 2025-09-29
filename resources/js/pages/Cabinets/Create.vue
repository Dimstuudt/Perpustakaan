<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Swal from 'sweetalert2'
import { type BreadcrumbItem } from '@/types'

const form = useForm({
  name: '',
  code: '',
  description: '',
})

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Cabinets', href: route('cabinets.index') },
  { title: 'Add Cabinet', href: route('cabinets.create') },
]

function submit() {
  Swal.fire({
    title: 'Are you sure?',
    text: 'Make sure cabinet data is correct before saving!',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, save it!',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      form.post(route('cabinets.store'), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'Saved!',
            text: 'Cabinet has been added successfully.',
            timer: 2000,
            showConfirmButton: false,
          })
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Something went wrong while saving cabinet.',
          })
        },
      })
    }
  })
}
</script>

<template>
  <Head title="Add Cabinet" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold">Add Cabinet</h2>

      <div class="space-y-4">
        <div>
          <label>Name</label>
          <InputText v-model="form.name" class="w-full" />
          <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
        </div>

        <div>
          <label>Code</label>
          <InputText v-model="form.code" class="w-full" />
          <div v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</div>
        </div>

        <div>
          <label>Description</label>
          <InputText v-model="form.description" class="w-full" />
          <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
        </div>

        <div class="flex gap-2">
          <Button label="Save" icon="pi pi-check" severity="success" @click="submit" />
          <Button label="Back" icon="pi pi-arrow-left" severity="secondary" @click="router.get(route('cabinets.index'))" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
