<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'

const form = useForm({
  name: '',
  code: '',
  description: '',
})

function submit() {
  form.post(route('racks.store'))
}
</script>

<template>
  <Head title="Add Rack" />
  <AppLayout>
    <div class="p-6 space-y-6">
      <h2 class="text-2xl font-semibold">Add Rack</h2>

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
          <Button label="Back" icon="pi pi-arrow-left" severity="secondary" @click="router.get(route('racks.index'))" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
