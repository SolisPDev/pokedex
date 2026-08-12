<template>
  <div class="space-y-6">
    <!-- Header/Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-zinc-900/40 p-6 rounded-2xl border border-zinc-800/80 backdrop-blur-md">
      <div>
        <h2 class="text-2xl font-bold text-white">Buscador PokéDex</h2>
        <p class="text-zinc-400 text-sm mt-1">Busca y explora todos los Pokémon de la PokéAPI oficial</p>
      </div>

      <!-- Search Input -->
      <div class="relative w-full md:w-80">
        <input 
          v-model="search"
          @input="handleSearch"
          type="text"
          placeholder="Buscar Pokémon por nombre..."
          class="w-full bg-zinc-950/80 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-zinc-500 outline-none text-sm transition-all"
        />
        <svg class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <!-- Error state -->
    <div v-if="error" class="p-4 bg-red-950/30 border border-red-500/20 text-red-400 rounded-2xl text-sm text-center">
      {{ error }}
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500"></div>
      <p class="text-zinc-400 text-sm">Cargando Pokémon desde la PokéAPI...</p>
    </div>

    <!-- Grid Results -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div 
        v-for="pokemon in pokemonList" 
        :key="pokemon.id"
        class="group bg-zinc-900/50 hover:bg-zinc-900/80 border border-zinc-800/80 hover:border-red-500/30 rounded-3xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col relative overflow-hidden"
      >
        <!-- Background glows -->
        <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-white/5 group-hover:scale-150 transition-all duration-500 blur-xl"></div>
        
        <!-- Sprite Image -->
        <div class="h-40 flex items-center justify-center bg-zinc-950/50 rounded-2xl mb-4 overflow-hidden relative border border-zinc-950 group-hover:border-zinc-800/50 transition-all">
          <img 
            :src="pokemon.sprite" 
            :alt="pokemon.name"
            class="h-28 w-28 object-contain z-10 group-hover:scale-115 group-hover:rotate-3 transition-all duration-300"
            @error="handleImageError"
          />
          <div class="absolute bottom-2 right-3 text-xs font-semibold text-zinc-600">
            #{{ String(pokemon.id).padStart(3, '0') }}
          </div>
        </div>

        <!-- Details -->
        <div class="space-y-2 flex-grow">
          <h3 class="text-lg font-bold text-white capitalize group-hover:text-red-500 transition-colors">
            {{ pokemon.name }}
          </h3>
          
          <!-- Badges -->
          <div class="flex flex-wrap gap-1.5">
            <span 
              v-for="type in pokemon.types" 
              :key="type"
              :class="`bg-type-${type}`"
              class="text-[10px] uppercase font-bold text-white px-2 py-0.5 rounded-md"
            >
              {{ type }}
            </span>
          </div>

          <!-- Extra stats preview -->
          <div class="grid grid-cols-2 gap-2 pt-2 text-xs text-zinc-500 border-t border-zinc-800/60 mt-3">
            <div>Altura: <span class="text-zinc-300">{{ pokemon.height / 10 }}m</span></div>
            <div>Peso: <span class="text-zinc-300">{{ pokemon.weight / 10 }}kg</span></div>
          </div>
        </div>

        <!-- Add Button -->
        <button 
          @click="openAddModal(pokemon)"
          class="w-full mt-4 bg-zinc-950 hover:bg-red-600 text-zinc-300 hover:text-white border border-zinc-800 hover:border-red-500/50 py-2.5 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm group-hover:shadow-lg shadow-black/10 group-hover:shadow-red-600/10"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Añadir a mi Colección
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && pokemonList.length === 0" class="text-center py-20 bg-zinc-900/20 rounded-3xl border border-zinc-800">
      <svg class="w-12 h-12 mx-auto text-zinc-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h4 class="text-lg font-semibold text-zinc-400">No se encontraron Pokémon</h4>
      <p class="text-zinc-500 text-sm mt-1">Prueba con otro término de búsqueda.</p>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && totalPages > 1" class="flex justify-center items-center gap-2 pt-6">
      <button 
        @click="changePage(currentPage - 1)" 
        :disabled="currentPage === 1"
        class="p-2 bg-zinc-900 border border-zinc-800 disabled:opacity-40 text-white rounded-lg hover:border-zinc-700 disabled:hover:border-zinc-800 cursor-pointer"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>

      <span class="text-sm text-zinc-400 px-4">
        Página <span class="text-white font-semibold">{{ currentPage }}</span> de <span class="text-white font-semibold">{{ totalPages }}</span>
      </span>

      <button 
        @click="changePage(currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="p-2 bg-zinc-900 border border-zinc-800 disabled:opacity-40 text-white rounded-lg hover:border-zinc-700 disabled:hover:border-zinc-800 cursor-pointer"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>
    </div>

    <!-- Add Pokemon Modal -->
    <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
      <div class="bg-zinc-950 border border-zinc-800 rounded-3xl w-full max-w-md p-6 relative overflow-hidden shadow-2xl">
        <!-- Close Button -->
        <button @click="closeModal" class="absolute top-4 right-4 text-zinc-500 hover:text-white cursor-pointer">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <h3 class="text-xl font-bold text-white mb-4">Añadir Pokémon</h3>

        <div class="flex items-center gap-4 bg-zinc-900/60 p-4 rounded-2xl mb-4 border border-zinc-900">
          <img :src="selectedPokemon.sprite" :alt="selectedPokemon.name" class="w-16 h-16 object-contain" />
          <div>
            <h4 class="text-lg font-bold text-white capitalize">{{ selectedPokemon.name }}</h4>
            <div class="flex gap-1.5 mt-1">
              <span 
                v-for="type in selectedPokemon.types" 
                :key="type"
                :class="`bg-type-${type}`"
                class="text-[9px] uppercase font-bold text-white px-1.5 py-0.5 rounded"
              >
                {{ type }}
              </span>
            </div>
          </div>
        </div>

        <form @submit.prevent="addToCollection" class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Notas Personalizadas</label>
            <textarea 
              v-model="customNotes" 
              placeholder="Escribe alguna nota sobre este Pokémon (ej. Naturaleza, estrategia, IVs...)"
              rows="3"
              class="w-full bg-zinc-900 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-zinc-600 outline-none text-sm resize-none"
            ></textarea>
          </div>

          <div v-if="modalError" class="p-3 bg-red-950/30 border border-red-500/20 text-red-400 rounded-xl text-xs">
            {{ modalError }}
          </div>

          <div class="flex gap-3 mt-6">
            <button 
              type="button" 
              @click="closeModal" 
              class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white py-3 px-4 rounded-xl text-sm font-bold border border-zinc-850 cursor-pointer"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              :disabled="saving"
              class="flex-1 bg-red-600 hover:bg-red-500 disabled:bg-red-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-lg shadow-red-600/10"
            >
              <svg v-if="saving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ saving ? 'Guardando...' : 'Guardar' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const search = ref('');
