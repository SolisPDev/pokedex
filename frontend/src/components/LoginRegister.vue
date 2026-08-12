<template>
  <div class="min-h-screen flex items-center justify-center bg-radial from-slate-900 via-zinc-950 to-black p-4 relative overflow-hidden">
    <!-- Background elements -->
    <div class="absolute w-[500px] h-[500px] rounded-full bg-red-600/10 blur-3xl -top-40 -left-40"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-3xl -bottom-40 -right-40"></div>

    <div class="w-full max-w-md bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 shadow-2xl relative z-10">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center p-3 bg-red-600/10 border border-red-500/20 rounded-2xl mb-4 animate-float">
          <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
          </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">PokéDex Manager</h1>
        <p class="text-zinc-400 mt-2 text-sm">Gestiona tu colección Pokémon potenciada por IA</p>
      </div>

      <!-- Mode Toggle -->
      <div class="flex bg-zinc-950 p-1.5 rounded-xl mb-6 border border-zinc-800">
        <button 
          @click="isLogin = true"
          :class="isLogin ? 'bg-red-600 text-white shadow-lg' : 'text-zinc-400 hover:text-white'"
          class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all duration-300"
        >
          Iniciar Sesión
        </button>
        <button 
          @click="isLogin = false"
          :class="!isLogin ? 'bg-red-600 text-white shadow-lg' : 'text-zinc-400 hover:text-white'"
          class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all duration-300"
        >
          Registrarse
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div v-if="!isLogin" class="space-y-1.5">
          <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Nombre Completo</label>
          <input 
            v-model="form.name" 
            type="text" 
            placeholder="Red Ketchum"
            required
            class="w-full bg-zinc-950/80 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-zinc-600 outline-none text-sm"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            placeholder="entrenador@pokedex.com"
            required
            class="w-full bg-zinc-950/80 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-zinc-600 outline-none text-sm"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold text-zinc-300 uppercase tracking-wider">Contraseña</label>
          <input 
            v-model="form.password" 
            type="password" 
            placeholder="••••••••"
            required
            class="w-full bg-zinc-950/80 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-zinc-600 outline-none text-sm"
          />
        </div>

        <!-- Errors -->
        <div v-if="error" class="p-3 bg-red-950/30 border border-red-500/20 text-red-400 rounded-xl text-xs flex items-start gap-2">
          <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div>{{ error }}</div>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-red-600 hover:bg-red-500 disabled:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-red-600/20 hover:shadow-red-600/30 transition-all duration-300 flex items-center justify-center gap-2 mt-6 cursor-pointer"
        >
          <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? 'Procesando...' : (isLogin ? 'Ingresar' : 'Registrarse') }}</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '../store/auth';

const authStore = useAuthStore();
const isLogin = ref(true);
const loading = ref(false);
const error = ref('');

const form = reactive({
  name: '',
  email: '',
  password: '',
});

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    if (isLogin.value) {
      await authStore.login(form.email, form.password);
    } else {
      await authStore.register(form.name, form.email, form.password);
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      // Laravel Validation errors
      const errors = err.response.data.errors;
      error.value = Object.values(errors).flat().join(' ');
    } else if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Ocurrió un error. Por favor inténtalo de nuevo.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
