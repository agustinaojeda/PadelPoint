<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Reserva, SlotTurno } from '@/types';
import { Eye, Plus } from 'lucide-vue-next';

defineProps<{
    turnoSlot: SlotTurno; 
}>();

defineEmits<{
    (e: 'reservar', slot: SlotTurno): void;
    (e: 'verDetalle', reserva?: Reserva): void;
}>();
</script>

<template>
    <div
        class="flex items-center justify-between rounded-lg border p-2.5 text-sm transition"
        :class="[
            turnoSlot.disponible
                ? 'border-border bg-card hover:border-primary/50'
                : turnoSlot.reserva?.estado === 'confirmada'
                  ? 'border-emerald-500/30 bg-emerald-500/10 dark:bg-emerald-950/20'
                  : 'border-orange-500/30 bg-orange-500/10 dark:bg-orange-950/20',
        ]"
    >
        <div class="flex flex-col">
            <span class="font-medium text-foreground">
                {{ turnoSlot.hora_inicio }} - {{ turnoSlot.hora_fin }} hs
            </span>

            <span
                v-if="!turnoSlot.disponible && turnoSlot.reserva"
                class="text-xs font-medium"
                :class="
                    turnoSlot.reserva.estado === 'confirmada'
                        ? 'text-emerald-600 dark:text-emerald-400'
                        : 'text-orange-600 dark:text-orange-400'
                "
            >
                {{ turnoSlot.reserva.nombre_cliente }}
            </span>
        </div>

        <Button
            v-if="turnoSlot.disponible"
            size="sm"
            variant="ghost"
            class="h-8 gap-1 text-xs text-muted-foreground hover:text-foreground"
            @click="$emit('reservar', turnoSlot)"
        >
            <Plus class="h-3.5 w-3.5" /> Reservar
        </Button>

        <div v-else class="flex items-center gap-2">
            <div
                class="flex items-center gap-1 text-xs font-medium capitalize"
                :class="
                    turnoSlot.reserva?.estado === 'confirmada'
                        ? 'text-emerald-700 dark:text-emerald-300'
                        : 'text-orange-700 dark:text-orange-300'
                "
            >
                <span>{{ turnoSlot.reserva?.estado ?? 'reservado' }}</span>
            </div>

            <Badge
                variant="secondary"
                class="flex cursor-pointer items-center gap-1 border-none text-xs capitalize transition-colors"
                :class="
                    turnoSlot.reserva?.estado === 'confirmada'
                        ? 'bg-emerald-500/20 text-emerald-700 hover:bg-emerald-500/30 dark:text-emerald-300'
                        : 'bg-orange-500/20 text-orange-700 hover:bg-orange-500/30 dark:text-orange-300'
                "
                @click="$emit('verDetalle', turnoSlot.reserva)"
            >
                <Eye class="h-3 w-3" />
            </Badge>
        </div>
    </div>
</template>