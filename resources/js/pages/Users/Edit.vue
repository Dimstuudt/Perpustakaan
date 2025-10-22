<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import Button from 'primevue/button'
import InputError from '@/components/InputError.vue'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Checkbox from 'primevue/checkbox'
import Divider from 'primevue/divider'
import { reactive, watch, ref, computed } from 'vue'

interface Role {
  id: number
  name: string
}

interface User {
  id: number
  name: string
  username: string
  email: string
  verified_email: boolean
}

const props = defineProps<{
  user: User
  roles: Role[]
  userRoles: string[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: '/users' },
  { title: 'Edit User', href: '#' },
]

const form = useForm({
  name: props.user.name,
  username: props.user.username,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  verified_email: Boolean(props.user.verified_email),
  roles: props.userRoles || [],
})

const submit = () => {
  form.post(route('users.update', props.user.id), {
    onError: (errors) => {
      console.log(errors)
    },
  })
}

// Username validation
const usernameWarning = ref('')
watch(() => form.username, (val) => {
  if (/\s/.test(val)) {
    usernameWarning.value = 'Username cannot contain spaces'
  } else {
    usernameWarning.value = ''
  }
})

// Password validation rules
const rules = reactive({
  minLength: false,
  hasUppercase: false,
  hasLowercase: false,
  hasNumber: false,
  hasSymbol: false,
})

function validatePassword() {
  const val = form.password
  rules.minLength = val.length >= 8
  rules.hasUppercase = /[A-Z]/.test(val)
  rules.hasLowercase = /[a-z]/.test(val)
  rules.hasNumber = /[0-9]/.test(val)
  rules.hasSymbol = /[^A-Za-z0-9]/.test(val)
}

watch(() => form.password, validatePassword)

const passwordStrength = computed(() => {
  if (!form.password) return 0
  const passed = Object.values(rules).filter(Boolean).length
  return (passed / 5) * 100
})

const passwordStrengthLabel = computed(() => {
  if (passwordStrength.value === 0) return ''
  if (passwordStrength.value <= 40) return 'Weak'
  if (passwordStrength.value <= 80) return 'Medium'
  return 'Strong'
})

const isAllRolesSelected = () => {
  return props.roles.every(r => form.roles.includes(r.name))
}

const toggleAllRoles = () => {
  if (isAllRolesSelected()) {
    form.roles = []
  } else {
    form.roles = props.roles.map(r => r.name)
  }
}
</script>

