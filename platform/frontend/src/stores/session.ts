import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import type { UserRole } from '@/types/domain'

const STORAGE_KEY = 'armaghan:test12:role'

function readRole(): UserRole {
  const value = sessionStorage.getItem(STORAGE_KEY)
  return value === 'admin' || value === 'customer' ? value : 'guest'
}

export const useSessionStore = defineStore('session', () => {
  const role = ref<UserRole>(readRole())
  const impersonatedCustomerId = ref<number | null>(null)

  const isAuthenticated = computed(() => role.value !== 'guest')
  const isAdmin = computed(() => role.value === 'admin')
  const isCustomer = computed(() => role.value === 'customer')

  function login(username: string, password: string): boolean {
    if (username === '1' && password === '1') role.value = 'admin'
    else if (username === '2' && password === '2') role.value = 'customer'
    else return false

    impersonatedCustomerId.value = null
    sessionStorage.setItem(STORAGE_KEY, role.value)
    return true
  }

  function logout(): void {
    role.value = 'guest'
    impersonatedCustomerId.value = null
    sessionStorage.removeItem(STORAGE_KEY)
    sessionStorage.removeItem('armaghan:test12:impersonation')
  }

  function impersonate(customerId: number): void {
    if (!isAdmin.value) return
    impersonatedCustomerId.value = customerId
    sessionStorage.setItem('armaghan:test12:impersonation', String(customerId))
  }

  function stopImpersonating(): void {
    impersonatedCustomerId.value = null
    sessionStorage.removeItem('armaghan:test12:impersonation')
  }

  return { role, isAuthenticated, isAdmin, isCustomer, impersonatedCustomerId, login, logout, impersonate, stopImpersonating }
})
