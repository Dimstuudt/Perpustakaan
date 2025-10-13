<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import InputText from 'primevue/inputtext';
import { Transition } from 'vue';
import { ref, watch } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import Swal from 'sweetalert2';

const props = defineProps<{
  mustVerifyEmail: boolean;
  status?: string;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Profile settings', href: '/settings/profile' },
];

const page = usePage();
const user = page.props.auth.user as User;

// 🔹 Pisahkan form
const profileForm = useForm({
  name: user.name,
  username: user.username,
  email: user.email,
});

const avatarForm = useForm({
  avatar: null as File | null,
});

// --------------------
// Update Profile Info
// --------------------
const submit = () => {
  profileForm.patch(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Process Success',
        text: 'Profile Berhasil Diperbarui',
        icon: 'success',
      });
    },
    onError: () => {
      Swal.fire({
        title: 'Process Failed',
        text:
          profileForm.errors.email ??
          profileForm.errors.name ??
          profileForm.errors.username,
        icon: 'error',
      });
    },
  });
};

// --------------------
// Update Avatar
// --------------------
const photoPreview = ref<string | null>(null);

const selectNewPhoto = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0] || null;
  avatarForm.avatar = file;
  if (file) {
    photoPreview.value = URL.createObjectURL(file);
  }
};

const updateProfilePhoto = () => {
  avatarForm.post(route('profile.avatar'), {
    preserveScroll: true,
    onSuccess: () => {
      avatarForm.reset('avatar');
      photoPreview.value = null;
    },
  });
};

// --------------------
// Username Warning
// --------------------
const usernameWarning = ref('');
watch(
  () => profileForm.username,
  (val) => {
    if (/\s/.test(val)) {
      usernameWarning.value = 'Username field cannot use spaces';
    } else {
      usernameWarning.value = '';
    }
  }
);

// --------------------
// SweetAlert reminder
// --------------------
const flash = page.props?.flash?.message;
if (flash || !user.username || !user.name) {
  Swal.fire({
    icon: 'info',
    title: 'Complete Your Profile',
    text: 'Please complete your profile info before proceed',
  });
}

const fileInput = ref<HTMLInputElement | null>(null);
const openFileInput = () => {
  fileInput.value?.click();
};

const { getInitials } = useInitials();
</script>

<style>
.profile-user-img {
  cursor: pointer;
}
</style>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Profile Settings" />

    <SettingsLayout>
      <div class="space-y-10">
        <!-- Password Link -->
        <div class="flex justify-end">
          <Link
            href="/settings/password"
            class="text-sm text-primary underline hover:text-primary/80"
          >
            Ganti Password
          </Link>
        </div>

        <!-- Foto Profil -->
        <section
          class="bg-white dark:bg-neutral-900 shadow rounded-2xl p-6 space-y-4"
        >
          <HeadingSmall
            title="Foto Profil"
            description="Unggah atau perbarui foto profil Anda"
          />
<div class="flex flex-col items-center space-y-4">
  <Avatar
    class="w-32 h-32 rounded-full ring-2 ring-offset-2 ring-primary cursor-pointer hover:scale-105 transition-transform"
    @click="openFileInput"
  >
    <AvatarImage
      v-if="user.avatar || photoPreview"
      :src="photoPreview || user.avatar"
      alt="default.png"
    />
    <AvatarFallback
      class="text-2xl font-semibold text-black dark:text-white"
    >
      {{ getInitials(user.username) }}
    </AvatarFallback>
  </Avatar>

  <form
    @submit.prevent="updateProfilePhoto"
    class="flex flex-col items-center gap-3"
  >
    <input
      ref="fileInput"
      id="avatar"
      type="file"
      accept="image/*"
      class="hidden"
      @change="selectNewPhoto"
    />

    <Button
      :disabled="avatarForm.processing || !avatarForm.avatar"
      class="w-fit"
    >
      Unggah Foto
    </Button>

    <InputError :message="avatarForm.errors.avatar" />
    <p
      v-if="props.status === 'profile-photo-updated'"
      class="text-sm font-medium text-green-600"
    >
      Foto profil berhasil diperbarui.
    </p>
  </form>
</div>

        </section>

        <!-- Profile Information -->
        <section
          class="bg-white dark:bg-neutral-900 shadow rounded-2xl p-6 space-y-6"
        >
          <HeadingSmall
            title="Informasi Profil"
            description="Update nama, username, dan email Anda"
          />

          <form @submit.prevent="submit" class="space-y-5">
            <!-- Fullname -->
            <div class="grid gap-2">
              <Label for="name">Fullname</Label>
              <InputText
                id="name"
                class="w-full"
                v-model="profileForm.name"
                required
                placeholder="Masukkan nama lengkap"
              />
              <InputError :message="profileForm.errors.name" />
            </div>

           <!-- Username -->
<div class="grid gap-2">
  <Label for="username">Username</Label>
  <InputText
    id="username"
    class="w-full bg-gray-100 cursor-not-allowed"
    v-model="profileForm.username"
    disabled
  />
  <InputError
    :message="profileForm.errors.username ?? usernameWarning"
  />
</div>


            <!-- Email -->
            <div class="grid gap-2">
              <Label for="email">Email Address</Label>
              <InputText
                id="email"
                type="email"
                class="w-full"
                v-model="profileForm.email"
                required
                placeholder="Masukkan email aktif"
              />
              <InputError :message="profileForm.errors.email" />
            </div>

            <!-- Verification -->
            <div v-if="mustVerifyEmail && !user.email_verified_at">
              <p class="text-sm text-muted-foreground">
                Email Anda belum diverifikasi.
                <Link
                  :href="route('verification.send')"
                  method="post"
                  as="button"
                  class="ml-1 text-primary underline hover:text-primary/80"
                >
                  Klik di sini untuk kirim ulang verifikasi.
                </Link>
              </p>
              <div
                v-if="status === 'verification-link-sent'"
                class="mt-2 text-sm font-medium text-green-600"
              >
                Link verifikasi baru telah dikirim ke email Anda.
              </div>
            </div>

            <!-- Save -->
            <div class="flex items-center gap-4">
              <Button :disabled="profileForm.processing">Simpan</Button>
              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
              >
                <p
                  v-show="profileForm.recentlySuccessful"
                  class="text-sm text-neutral-600"
                >
                  Tersimpan.
                </p>
              </Transition>
            </div>
          </form>
        </section>

        <!-- Delete Account -->
        <DeleteUser />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
