<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatearFechaLarga } from '@/lib/fechas';
import { formatHora } from '@/lib/formatos';
import type { ReservaConfirmada } from '@/types';
import { CheckCircle2, X } from 'lucide-vue-next';

defineProps<{
    reserva: ReservaConfirmada;
}>();

defineEmits<{
    (e: 'close'): void;
}>();
</script>

<template>
    <div
        id="confirmacion"
        class="mb-8 flex flex-col gap-4 rounded-2xl border border-lime-200 bg-lime-50 p-5 sm:flex-row sm:items-start sm:justify-between"
    >
        <div class="flex items-start gap-3">
            <CheckCircle2 class="mt-0.5 h-6 w-6 shrink-0 text-lime-600" />
            <div>
                <p class="font-bold text-slate-900">¡Reserva solicitada!</p>
                <p class="mt-1 text-sm text-slate-700">
                    {{ reserva.cancha }} ·
                    <span class="capitalize">{{ formatearFechaLarga(reserva.fecha) }}</span>
                    · {{ formatHora(reserva.hora_inicio) }} a {{ formatHora(reserva.hora_fin) }} hs
                </p>
                <p class="mt-1 text-xs text-slate-500">Revisá tu correo: te avisaremos cuando el turno quede confirmado.</p>
            </div>
        </div>

        <Button
            type="button"
            variant="ghost"
            size="sm"
            class="h-auto self-start px-2 py-1 text-xs text-slate-500 hover:bg-lime-100 hover:text-slate-700"
            @click="$emit('close')"
        >
            <X class="h-3.5 w-3.5" />
            Cerrar
        </Button>
    </div>
</template>