const pokemonList = ref([]);
const loading = ref(false);
const error = ref('');

const currentPage = ref(1);
const totalPages = ref(1);

// Modal states
const modalOpen = ref(false);
const selectedPokemon = ref(null);
const customNotes = ref('');
const saving = ref(false);
const modalError = ref('');

let debounceTimer = null;

const fetchPokemon = async (page = 1) => {
  loading.value = true;
  error.value = '';
  try {
    const params = { page };
    if (search.value.trim()) {
      params.search = search.value.trim();
    }
    const response = await api.get('/pokemon', { params });
    pokemonList.value = response.data.data;
    currentPage.value = response.data.current_page;
    totalPages.value = response.data.last_page;
  } catch (err) {
    console.error('Error fetching Pokemon', err);
    error.value = 'No se pudieron obtener los Pokémon. Por favor intenta de nuevo.';
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchPokemon(1);
  }, 400);
};

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    fetchPokemon(page);
  }
};

const openAddModal = (pokemon) => {
  selectedPokemon.value = pokemon;
  customNotes.value = '';
  modalError.value = '';
  modalOpen.value = true;
};

const closeModal = () => {
  modalOpen.value = false;
  selectedPokemon.value = null;
  customNotes.value = '';
};

const addToCollection = async () => {
  saving.value = true;
  modalError.value = '';
  try {
    await api.post('/collection', {
      pokemon_id: selectedPokemon.value.id,
      pokemon_name: selectedPokemon.value.name,
      pokemon_type: selectedPokemon.value.types.join(', '),
      custom_notes: customNotes.value,
    });
    closeModal();
    // Notify or handle successful add
  } catch (err) {
    console.error('Error adding to collection', err);
    if (err.response && err.response.data && err.response.data.message) {
      modalError.value = err.response.data.message;
    } else {
      modalError.value = 'Error al guardar el Pokémon en tu colección.';
    }
  } finally {
    saving.value = false;
  }
};

const handleImageError = (e) => {
  e.target.src = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png';
};

onMounted(() => {
  fetchPokemon();
});
</script>
