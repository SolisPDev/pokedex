<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Left Column: Image Vision Scanner -->
    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-6 shadow-lg backdrop-blur-md flex flex-col h-full">
      <div class="mb-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          Escáner de Visión LMM
        </h2>
        <p class="text-zinc-400 text-sm mt-1">Sube una foto de un Pokémon o carta para identificarlo automáticamente.</p>
      </div>

      <!-- Upload Zone -->
      <div 
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop"
        :class="dragOver ? 'border-indigo-500 bg-indigo-500/5' : 'border-zinc-800 hover:border-zinc-700 bg-zinc-950/40'"
        class="border-2 border-dashed rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer transition-all min-h-60 relative group"
        @click="triggerFileInput"
      >
        <input 
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileSelect"
        />

        <!-- Preview Image -->
        <div v-if="imagePreview" class="relative max-h-52 w-full flex items-center justify-center overflow-hidden rounded-xl">
          <img :src="imagePreview" class="max-h-48 object-contain rounded-xl shadow-md" />
          <button 
            @click.stop="clearImage" 
            class="absolute top-2 right-2 p-1.5 bg-black/75 hover:bg-black text-zinc-400 hover:text-white rounded-full cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Placeholder -->
        <div v-else class="text-center space-y-3">
          <div class="inline-flex items-center justify-center p-3 bg-zinc-900 border border-zinc-800 rounded-2xl text-zinc-400 group-hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-zinc-300">Arrastra una imagen aquí o haz clic para subir</p>
            <p class="text-xs text-zinc-500 mt-1">Soporta PNG, JPG o JPEG</p>
          </div>
        </div>
      </div>

      <!-- Scan Actions -->
      <div class="mt-6 flex-grow flex flex-col justify-end">
        <button 
          v-if="imagePreview"
          @click="scanImage" 
          :disabled="scanning"
          class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 disabled:opacity-40 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-600/10 cursor-pointer flex items-center justify-center gap-2"
        >
          <svg v-if="scanning" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ scanning ? 'Gemini Analizando Imagen...' : 'Identificar Pokémon con Gemini' }}</span>
        </button>
      </div>

      <!-- Scan Result -->
      <div v-if="scanResult" class="mt-6 bg-zinc-950 border border-zinc-800 rounded-2xl p-5 space-y-4 animate-fade-in relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-indigo-600/5 blur-xl"></div>
        <div class="flex justify-between items-start">
          <div>
            <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Identificado</div>
            <h4 class="text-xl font-bold text-white capitalize mt-1">{{ scanResult.name }}</h4>
          </div>
          <div class="bg-indigo-950/50 border border-indigo-500/20 px-3 py-1 rounded-xl text-center">
            <div class="text-[9px] text-indigo-300 font-bold uppercase tracking-wider">Confianza</div>
            <div class="text-white font-extrabold text-sm">{{ (scanResult.confidence * 100).toFixed(0) }}%</div>
          </div>
        </div>

        <div class="flex flex-wrap gap-1.5">
          <span 
            v-for="type in splitTypes(scanResult.type)" 
            :key="type"
            :class="`bg-type-${type}`"
            class="text-[10px] uppercase font-bold text-white px-2 py-0.5 rounded-md"
          >
            {{ type }}
          </span>
        </div>

        <div class="p-3.5 bg-zinc-900/60 border border-zinc-850 rounded-xl">
          <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Consejo de IA</div>
          <p class="text-zinc-300 text-sm italic">{{ scanResult.suggestion }}</p>
        </div>

        <!-- Quick Add Button if identified -->
        <button 
          v-if="scanResult.name !== 'Desconocido' && scanResult.name !== 'Error' && !added"
          @click="addScannedToCollection"
          :disabled="adding"
          class="w-full bg-zinc-900 hover:bg-red-600 border border-zinc-800 hover:border-red-500/50 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-1.5"
        >
          <svg v-if="adding" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ adding ? 'Agregando...' : 'Añadir Pokémon identificado a favoritos' }}</span>
        </button>

        <div v-else-if="added" class="text-center text-xs font-bold text-green-400 p-2 bg-green-950/30 border border-green-500/20 rounded-xl">
          ¡Añadido a favoritos con éxito!
        </div>
      </div>
    </div>

    <!-- Right Column: Collection Chat Advisor -->
    <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-3xl p-6 shadow-lg backdrop-blur-md flex flex-col h-full min-h-[500px]">
      <div class="mb-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
          </svg>
          Consejero de Colección IA
        </h2>
        <p class="text-zinc-400 text-sm mt-1">El Profesor Pokémon analiza tu equipo y te da consejos de sinergia.</p>
      </div>

      <!-- Advisor Box / Chat bubble -->
      <div class="flex-grow bg-zinc-950/40 border border-zinc-850 rounded-2xl p-6 overflow-y-auto min-h-60 relative flex flex-col justify-center items-center">
        <!-- Glow effects -->
        <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-purple-600/5 blur-xl"></div>
        
        <!-- Loading -->
        <div v-if="loadingInsights" class="flex flex-col items-center justify-center space-y-4">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-500"></div>
          <p class="text-zinc-500 text-sm">Consultando al Profesor Pokémon...</p>
        </div>

        <!-- Loaded Content -->
        <div v-else-if="insights" class="w-full text-zinc-300 text-sm whitespace-pre-line leading-relaxed">
          <div class="flex items-center gap-3 border-b border-zinc-850 pb-4 mb-4">
            <div class="w-10 h-10 rounded-full bg-purple-600/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
              🎓
            </div>
            <div>
              <div class="text-sm font-bold text-white">Profesor Pokémon</div>
              <div class="text-[10px] text-zinc-500 font-semibold uppercase">Consejos de combate</div>
            </div>
          </div>
          {{ insights }}
        </div>

        <!-- Empty State -->
        <div v-else class="text-center space-y-4 max-w-sm">
          <div class="w-12 h-12 mx-auto rounded-full bg-zinc-900 border border-zinc-850 flex items-center justify-center text-2xl animate-float">
            🎓
          </div>
          <div>
            <h4 class="text-white font-semibold text-sm">¿Necesitas consejos estratégicos?</h4>
            <p class="text-zinc-500 text-xs mt-1">Haz clic abajo para que el Profesor Pokémon analice tu equipo actual y te dé insights estratégicos.</p>
          </div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="mt-6">
        <button 
          @click="fetchInsights" 
          :disabled="loadingInsights"
          class="w-full bg-zinc-950 hover:bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-zinc-200 hover:text-white font-bold py-3.5 px-4 rounded-xl cursor-pointer transition flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5M7 16l-4-4 4-4m12 8l4-4-4-4"/>
          </svg>
          <span>{{ insights ? 'Recargar Consejos Estratégicos' : 'Analizar mi Colección con IA' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../services/api';

const fileInput = ref(null);
const imageFile = ref(null);
const imagePreview = ref(null);
const scanning = ref(false);
const scanResult = ref(null);
const dragOver = ref(false);

const added = ref(false);
const adding = ref(false);

// Insights states
const insights = ref('');
const loadingInsights = ref(false);

const triggerFileInput = () => {
  if (!imagePreview.value) {
    fileInput.value.click();
  }
};

const handleFileSelect = (e) => {
  const files = e.target.files;
  if (files.length > 0) {
    processFile(files[0]);
  }
};

const handleDrop = (e) => {
  dragOver.value = false;
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    processFile(files[0]);
  }
};

const processFile = (file) => {
  if (!file.type.startsWith('image/')) {
    alert('Por favor, selecciona un archivo de imagen válido.');
    return;
  }
  imageFile.value = file;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
    scanResult.value = null;
    added.value = false;
  };
  reader.readAsDataURL(file);
};

const clearImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  scanResult.value = null;
  added.value = false;
};

const scanImage = async () => {
  if (!imageFile.value) return;
  scanning.value = true;
  scanResult.value = null;
  
  try {
    const formData = new FormData();
    formData.append('image', imageFile.value);
    
    const response = await api.post('/ia/identify-pokemon', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    scanResult.value = response.data;
  } catch (err) {
    console.error('Error scanning image', err);
    scanResult.value = {
      name: 'Error',
      confidence: 0.0,
      type: 'error',
      suggestion: 'Hubo un error al comunicarse con la API de identificación de imagen.'
    };
  } finally {
    scanning.value = false;
  }
};

const splitTypes = (typeString) => {
  if (!typeString) return [];
  return typeString.split(',').map(t => t.trim());
};

const addScannedToCollection = async () => {
  if (!scanResult.value) return;
  adding.value = true;
  try {
    // We need to fetch details or map types from what scan returned
    // First, let's fetch detail from Pokedex API proxy to get true PokéAPI ID
    const name = scanResult.value.name;
    const detailResponse = await api.get(`/pokemon/${name}`);
    const pokemon = detailResponse.data;
    
    await api.post('/collection', {
      pokemon_id: pokemon.id,
      pokemon_name: pokemon.name,
      pokemon_type: pokemon.types.join(', '),
      custom_notes: `Identificado mediante escaneo de IA. Sugerencia de Gemini: ${scanResult.value.suggestion}`,
    });
    added.value = true;
  } catch (err) {
    console.error('Error adding scanned pokemon', err);
    alert('No se pudo añadir el Pokémon. Tal vez no existe en PokéAPI o ya está registrado.');
  } finally {
    adding.value = false;
  }
};

const fetchInsights = async () => {
  loadingInsights.value = true;
  insights.value = '';
  try {
    const response = await api.post('/ia/chat-insights');
    insights.value = response.data.insights;
  } catch (err) {
    console.error('Error fetching insights', err);
    insights.value = 'El Profesor Pokémon no pudo analizar tu colección en este momento. Inténtalo más tarde.';
  } finally {
    loadingInsights.value = false;
  }
};
</script>
