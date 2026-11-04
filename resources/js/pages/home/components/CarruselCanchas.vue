<script setup lang="ts">
import type { Cancha } from '@/types';
import { CalendarX, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import CanchaCardPublica from './CanchaCardPublica.vue';

const props = defineProps<{
    canchas: Cancha[];
}>();

const ITEMS_PER_PAGE = 3;
const currentIndex = ref(0);

const totalPaginas = computed(() => Math.ceil(props.canchas.length / ITEMS_PER_PAGE));

const canchasVisibles = computed(() => {
    const start = currentIndex.value * ITEMS_PER_PAGE;
    return props.canchas.slice(start, start + ITEMS_PER_PAGE);
});

const anterior = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
};

const siguiente = () => {
    if (currentIndex.value < totalPaginas.value - 1) {
        currentIndex.value++;
    }
};
</script>

<template>
    <section id="canchas" class="w-full scroll-mt-20 py-16">
        <div class="container mx-auto max-w-6xl px-6 md:px-12">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-extrabold text-slate-900  md:text-4xl">
                    Canchas <span class="relative inline-block underline decoration-lime-400 decoration-4 underline-offset-8">disponibles</span>
                </h2>
                <p class="mt-4 text-sm text-slate-500  md:text-base">
                    Explorá las canchas y consultá disponibilidad en tiempo real.
                </p>
            </div>

            <!--si no hay todavia-->
            <div
                v-if="!canchas || canchas.length === 0"
                class="mx-auto flex max-w-lg flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center "
            >
                <div class="mb-4 rounded-full bg-lime-100 p-4 text-lime-600 ">
                    <CalendarX class="h-10 w-10" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 ">No hay canchas disponibles</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Actualmente no tenemos canchas habilitadas. Volvé a consultar más tarde.
                </p>
            </div>

            <!--si hay entonces-->
            <div v-else class="relative">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <CanchaCardPublica v-for="cancha in canchasVisibles" :key="cancha.id" :cancha="cancha" />
                </div>

                <div v-if="totalPaginas > 1" class="mt-8 flex items-center justify-center gap-4">
                    <button
                        @click="anterior"
                        :disabled="currentIndex === 0"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 shadow-sm transition-all hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40 "
                        aria-label="Página anterior"
                    >
                        <ChevronLeft class="h-6 w-6" />
                    </button>

                    <span class="text-xs font-semibold text-slate-500 "> {{ currentIndex + 1 }} / {{ totalPaginas }} </span>

                    <button
                        @click="siguiente"
                        :disabled="currentIndex >= totalPaginas - 1"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 shadow-sm transition-all hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40 "
                        aria-label="Página siguiente"
                    >
                        <ChevronRight class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
