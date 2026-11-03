<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

import CtaReserva from './shared/CtaReserva.vue';

const menuAbierto = ref(false);

const enlaces = [
    { etiqueta: 'Inicio', destino: route('home') },
    { etiqueta: 'Canchas', destino: `${route('home')}#canchas` },
    { etiqueta: 'Cómo funciona', destino: `${route('home')}#como-funciona` },
    { etiqueta: 'Experiencia', destino: `${route('home')}#experiencia` },
];
</script>

<template>
    <header class="fixed top-0 z-50 w-full border-b border-white/10 bg-teal-950/80 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-6 px-6 md:px-12">
            <Link :href="route('home')" class="flex shrink-0 items-center" @click="menuAbierto = false">
                <img src="/images/logo-header.svg" alt="PadelPoint" class="h-7 w-auto md:h-8" />
            </Link>

            <nav class="hidden items-center gap-8 md:flex" aria-label="Navegación principal">
                <a
                    v-for="enlace in enlaces"
                    :key="enlace.etiqueta"
                    :href="enlace.destino"
                    class="text-sm font-medium text-teal-100/80 transition-colors hover:text-lime-300"
                >
                    {{ enlace.etiqueta }}
                </a>
            </nav>

            <div class="hidden items-center gap-5 md:flex">
                <CtaReserva tamano="sm" />
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-teal-100 transition-colors hover:bg-white/10 md:hidden"
                :aria-label="menuAbierto ? 'Cerrar menú' : 'Abrir menú'"
                :aria-expanded="menuAbierto"
                @click="menuAbierto = !menuAbierto"
            >
                <X v-if="menuAbierto" class="h-6 w-6" />
                <Menu v-else class="h-6 w-6" />
            </button>
        </div>

        <div v-if="menuAbierto" class="border-t border-white/10 bg-teal-950/95 px-6 py-4 backdrop-blur-md md:hidden">
            <nav class="flex flex-col gap-1" aria-label="Navegación móvil">
                <a
                    v-for="enlace in enlaces"
                    :key="enlace.etiqueta"
                    :href="enlace.destino"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-teal-100/90 transition-colors hover:bg-white/10 hover:text-lime-300"
                    @click="menuAbierto = false"
                >
                    {{ enlace.etiqueta }}
                </a>
            </nav>

            <CtaReserva etiqueta="Reservar turno" tamano="sm" class="mt-3 w-full" @click="menuAbierto = false" />
        </div>
    </header>
</template>
