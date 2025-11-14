<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import {
  BookOpen,
  Folder,
  LayoutGrid,
  Users,
  UserRoundCog,
  ShieldCheck,
  Book,
  BookCopyIcon,
  Library,
  LibraryBig,
  Layers,
} from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'

// helper cek permission
import { can } from '@/lib/can'
import { computed } from 'vue'

// semua menu diberi permission
const mainNavItems: NavItem[] = [
  { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid, permission: 'dashboard.view' },
  { title: 'Landing', href: '/welcome', icon: Book, permission: 'dashboard.view' },
  { title: 'Users', href: '/users', icon: Users, permission: 'users.view' },
  { title: 'Roles', href: '/roles', icon: UserRoundCog, permission: 'roles.view' },
  { title: 'Permissions', href: '/permissions', icon: ShieldCheck, permission: 'permissions.view' },
  { title: 'Books', href: '/books', icon: BookOpen, permission: 'books.view' },
  { title: 'UnCategoried', href: '/books/need', icon: BookOpen, permission: 'books.view' },
  { title: 'Racks', href: '/racks', icon: Library, permission: 'racks.manage' },
  { title: 'Cabinets', href: '/cabinets', icon: LibraryBig, permission: 'cabinets.manage' },
  { title: 'Categories', href: '/categories', icon: Folder, permission: 'categories.view' },
  { title: 'Loans Admin', href: '/loans', icon: Book, permission: 'loans.view' },
  { title: 'Loans User', href: '/user/loansuser', icon: BookCopyIcon, permission: 'loans.user' },
]

// footer menu bebas (tanpa permission)
const footerNavItems: NavItem[] = [
  { title: 'Landing', href: '/welcome', icon: Layers },
  { title: 'Documentation', href: 'https://github.com/Dimstuudt/Perpustakaan', icon: BookOpen },
]

// filter menu sesuai permission
const filteredNavItems = computed(() => {
  return mainNavItems.filter(item => {
    if (item.permission && !can(item.permission)) return false
    return true
  })
})
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
      <NavMain :items="filteredNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>

<style scoped>
/* Optional: scrollbar lebih halus */
.flex-1::-webkit-scrollbar {
  width: 6px;
}
.flex-1::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}
</style>
