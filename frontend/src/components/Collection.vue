<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-zinc-900/40 p-6 rounded-2xl border border-zinc-800/80 backdrop-blur-md">
      <h2 class="text-2xl font-bold text-white">Mi Colección Pokémon</h2>
      <p class="text-zinc-400 text-sm mt-1">Administra tus Pokémon favoritos y edita sus notas estratégicas</p>
    </div>

    <!-- Error state -->
    <div v-if="error" class="p-4 bg-red-950/30 border border-red-500/20 text-red-400 rounded-2xl text-sm text-center">
      {{ error }}
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500"></div>
      <p class="text-zinc-400 text-sm">Cargando colección...</p>
    </div>

    <!-- Collection Grid -->
    <div v-else-if="collection.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="item in collection" 
        :key="item.id"
        class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-5 hover:border-zinc-700/60 shadow-lg flex flex-col relative"
      >
        <div class="flex items-start gap-4">
          <!-- Sprite / Image proxy from official artworks -->
          <div class="w-20 h-20 bg-zinc-950 rounded-2xl flex items-center justify-center border border-zinc-900 shrink-0">
            <img 
              :src="`https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${item.pokemon_id}.png`" 
              :alt="item.pokemon_name"
              class="w-16 h-16 object-contain"
              @error="handleImageError"
            />
          </div>

          <div class="space-y-1 flex-grow">
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-bold text-white capitalize">{{ item.pokemon_name }}</h3>
              <span class="text-xs font-semibold text-zinc-600">#{{ String(item.pokemon_id).padStart(3, '0') }}</span>
            </div>
            
            <!-- Badges -->
            <div class="flex flex-wrap gap-1">
              <span 
                v-for="type in splitTypes(item.pokemon_type)" 
                :key="type"
                :class="`bg-type-${type}`"
                class="text-[9px] uppercase font-bold text-white px-1.5 py-0.5 rounded"
              >
                {{ type }}
              </span>
            </div>
          </div>
        </div>

        <!-- Custom Notes -->
        <div class="mt-4 p-3.5 bg-zinc-950/80 border border-zinc-900 rounded-2xl flex-grow flex flex-col">
          <div class="text-[10px] uppercase font-semibold text-zinc-500 tracking-wider mb-1">Notas del Entrenador</div>
          <div class="text-zinc-300 text-sm whitespace-pre-line italic flex-grow">
            {{ item.custom_notes || 'Sin notas añadidas. Haz clic abajo para escribir.' }}
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 mt-4 pt-4 border-t border-zinc-850">
          <button 
            @click="openEditModal(item)"
            class="flex-1 bg-zinc-950 hover:bg-zinc-800 text-zinc-300 border border-zinc-850 hover:border-zinc-700 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Editar Notas
          </button>
          
          <button 
            @click="deletePokemon(item.id)"
            class="bg-zinc-950 hover:bg-red-950/30 text-zinc-500 hover:text-red-500 border border-zinc-850 hover:border-red-950/40 p-2 rounded-xl transition cursor-pointer flex items-center justify-center"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-20 bg-zinc-900/10 rounded-3xl border border-zinc-850">
      <svg class="w-16 h-16 mx-auto text-zinc-700 mb-4 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
      </svg>
      <h4 class="text-lg font-semibold text-zinc-400">Tu colección está vacía</h4>
      <p class="text-zinc-500 text-sm mt-1 mb-6">Comienza explorando el buscador de PokéDex para registrar tus Pokémon favoritos.</p>
    </div>

    <!-- Edit Notes Modal -->
    <div v-if="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
      <div class="bg-zinc-950 border border-zinc-800 rounded-3xl w-full max-w-md p-6 relative shadow-2xl">
        <!-- Close Button -->
        <button @click="closeEditModal" class="absolute top-4 right-4 text-zinc-500 hover:text-white cursor-pointer">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <h3 class="text-xl font-bold text-white mb-4">Editar Notas</h3>

        <div class="flex items-center gap-4 bg-zinc-900/60 p-4 rounded-2xl mb-4 border border-zinc-900">
          <img 
            :src="`https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${selectedItem.pokemon_id}.png`" 
            :alt="selectedItem.pokemon_name" 
            class="w-16 h-16 object-contain" 
            @error="handleImageError"
          />
          <div>
            <h4 class="text-lg font-bold text-white capitalize">{{ selectedItem.pokemon_name }}</h4>
            <div class="flex gap-1.5 mt-1">
              <span 
                v-for="type in splitTypes(selectedItem.pokemon_type)" 
                :key="type"
                :class="`bg-type-${type}`"
                class="text-[9px] uppercase font-bold text-white px-1.5 py-0.5 rounded"
              >
                {{ type }}
              </span>
            </div>
          </div>
        </div>

        <form @submit.prevent="updateNotes" class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Notas Personalizadas</label>
            <textarea 
              v-model="editNotes" 
              placeholder="Escribe alguna nota sobre este Pokémon (ej. Naturaleza, estrategia, IVs...)"
              rows="4"
              class="w-full bg-zinc-900 border border-zinc-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-xl px-4 py-3 text-white placeholder-zinc-600 outline-none text-sm resize-none"
            ></textarea>
          </div>

          <div v-if="editError" class="p-3 bg-red-950/30 border border-red-500/20 text-red-400 rounded-xl text-xs">
            {{ editError }}
          </div>

          <div class="flex gap-3 mt-6">
            <button 
              type="button" 
              @click="closeEditModal" 
              class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white py-3 px-4 rounded-xl text-sm font-bold border border-zinc-850 cursor-pointer"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              :disabled="saving"
              class="flex-1 bg-red-600 hover:bg-red-500 disabled:bg-red-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer"
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

const collection = ref([]);
const loading = ref(false);
const error = ref('');

// Edit Modal states
const editModalOpen = ref(false);
const selectedItem = ref(null);
const editNotes = ref('');
const saving = ref(false);
const editError = ref('');

const fetchCollection = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/collection');
    collection.value = response.data;
  } catch (err) {
    console.error('Error fetching collection', err);
    error.value = 'No se pudo cargar la colección. Inténtalo más tarde.';
  } finally {
    loading.value = false;
  }
};

const splitTypes = (typeString) => {
  if (!typeString) return [];
  return typeString.split(',').map(t => t.trim());
};

const openEditModal = (item) => {
  selectedItem.value = item;
  editNotes.value = item.custom_notes || '';
  editError.value = '';
  editModalOpen.value = true;
};

const closeEditModal = () => {
  editModalOpen.value = false;
  selectedItem.value = null;
  editNotes.value = '';
};

const updateNotes = async () => {
  saving.value = true;
  editError.value = '';
  try {
    const response = await api.put(`/collection/${selectedItem.value.id}`, {
      custom_notes: editNotes.value,
    });
    // Update local item
    const index = collection.value.findIndex(c => c.id === selectedItem.value.id);
    if (index !== -1) {
      collection.value[index] = response.data.data;
    }
    closeEditModal();
  } catch (err) {
    console.error('Error updating notes', err);
    editError.value = 'No se pudieron guardar las notas.';
  } finally {
    saving.value = false;
  }
};

const deletePokemon = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar este Pokémon de tu colección?')) return;
  try {
    await api.delete(`/collection/${id}`);
    collection.value = collection.value.filter(item => item.id !== id);
  } catch (err) {
    console.error('Error deleting pokemon', err);
    alert('No se pudo eliminar el Pokémon de la colección.');
  }
};

const handleImageError = (e) => {
  e.target.src = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png';
};

onMounted(() => {
  fetchCollection();
});
</script>
