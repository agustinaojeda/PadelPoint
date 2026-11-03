<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Clock, MapPin, Phone } from 'lucide-vue-next';

import CtaReserva from './shared/CtaReserva.vue';

const anioActual = new Date().getFullYear();

const enlacesRapidos = [
    { etiqueta: 'Inicio', destino: route('home') },
    { etiqueta: 'Canchas', destino: `${route('home')}#canchas` },
    { etiqueta: 'Cómo funciona', destino: `${route('home')}#como-funciona` },
    { etiqueta: 'Experiencia', destino: `${route('home')}#experiencia` },
];

const contacto = {
    direccion: 'Av. del Padel 1250, Buenos Aires',
    telefono: '+54 11 5555-1234',
    horarios: ['Lunes a viernes de 8:00 a 23:00 hs', 'Sábados y domingos de 8:00 a 22:00 hs'],
};
</script>

<template>
    <footer class="w-full border-t border-white/10 bg-teal-950 text-teal-100/80">
        <div class="mx-auto max-w-6xl px-6 py-14 md:px-12">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <Link :href="route('home')" class="inline-flex items-center">
                        <img src="/images/logo-header.svg" alt="PadelPoint" class="h-8 w-auto" />
                    </Link>
                    <p class="mt-5 max-w-sm text-sm leading-relaxed text-teal-100/70">
                        Club de padel con canchas techadas de cristal panorámico, área de descanso y estacionamiento privado. Reservá tu turno online
                        en segundos.
                    </p>
                    <CtaReserva tamano="sm" class="mt-6" />
                </div>

                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Enlaces rápidos</h2>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li v-for="enlace in enlacesRapidos" :key="enlace.etiqueta">
                            <a :href="enlace.destino" class="transition-colors hover:text-lime-300">
                                {{ enlace.etiqueta }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-white">Contacto</h2>
                    <ul class="mt-5 space-y-4 text-sm">
                        <li class="flex gap-3">
                            <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-lime-400" />
                            <span>{{ contacto.direccion }}</span>
                        </li>
                        <li class="flex gap-3">
                            <Phone class="mt-0.5 h-4 w-4 shrink-0 text-lime-400" />
                            <a :href="`tel:${contacto.telefono.replace(/\s/g, '')}`" class="transition-colors hover:text-lime-300">
                                {{ contacto.telefono }}
                            </a>
                        </li>
                        <li class="flex gap-3">
                            <Clock class="mt-0.5 h-4 w-4 shrink-0 text-lime-400" />
                            <span>
                                <span v-for="horario in contacto.horarios" :key="horario" class="block">{{ horario }}</span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-teal-100/60 md:flex-row">
                <p>© {{ anioActual }} PadelPoint. Todos los derechos reservados.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="transition-colors hover:text-lime-300">Términos y condiciones</a>
                    <a href="#" class="transition-colors hover:text-lime-300">Política de privacidad</a>
                </div>
            </div>
        </div>
    </footer>
</template>
