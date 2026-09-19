import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import type { UserRole } from '@/types/domain'

const STORAGE_KEY = 'armaghan:test15:role'
const IMPERSONATION_KEY = 'armaghan:test15:impersonation'

function readRole(): UserRole {
  const value = sessionStorage.getItem(STORAGE_KEY)
  return value === 'admin' || value === 'customer' ? value : 'guest'
}

function readImpersonation(): number | null {
  const raw = sessionStorage.getItem(IMPERSONATION_KEY)
  if (!raw) return null
  const value = Number(raw)
  return Number.isFinite(value) && value > 0 ? value : null
}

export const useSessionStore = defineStore('session', () => {
  const role = ref<UserRole>(readRole())
  const impersonatedCustomerId = ref<number | null>(readImpersonation())

  const isAuthenticated = computed(() => role.value !== 'guest')
  const isAdmin = computed(() => role.value === 'admin')
  const isCustomer = computed(() => role.value === 'customer')

  function login(username: string, password: string): boolean {
    if (username === '1' && password === '1') role.value = 'admin'
    else if (username === '2' && password === '2') role.value = 'customer'
    else return false

    impersonatedCustomerId.value = null
    sessionStorage.setItem(STORAGE_KEY, role.value)
    sessionStorage.removeItem(IMPERSONATION_KEY)
    return true
  }

  function logout(): void {
    role.value = 'guest'
    impersonatedCustomerId.value = null
    sessionStorage.removeItem(STORAGE_KEY)
    sessionStorage.removeItem(IMPERSONATION_KEY)
  }

  function impersonate(customerId: number): void {
    if (!isAdmin.value) return
    impersonatedCustomerId.value = customerId
    sessionStorage.setItem(IMPERSONATION_KEY, String(customerId))
  }

  function stopImpersonating(): void {
    impersonatedCustomerId.value = null
    sessionStorage.removeItem(IMPERSONATION_KEY)
  }

  return { role, isAuthenticated, isAdmin, isCustomer, impersonatedCustomerId, login, logout, impersonate, stopImpersonating }
})
