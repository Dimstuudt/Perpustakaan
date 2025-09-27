<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, UserRoundCog, ShieldCheck, Book, BookCopyIcon, Library } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    { title: 'Dashboard|SAU', href: '/dashboard', icon: LayoutGrid },
    { title: 'Users|SA', href: '/users', icon: Users },
    { title: 'Roles|S', href: '/roles', icon: UserRoundCog },
    { title: 'Permissions|S', href: '/permissions', icon: ShieldCheck },
    { title: 'Books|SA', href: '/books', icon: BookOpen },
    { title: 'Racks|SA', href: '/racks', icon: Library},
    { title: 'Categories|SA', href: '/categories', icon: Folder },
    { title: 'Loans Admin|SA', href: '/loans', icon: Book},
    { title: 'Loans User|U', href: '/user/loansuser', icon: BookCopyIcon }

    // bisa ditambah item lain untuk testing scroll
];

const footerNavItems: NavItem[] = [
    { title: 'Github Repo', href: 'https://github.com/laravel/vue-starter-kit', icon: Folder },
    { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits#vue', icon: BookOpen },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset" class="h-screen flex flex-col">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <!-- Sidebar content scrollable -->
    <SidebarContent class="flex-1 overflow-y-auto">
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>

<style scoped>
/* Optional: jika ingin scroll bar lebih halus */
.flex-1::-webkit-scrollbar {
  width: 6px;
}
.flex-1::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 3px;
}
</style>
