<template>
  <AppLayout>
    <div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden">
      <GlowingOrbs />
      <div 
        class="fixed inset-0 z-[1] pointer-events-none opacity-60"
        style="background-image: url('data:image/svg+xml,%3Csvg width=\'200\' height=\'200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noise\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'200\' height=\'200\' filter=\'url(%23noise)\' opacity=\'0.5\'/%3E%3C/svg%3E')"
      />

      <div class="relative z-10 w-full max-w-[480px] mx-4">
        <div 
          class="glass-panel rounded-2xl p-8"
          style="background: rgba(27, 22, 38, 0.6); border: 1px solid rgba(43, 29, 58, 0.7); box-shadow: 0 20px 60px rgba(124, 58, 237, 0.3);"
        >
          <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div class="text-center mb-2">
              <div class="flex items-center justify-center gap-2 mb-1">
                <Icon name="lucide:gamepad-2" class="w-5 h-5 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
                <h1 class="text-brand text-[#22c55e]" style="font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
                  Enderchive
                </h1>
              </div>
            </div>

            <h2 class="text-center mb-2 text-brand text-white" style="font-size: 18px; letter-spacing: 0.05em">
              Enter the End
            </h2>

            <GameInput
              v-model="displayName"
              type="text"
              placeholder="Display name"
              class="h-12"
            />

            <GameInput
              v-model="username"
              type="text"
              placeholder="Username"
              class="h-12"
            />

            <GameInput
              v-model="email"
              type="email"
              placeholder="Email"
              class="h-12"
            />

            <div class="relative">
              <GameInput
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password"
                class="h-12 pr-12"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-white/10 rounded transition-colors"
              >
                <Icon :name="showPassword ? 'lucide:eye-off' : 'lucide:eye'" class="w-4 h-4 text-gray-400" />
              </button>
            </div>

            <div class="relative">
              <GameInput
                v-model="confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Confirm Password"
                class="h-12 pr-12"
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-white/10 rounded transition-colors"
              >
                <Icon :name="showConfirmPassword ? 'lucide:eye-off' : 'lucide:eye'" class="w-4 h-4 text-gray-400" />
              </button>
            </div>

            <div v-if="error" class="px-4 py-3 rounded-lg border border-red-500/30 bg-red-500/10" style="font-size: 14px; color: #ef4444">
              {{ error }}
            </div>

            <GameButton variant="purple-solid" :full-width="true" type="submit">
              <template #icon>
                <Icon name="lucide:user-plus" class="w-5 h-5" />
              </template>
              Create Account
            </GameButton>

            <GameButton variant="purple-outline" :full-width="true" type="button" @click="router.visit('/login')">
              Back to Login
            </GameButton>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import GlowingOrbs from '../Components/effects/GlowingOrbs.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import Icon from '../Components/ui/Icon.vue'
import axios from 'axios'

const displayName = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const error = ref('')

const handleSubmit = async () => {
  error.value = ''

  if (!displayName.value || !username.value || !email.value || !password.value || !confirmPassword.value) {
    error.value = 'All fields are required'
    return
  }

  if (username.value.includes('@') || username.value.toLowerCase() === email.value.toLowerCase()) {
    error.value = 'Username cannot be an email address'
    return
  }

  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match'
    return
  }

  if (password.value.length < 6) {
    error.value = 'Password must be at least 6 characters'
    return
  }

  try {
    const response = await axios.post('/api/register', {
      name: displayName.value,
      username: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    })
    
    if (response.data.status === 'success') {
      // Registration successful, redirect to login
      router.visit('/login')
    } else {
      error.value = response.data.message || 'Registration failed. Please try again.'
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors
      error.value = Object.values(errors).flat().join(', ') || err.response?.data?.message || 'Registration failed'
    } else {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.'
    }
  }
}
</script>

