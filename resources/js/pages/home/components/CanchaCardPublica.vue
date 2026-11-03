<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatDuracion, formatHora, formatPrecio, formatearDias, imagenCancha } from '@/lib/formatos';
import type { Cancha } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Clock } from 'lucide-vue-next';

defineProps<{
    cancha: Cancha;
}>();
</script>

<template>
    <Card
        class="flex h-full flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
    >
        <div>
            <div class="relative h-52 w-full overflow-hidden p-3">
                <img :src="imagenCancha(cancha.imagen_url)" :alt="cancha.nombre" class="h-full w-full rounded-2xl object-cover" />
                <Badge class="absolute right-6 top-6 bg-slate-950/80 font-medium text-white backdrop-blur-md">
                    {{ cancha.es_techada ? 'Techada' : 'Descubierta' }}
                </Badge>
            </div>

            <CardHeader class="px-6 pb-2 pt-2">
                <div class="flex items-start justify-between gap-2">
                    <CardTitle class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ cancha.nombre }}
                    </CardTitle>
                    <span class="text-lg font-extrabold text-lime-600 dark:text-lime-400">
                        {{ formatPrecio(cancha.precio) }}
                    </span>
                </div>
                <p v-if="cancha.descripcion?.trim()" class="line-clamp-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ cancha.descripcion }}
                </p>
                <p v-else class="text-sm text-slate-400 dark:text-slate-500">Cancha reglamentaria equipada para competencia y entrenamiento.</p>
            </CardHeader>

            <CardContent class="space-y-2.5 px-6 pb-4 text-xs text-slate-600 dark:text-slate-300">
                <div class="flex items-center gap-2">
                    <Clock class="h-3.5 w-3.5 text-lime-600 dark:text-lime-400" />
                    <span><strong>Turnos de </strong>{{ formatDuracion(cancha.duracion_turno) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <Calendar class="h-3.5 w-3.5 text-lime-600 dark:text-lime-400" />
                    <span
                        ><strong>Horario: </strong>{{ formatearDias(cancha.dias_disponibles) }} de {{ formatHora(cancha.hora_apertura) }} a
                        {{ formatHora(cancha.hora_cierre) }} hs</span
                    >
                </div>
            </CardContent>
        </div>

        <div class="px-6 pb-6 pt-2">
            <Link
                :href="route('reservas.index', { cancha_id: cancha.id })"
                class="inline-flex items-center gap-2 text-sm font-bold text-lime-600 transition-colors hover:text-lime-700 dark:text-lime-400 dark:hover:text-lime-300"
            >
                <span>Ver disponibilidad</span>
                <ArrowRight class="h-4 w-4" />
            </Link>
        </div>
    </Card>
</template>
