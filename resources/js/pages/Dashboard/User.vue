<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
];
</script>

<template>
  <Head title="User Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
      <h1 class="text-2xl font-bold">User Dashboard 🙌</h1>

      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
          <span class="absolute bottom-2 left-2 text-sm font-medium">My Books</span>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
