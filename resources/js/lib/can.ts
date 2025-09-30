import { usePage } from '@inertiajs/vue3'

export function can(permission: string): boolean {
  const page = usePage()
  const permissions: string[] = page.props.auth?.user?.permissions ?? []
  return permissions.includes(permission)
}

export function isRole(role: string): boolean {
  const page = usePage()
  const roles: string[] = page.props.auth?.user?.roles ?? []
  return roles.includes(role)
}
