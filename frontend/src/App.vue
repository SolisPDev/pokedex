<template>
  <!-- If not logged in, show Login/Register view -->
  <LoginRegister v-if="!authStore.isAuthenticated" />

  <!-- Otherwise, show Main App Layout -->
  <div v-else class="min-h-screen bg-zinc-950 text-white flex flex-col md:flex-row font-sans">
    <!-- Sidebar navigation -->
    <aside class="w-full md:w-64 bg-zinc-900 border-b md:border-b-0 md:border-r border-zinc-800 flex flex-col justify-between shrink-0">
      <!-- Sidebar Header -->
      <div class="p-6">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-red-600/10 border border-red-500/20 rounded-xl text-red-500">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
          </div>
          <div>
            <h1 class="font-black text-lg tracking-wide bg-gradient-to-r from-white via-zinc-200 to-zinc-400 bg-clip-text text-transparent">PokéDex</h1>
            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Manager</span>
          </div>
        </div>

        <!-- User profile preview -->
        <div class="mt-8 p-3 bg-zinc-950/50 border border-zinc-850 rounded-2xl flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-red-600/10 border border-red-500/20 flex items-center justify-center font-bold text-red-400">
            {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
          </div>
          <div class="min-w-0">
            <div class="text-sm font-bold text-zinc-200 truncate">{{ authStore.user?.name }}</div>
            <div class="text-[10px] text-zinc-500 truncate">{{ authStore.user?.email }}</div>
          </div>
        </div>

        <!-- Navigation items -->
        <nav class="mt-8 space-y-1.5">
          <button 
            v-for="item in navItems" 
            :key="item.view"
            @click="currentView = item.view"
            :class="currentView === item.view ? 'bg-zinc-800 text-white border-zinc-700' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40 border-transparent'"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition border cursor-pointer"
          >
            <component :is="item.icon" class="w-4 h-4" />
            <span>{{ item.label }}</span>
          </button>
        </nav>
      </div>

      <!-- Logout Button -->
      <div class="p-6 border-t border-zinc-800">
        <button 
          @click="handleLogout"
          class="w-full bg-zinc-950 hover:bg-red-950/20 border border-zinc-850 hover:border-red-900/30 text-zinc-400 hover:text-red-400 py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Cerrar Sesión
        </button>
      </div>
    </aside>

    <!-- Main Content Body -->
    <main class="flex-grow p-6 md:p-8 overflow-y-auto max-h-screen">
      <div class="max-w-6xl mx-auto">
        <component :is="activeComponent" />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from './store/auth';

// Icons (Lucide)
import { 
  Search, 
  Sparkles, 
  BookmarkCheck
} from 'lucide-vue-next';

// Components
import LoginRegister from './components/LoginRegister.vue';
import Pokedex from './components/Pokedex.vue';
import Collection from './components/Collection.vue';
import IaScanner from './components/IaScanner.vue';

const authStore = useAuthStore();
const currentView = ref('pokedex');

const navItems = [
  { view: 'pokedex', label: 'Buscador PokéDex', icon: Search },
  { view: 'collection', label: 'Mi Colección', icon: BookmarkCheck },
  { view: 'scanner', label: 'Escáner & Asesor IA', icon: Sparkles },
];

const activeComponent = computed(() => {
  switch (currentView.value) {
    case 'pokedex': return Pokedex;
    case 'collection': return Collection;
    case 'scanner': return IaScanner;
    default: return Pokedex;
  }
});

const handleLogout = async () => {
  await authStore.logout();
};

onMounted(() => {
  authStore.initialize();
});
</script>
