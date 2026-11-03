<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { formatDuracion, formatHora, formatPrecio, imagenCancha } from '@/lib/formatos';
import type { CanchaConSlots, SlotPublico } from '@/types';
import { Clock, MapPin, Users } from 'lucide-vue-next';

defineProps<{
    cancha: CanchaConSlots;
    destacada: boolean;
}>();

defineEmits<{
    (e: 'reservar', cancha: CanchaConSlots, slot: SlotPublico): void;
}>();
</script>

<template>
    <Card
        class="flex flex-col overflow-hidden rounded-3xl shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
        :class="destacada ? 'border-lime-400 ring-2 ring-lime-200' : 'border-slate-200/80'"
    >
        <div class="relative h-44 w-full overflow-hidden">
            <img :src="imagenCancha(cancha.imagen_url)" :alt="cancha.nombre" class="h-full w-full object-cover" />

            <Badge class="absolute right-4 top-4 bg-slate-950/80 font-semibold text-white backdrop-blur hover:bg-slate-950/80">
                {{ cancha.es_techada ? 'Techada' : 'Descubierta' }}
            </Badge>
        </div>

        <div class="flex flex-1 flex-col p-6">
            <div class="flex items-start justify-between gap-3">
                <h3 class="text-xl font-bold text-slate-900">{{ cancha.nombre }}</h3>
                <span class="shrink-0 text-lg font-extrabold text-lime-600">{{ formatPrecio(cancha.precio) }}</span>
            </div>

            <p class="mt-2 line-clamp-2 text-sm text-slate-500">
                {{ cancha.descripcion || 'Cancha reglamentaria equipada para competencia y entrenamiento.' }}
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                    <Clock class="h-3.5 w-3.5 text-lime-600" />
                    {{ formatDuracion(cancha.duracion_turno) }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <Users class="h-3.5 w-3.5 text-lime-600" />
                    {{ cancha.cantidad_jugadores }} jugadores
                </span>
                <span v-if="cancha.superficie" class="inline-flex items-center gap-1.5">
                    <MapPin class="h-3.5 w-3.5 text-lime-600" />
                    {{ cancha.superficie }}
                </span>
            </div>

            <div
                v-if="cancha.slots.length === 0"
                class="mt-5 rounded-xl border border-dashed border-slate-200 p-4 text-center text-sm text-slate-500"
            >
                Sin turnos configurados para este día.
            </div>

            <div v-else class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-3">
                <Button
                    v-for="slot in cancha.slots"
                    :key="slot.hora_inicio"
                    type="button"
                    variant="outline"
                    :disabled="!slot.disponible"
                    :aria-label="`Reservar ${cancha.nombre} de ${slot.hora_inicio} a ${slot.hora_fin}`"
                    class="h-auto rounded-xl px-2 py-2.5 text-sm font-semibold transition-all"
                    :class="
                        slot.disponible
                            ? 'border-slate-200 bg-white text-slate-700 hover:-translate-y-0.5 hover:border-lime-400 hover:bg-lime-50 hover:text-slate-900'
                            : 'border-slate-100 bg-slate-100 text-slate-400 line-through'
                    "
                    @click="$emit('reservar', cancha, slot)"
                >
                    {{ formatHora(slot.hora_inicio) }}
                </Button>
            </div>
        </div>
    </Card>
</template>
