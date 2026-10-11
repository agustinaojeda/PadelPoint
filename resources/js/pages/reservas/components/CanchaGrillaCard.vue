<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Cancha, Reserva, SlotTurno } from '@/types';
import SlotItem from './SlotItem.vue';

defineProps<{
    cancha: Cancha;
    slots: SlotTurno[];
}>();

defineEmits<{
    (e: 'reservarSlot', slot: SlotTurno): void;
    (e: 'verDetalleReserva', reserva?: Reserva): void;
}>();
</script>

<template>
    <Card class="flex min-w-[280px] max-w-full flex-1 flex-col">
        <CardHeader class="border-b pb-3">
            <div class="flex items-center justify-between">
                <CardTitle class="text-lg font-semibold">{{ cancha.nombre }}</CardTitle>
                <Badge variant="outline">{{ cancha.es_techada ? 'Techada' : 'Descubierta' }}</Badge>
            </div>
            <p class="text-xs text-muted-foreground">
                Turnos de {{ cancha.duracion_turno }} min | ${{ cancha.precio.toLocaleString('es-AR') }}
            </p>
        </CardHeader>

        <CardContent class="flex-1 space-y-2 pt-4">
            <SlotItem
                v-for="slot in slots"
                :key="slot.hora_inicio"
                :slot="slot"
                @reservar="$emit('reservarSlot', $event)"
                @ver-detalle="$emit('verDetalleReserva', $event)"
            />
        </CardContent>
    </Card>
</template>