<template>
  <Head title="Edit User" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
              <p class="text-gray-600 mt-1">Update user information and permissions</p>
            </div>
            <Button
              label="Back"
              icon="pi pi-arrow-left"
              outlined
              size="small"
              @click="$inertia.visit(route('users.index'))"
            />
          </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <form @submit.prevent="submit">

            <!-- Basic Information Section -->
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="pi pi-user text-indigo-600"></i>
                Basic Information
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Full Name -->
                <div class="space-y-2">
                  <label for="name" class="block text-sm font-medium text-gray-900">
                    Full Name <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Enter full name"
                    class="w-full"
                    :class="{ 'border-red-500': form.errors.name }"
                    required
                    autofocus
                  />
                  <InputError :message="form.errors.name" />
                </div>

                <!-- Username -->
                <div class="space-y-2">
                  <label for="username" class="block text-sm font-medium text-gray-900">
                    Username <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="username"
                    v-model="form.username"
                    type="text"
                    placeholder="Enter username"
                    class="w-full"
                    :class="{ 'border-red-500': form.errors.username || usernameWarning }"
                    required
                  />
                  <InputError :message="form.errors.username || usernameWarning" />
                </div>

                <!-- Email -->
                <div class="space-y-2 md:col-span-2">
                  <label for="email" class="block text-sm font-medium text-gray-900">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="email@example.com"
                    class="w-full"
                    :class="{ 'border-red-500': form.errors.email }"
                    required
                  />
                  <InputError :message="form.errors.email" />
                </div>

                <!-- Email Verification -->
                <div class="md:col-span-2">
                  <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 border border-gray-200">
                    <Checkbox
                      inputId="verified_email"
                      v-model="form.verified_email"
                      :binary="true"
                    />
                    <label for="verified_email" class="text-sm font-medium text-gray-700 cursor-pointer">
                      Mark email as verified
                    </label>
                    <i class="pi pi-info-circle text-gray-400 text-sm ml-auto"
                       title="User can access features without email verification"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Password Section -->
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="pi pi-lock text-indigo-600"></i>
                Change Password
              </h3>

              <p class="text-sm text-gray-500 mb-4">
                Leave blank if you don't want to change the password
              </p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- New Password -->
                <div class="space-y-2">
                  <label for="password" class="block text-sm font-medium text-gray-900">
                    New Password
                  </label>
                  <Password
                    id="password"
                    v-model="form.password"
                    placeholder="Enter new password"
                    toggleMask
                    inputClass="w-full"
                    class="w-full"
                    :class="{ 'border-red-500': form.errors.password }"
                  >
                    <template #footer>
                      <Divider />
                      <div class="space-y-2 text-sm">
                        <p class="font-semibold">Password Requirements:</p>
                        <ul class="space-y-1">
                          <li :class="rules.minLength ? 'text-green-600' : 'text-gray-600'">
                            <i :class="rules.minLength ? 'pi pi-check' : 'pi pi-times'"></i>
                            Minimum 8 characters
                          </li>
                          <li :class="rules.hasUppercase ? 'text-green-600' : 'text-gray-600'">
                            <i :class="rules.hasUppercase ? 'pi pi-check' : 'pi pi-times'"></i>
                            At least one uppercase letter
                          </li>
                          <li :class="rules.hasLowercase ? 'text-green-600' : 'text-gray-600'">
                            <i :class="rules.hasLowercase ? 'pi pi-check' : 'pi pi-times'"></i>
                            At least one lowercase letter
                          </li>
                          <li :class="rules.hasNumber ? 'text-green-600' : 'text-gray-600'">
                            <i :class="rules.hasNumber ? 'pi pi-check' : 'pi pi-times'"></i>
                            At least one number
                          </li>
                          <li :class="rules.hasSymbol ? 'text-green-600' : 'text-gray-600'">
                            <i :class="rules.hasSymbol ? 'pi pi-check' : 'pi pi-times'"></i>
                            At least one symbol (!@#$%^&*)
                          </li>
                        </ul>
                        <div v-if="form.password" class="mt-3">
                          <div class="flex items-center justify-between text-xs mb-1">
                            <span>Strength:</span>
                            <span :class="{
                              'text-red-600': passwordStrength <= 40,
                              'text-yellow-600': passwordStrength > 40 && passwordStrength <= 80,
                              'text-green-600': passwordStrength > 80
                            }">{{ passwordStrengthLabel }}</span>
                          </div>
                          <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                              class="h-2 rounded-full transition-all"
                              :class="{
                                'bg-red-500': passwordStrength <= 40,
                                'bg-yellow-500': passwordStrength > 40 && passwordStrength <= 80,
                                'bg-green-500': passwordStrength > 80
                              }"
                              :style="{ width: passwordStrength + '%' }"
                            ></div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </Password>
                  <InputError :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-900">
                    Confirm Password
                  </label>
                  <Password
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    placeholder="Confirm new password"
                    toggleMask
                    :feedback="false"
                    inputClass="w-full"
                    class="w-full"
                    :class="{ 'border-red-500': form.errors.password_confirmation }"
                  />
                  <InputError :message="form.errors.password_confirmation" />
                </div>
              </div>
            </div>

            <!-- Roles Section -->
            <div class="p-6">
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                      <i class="pi pi-users text-indigo-600"></i>
                      User Roles
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                      Assign roles to define user permissions ({{ form.roles.length }} selected)
                    </p>
                  </div>
                  <button
                    type="button"
                    @click="toggleAllRoles"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
                  >
                    {{ isAllRolesSelected() ? 'Deselect All' : 'Select All' }}
                  </button>
                </div>

                <InputError :message="form.errors.roles" />

                <!-- Roles Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
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
                    <label
                      :for="'role-' + role.name"
                      class="text-sm font-medium text-gray-700 cursor-pointer flex-1"
                    >
                      {{ role.name }}
                    </label>
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
                @click="$inertia.visit(route('users.index'))"
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
              <h4 class="text-sm font-semibold text-blue-900 mb-1">Important Notes</h4>
              <ul class="text-sm text-blue-800 space-y-1">
                <li>• Username cannot contain spaces or special characters</li>
                <li>• Password is optional - leave blank to keep current password</li>
                <li>• Users need at least one role to access the system</li>
                <li>• Email verification can be managed manually if needed</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
:deep(.p-checkbox) {
  flex-shrink: 0;
}

:deep(.p-password) {
  width: 100%;
}
</style>
