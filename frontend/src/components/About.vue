<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-zinc-800 pb-5">
      <div>
        <h2 class="text-2xl font-black tracking-wide text-zinc-100">Acerca del Desarrollador</h2>
        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Créditos e Información del Proyecto</p>
      </div>
    </div>

    <!-- Main Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
      <!-- Decorative gradient circles -->
      <div class="absolute -right-20 -top-20 w-60 h-60 bg-red-600/10 rounded-full blur-3xl"></div>
      <div class="absolute -left-20 -bottom-20 w-60 h-60 bg-zinc-800/10 rounded-full blur-3xl"></div>

      <div class="relative flex flex-col md:flex-row items-center md:items-start gap-8">
        <!-- Developer Profile Icon / Logo -->
        <div class="shrink-0 w-24 h-24 rounded-2xl bg-gradient-to-tr from-red-600 to-red-500 flex items-center justify-center text-white shadow-lg shadow-red-900/30">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>

        <!-- Developer Details -->
        <div class="flex-grow space-y-6 text-center md:text-left">
          <div>
            <h3 class="text-3xl font-extrabold text-zinc-100">Ing. Antonio Solis Perales</h3>
            <p class="text-sm font-semibold text-red-400 mt-1">Software Engineer & Full Stack Developer</p>
          </div>

          <!-- Contact Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-zinc-800/50">
            <div class="p-4 bg-zinc-950/40 border border-zinc-850 rounded-2xl flex items-center gap-3">
              <div class="p-2.5 bg-zinc-900 rounded-xl text-zinc-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="text-left min-w-0">
                <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Email Principal</div>
                <a href="mailto:solispantonio@gmail.com" class="text-sm text-zinc-300 font-semibold hover:text-white transition break-all">{{ email1 }}</a>
              </div>
            </div>

            <div class="p-4 bg-zinc-950/40 border border-zinc-850 rounded-2xl flex items-center gap-3">
              <div class="p-2.5 bg-zinc-900 rounded-xl text-zinc-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="text-left min-w-0">
                <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Email Corporativo</div>
                <a href="mailto:solispantonio@horizondevs.net" class="text-sm text-zinc-300 font-semibold hover:text-white transition break-all">{{ email2 }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tech Stack Summary -->
    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-3xl p-6">
      <h4 class="text-sm font-bold text-zinc-300 uppercase tracking-widest mb-4">Stack de este Proyecto</h4>
      <div class="flex flex-wrap gap-2">
        <span v-for="tech in techStack" :key="tech" class="px-3.5 py-1.5 bg-zinc-950 border border-zinc-850 text-zinc-400 rounded-xl text-xs font-semibold">
          {{ tech }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const email1 = ref('solispantonio@gmail.com');
const email2 = ref('solispantonio@horizondevs.net');

const techStack = ref([
  'Laravel 11',
  'Vue 3 (Composition API)',
  'Vite',
  'Tailwind CSS v4',
  'Pinia (State Management)',
  'SQLite',
  'OpenAI (Multimodal LMM)',
  'Google Gemini (Fallback)',
  'Laravel Sanctum'
]);
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
