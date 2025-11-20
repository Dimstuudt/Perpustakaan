<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputError from '@/components/InputError.vue'
import InputText from 'primevue/inputtext'
import Checkbox from 'primevue/checkbox'

interface Role {
  id: number
  name: string
}

interface Permission {
  id: number
  name: string
}

const props = defineProps<{
  permission: Permission
  roles: Role[]
  rolePermissions: string[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Permissions', href: '/permissions' },
  { title: 'Edit Permission', href: '#' },
]

const form = useForm({
  name: props.permission.name,
  roles: props.rolePermissions || [],
})

const submit = () => {
  form.post(route('permissions.update', props.permission.id), {
    onError: (errors) => {
      console.log(errors)
    },
  })
}

const isAllSelected = () => {
  return props.roles.every(r => form.roles.includes(r.name))
}

const toggleAll = () => {
  if (isAllSelected()) {
    form.roles = []
  } else {
    form.roles = props.roles.map(r => r.name)
  }
}
</script>

<template>
  <Head title="Edit Permission" />
  <AppLayout :breadcrumbs="breadcrumbs">
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Edit Permission</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">Update permission name and assign to roles</p>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <form @submit.prevent="submit">

        <!-- Permission Name Section -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
              Permission Name
            </label>

            <InputText
              id="name"
              v-model="form.name"
              type="text"
              placeholder="e.g., users.view, books.edit"
              class="w-full dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
              :class="{ 'border-red-500': form.errors.name }"
              required
              autofocus
            />

            <p class="text-xs text-gray-500 dark:text-gray-400">
              Use dot notation for grouping (e.g., resource.action)
            </p>
            <InputError :message="form.errors.name" />
          </div>
        </div>

        <!-- Roles Section -->
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Assign to Roles</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  Select which roles should have this permission ({{ form.roles.length }} selected)
                </p>
              </div>

              <button
                type="button"
                @click="toggleAll"
                class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors"
              >
                {{ isAllSelected() ? 'Deselect All' : 'Select All' }}
              </button>
            </div>

            <InputError :message="form.errors.roles" />

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

              <div
                v-for="role in roles"
                :key="role.name"
                class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3 border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-600 transition-all cursor-pointer"
                @click="() => {
                  const index = form.roles.indexOf(role.name)
                  if (index > -1) {
                    form.roles.splice(index, 1)
                  } else {
                    form.roles.push(role.name)
                  }
                }"
              >
                <Checkbox
                  v-model="form.roles"
                  :value="role.name"
                  :inputId="'role-' + role.name"
                />

                <div class="flex items-center gap-2 flex-1">
                  <i class="pi pi-users text-indigo-600 dark:text-indigo-400"></i>
                  <label
                    :for="'role-' + role.name"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 cursor-pointer"
                  >
                    {{ role.name }}
                  </label>
                </div>
              </div>

            </div>

            <!-- Empty State -->
            <div v-if="roles.length === 0" class="text-center py-8">
              <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="pi pi-users text-2xl text-gray-400 dark:text-gray-300"></i>
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400">No roles available</p>
            </div>

          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
          <Button
            label="Cancel"
            severity="secondary"
            outlined
            type="button"
            class="dark:text-gray-300 dark:border-gray-500"
            @click="$inertia.visit(route('permissions.index'))"
          />
          <Button
            label="Save Changes"
            icon="pi pi-check"
            iconPos="right"
            :loading="form.processing"
            type="submit"
          />
        </div>

      </form>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <i class="pi pi-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
        <div>
          <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">Permission Naming Convention</h4>
          <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
            <li>• Use lowercase letters and dots</li>
            <li>• Format: resource.action</li>
            <li>• Be consistent with naming</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Example Card -->
    <div class="mt-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <i class="pi pi-lightbulb text-gray-600 dark:text-gray-300 mt-0.5"></i>
        <div>
          <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Examples</h4>
          <div class="flex flex-wrap gap-2 text-xs">
            <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded">users.view</code>
            <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded">users.create</code>
            <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded">books.edit</code>
            <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded">books.delete</code>
            <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded">categories.manage</code>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

  </AppLayout>
</template>

<style scoped>
/* Custom checkbox styling */
:deep(.p-checkbox) {
  flex-shrink: 0;
}

/* Code styling */
code {
  font-family: 'Courier New', Courier, monospace;
  font-size: 0.875rem;
}
</style
