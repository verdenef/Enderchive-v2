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
          <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
            <div class="text-center mb-2">
              <div class="flex items-center justify-center gap-2 mb-1">
                <Icon name="lucide:gamepad-2" class="w-5 h-5 text-[#22c55e]" style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))" />
                <h1 class="text-brand text-[#22c55e]" style="font-size: 14px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); letter-spacing: 0.05em">
                  Enderchive
                </h1>
              </div>
            </div>

            <h2 class="text-center mb-2 text-brand text-white" style="font-size: 18px; letter-spacing: 0.05em">
              Return to the Archive
            </h2>

            <div v-if="error" class="px-4 py-3 rounded-lg border border-red-500/30 bg-red-500/10" style="font-size: 14px; color: #ef4444">
              {{ error }}
            </div>

            <GameInput
              v-model="form.email"
              type="text"
              placeholder="Email or username"
              class="h-12"
            />

            <GameInput
              v-model="form.password"
              type="password"
              placeholder="Password"
              class="h-12"
            />

            <GameButton variant="purple-solid" :full-width="true" type="submit">
              <template #icon>
                <Icon name="lucide:log-in" class="w-5 h-5" />
              </template>
              Login
            </GameButton>

            <GameButton variant="purple-outline" :full-width="true" type="button" @click="router.visit('/signup')">
              <template #icon>
                <Icon name="lucide:user-plus" class="w-5 h-5" />
              </template>
              Create account
            </GameButton>

            <div class="relative my-2">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t" style="border-color: #6b7280"></div>
              </div>
              <div class="relative flex justify-center">
                <span class="px-4" style="font-size: 12px; color: #6b7280; background: rgba(27, 22, 38, 0.6)">
                  or
                </span>
              </div>
            </div>

            <GameButton variant="green-accent" :full-width="true" type="button" @click="handleGuestLogin">
              <template #icon>
                <Icon name="lucide:user" class="w-5 h-5" />
              </template>
              Continue as Guest
            </GameButton>

            <p class="text-center text-xs text-white/30 mt-4">
              Admin? Login with email "admin@example.com" and password "password"
            </p>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import GlowingOrbs from '../Components/effects/GlowingOrbs.vue'
import GameButton from '../Components/buttons/GameButton.vue'
import GameInput from '../Components/forms/GameInput.vue'
import Icon from '../Components/ui/Icon.vue'

const form = useForm({
  email: '',
  password: '',
})

const error = ref('')

const handleLogin = () => {
  error.value = ''
  form.post('/login', {
    onSuccess: () => {
      // Redirect handled by Inertia
    },
    onError: (errors) => {
      if (errors.email) {
        error.value = errors.email
      } else if (errors.password) {
        error.value = errors.password
      } else {
        error.value = 'Login failed. Please check your credentials.'
      }
    },
  })
}

const handleGuestLogin = () => {
  router.visit('/home')
}
</script>

