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
  form.put(route('permissions.update', props.permission.id), {
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
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Edit Permission</h1>
              <p class="text-gray-600 mt-1">Update permission name and assign to roles</p>
            </div>
            <Button
              label="Back"
              icon="pi pi-arrow-left"
              outlined
              size="small"
              @click="$inertia.visit(route('permissions.index'))"
            />
          </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <form @submit.prevent="submit">

            <!-- Permission Name Section -->
            <div class="p-6 border-b border-gray-200">
              <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-gray-900">
                  Permission Name
                </label>
                <InputText
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="e.g., users.view, books.edit"
                  class="w-full"
                  :class="{ 'border-red-500': form.errors.name }"
                  required
                  autofocus
                />
                <p class="text-xs text-gray-500">
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
                    <h3 class="text-sm font-semibold text-gray-900">Assign to Roles</h3>
                    <p class="text-xs text-gray-500 mt-1">
                      Select which roles should have this permission ({{ form.roles.length }} selected)
                    </p>
                  </div>
                  <button
                    type="button"
                    @click="toggleAll"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
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
                    class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all cursor-pointer"
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
                      <i class="pi pi-users text-indigo-600"></i>
                      <label
                        :for="'role-' + role.name"
                        class="text-sm font-medium text-gray-700 cursor-pointer"
                      >
                        {{ role.name }}
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-if="roles.length === 0" class="text-center py-8">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="pi pi-users text-2xl text-gray-400"></i>
                  </div>
                  <p class="text-sm text-gray-500">No roles available</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
              <Button
                label="Cancel"
                severity="secondary"
                outlined
                @click="$inertia.visit(route('permissions.index'))"
                type="button"
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
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <i class="pi pi-info-circle text-blue-600 mt-0.5"></i>
            <div>
              <h4 class="text-sm font-semibold text-blue-900 mb-1">Permission Naming Convention</h4>
              <ul class="text-sm text-blue-800 space-y-1">
                <li>• Use lowercase letters and dots (e.g., <code class="bg-blue-100 px-1 rounded">users.view</code>)</li>
                <li>• Format: <code class="bg-blue-100 px-1 rounded">resource.action</code> (e.g., books.edit, categories.delete)</li>
                <li>• Be consistent with naming across your application</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Example Card -->
        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <i class="pi pi-lightbulb text-gray-600 mt-0.5"></i>
            <div>
              <h4 class="text-sm font-semibold text-gray-900 mb-1">Examples</h4>
              <div class="flex flex-wrap gap-2 text-xs">
                <code class="bg-gray-200 px-2 py-1 rounded">users.view</code>
                <code class="bg-gray-200 px-2 py-1 rounded">users.create</code>
                <code class="bg-gray-200 px-2 py-1 rounded">books.edit</code>
                <code class="bg-gray-200 px-2 py-1 rounded">books.delete</code>
                <code class="bg-gray-200 px-2 py-1 rounded">categories.manage</code>
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
