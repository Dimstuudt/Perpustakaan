<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputError from '@/components/InputError.vue'
import InputText from 'primevue/inputtext'
import Checkbox from 'primevue/checkbox'
import { computed } from 'vue'

interface Permission {
  id: number
  name: string
}

interface Role {
  id: number
  name: string
}

const props = defineProps<{
  role: Role
  permissions: Permission[]
  rolePermissions: string[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Roles', href: '/roles' },
  { title: 'Edit Role', href: '#' },
]

const form = useForm({
  name: props.role.name,
  permissions: props.rolePermissions || [],
})

const submit = () => {
  form.put(route('roles.update', props.role.id), {
    onError: (errors) => {
      console.log(errors)
    },
  })
}

// Group permissions by prefix (e.g., "users.", "books.", etc.)
const groupedPermissions = computed(() => {
  const groups: Record<string, Permission[]> = {}

  props.permissions.forEach((permission) => {
    const parts = permission.name.split('.')
    const groupName = parts.length > 1 ? parts[0] : 'other'

    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(permission)
  })

  return groups
})

const isAllSelected = (groupPermissions: Permission[]) => {
  return groupPermissions.every(p => form.permissions.includes(p.name))
}

const toggleGroup = (groupPermissions: Permission[]) => {
  const allSelected = isAllSelected(groupPermissions)

  if (allSelected) {
    // Unselect all in group
    form.permissions = form.permissions.filter(
      p => !groupPermissions.some(gp => gp.name === p)
    )
  } else {
    // Select all in group
    const toAdd = groupPermissions
      .map(p => p.name)
      .filter(name => !form.permissions.includes(name))
    form.permissions = [...form.permissions, ...toAdd]
  }
}

const formatGroupName = (group: string) => {
  return group.charAt(0).toUpperCase() + group.slice(1)
}
</script>

<template>
  <Head title="Edit Role" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Edit Role</h1>
              <p class="text-gray-600 mt-1">Update role name and permissions</p>
            </div>
            <Button
              label="Back"
              icon="pi pi-arrow-left"
              outlined
              size="small"
              @click="$inertia.visit(route('roles.index'))"
            />
          </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <form @submit.prevent="submit">

            <!-- Role Name Section -->
            <div class="p-6 border-b border-gray-200">
              <div class="space-y-2">
                <label for="name" class="block text-sm font-semibold text-gray-900">
                  Role Name
                </label>
                <InputText
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="e.g., Administrator, Editor, Viewer"
                  class="w-full"
                  :class="{ 'border-red-500': form.errors.name }"
                  required
                  autofocus
                />
                <InputError :message="form.errors.name" />
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="p-6">
              <div class="space-y-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-sm font-semibold text-gray-900">Permissions</h3>
                    <p class="text-xs text-gray-500 mt-1">
                      Select the permissions for this role ({{ form.permissions.length }} selected)
                    </p>
                  </div>
                </div>

                <InputError :message="form.errors.permissions" />

                <!-- Permission Groups -->
                <div class="space-y-4">
                  <div
                    v-for="(groupPermissions, groupName) in groupedPermissions"
                    :key="groupName"
                    class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                  >
                    <!-- Group Header with Select All -->
                    <div class="flex items-center justify-between mb-3">
                      <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <i class="pi pi-shield text-indigo-600"></i>
                        {{ formatGroupName(groupName) }}
                        <span class="text-xs font-normal text-gray-500">
                          ({{ groupPermissions.length }})
                        </span>
                      </h4>
                      <button
                        type="button"
                        @click="toggleGroup(groupPermissions)"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
                      >
                        {{ isAllSelected(groupPermissions) ? 'Deselect All' : 'Select All' }}
                      </button>
                    </div>

                    <!-- Permission Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                      <div
                        v-for="permission in groupPermissions"
                        :key="permission.name"
                        class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all cursor-pointer"
                        @click="() => {
                          const index = form.permissions.indexOf(permission.name)
                          if (index > -1) {
                            form.permissions.splice(index, 1)
                          } else {
                            form.permissions.push(permission.name)
                          }
                        }"
                      >
                        <Checkbox
                          v-model="form.permissions"
                          :value="permission.name"
                          :inputId="'perm-' + permission.name"
                        />
                        <label
                          :for="'perm-' + permission.name"
                          class="text-sm text-gray-700 cursor-pointer flex-1"
                        >
                          {{ permission.name.split('.')[1] || permission.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
              <Button
                label="Cancel"
                severity="secondary"
                outlined
                @click="$inertia.visit(route('roles.index'))"
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
              <h4 class="text-sm font-semibold text-blue-900 mb-1">About Permissions</h4>
              <p class="text-sm text-blue-800">
                Permissions determine what actions users with this role can perform.
                Be careful when assigning permissions to ensure appropriate access control.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom checkbox styling for better UX */
:deep(.p-checkbox) {
  flex-shrink: 0;
}

/* Smooth transitions */
.permission-item {
  transition: all 0.2s ease;
}
</style>
