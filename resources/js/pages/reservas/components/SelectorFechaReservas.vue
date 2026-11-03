<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { fechaHoyIso, formatearFechaLarga, sumarDias } from '@/lib/fechas';
import { CalendarDays, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    fecha: string;
}>();

const emit = defineEmits<{
    (e: 'cambiar', fecha: string): void;
}>();

const titulo = computed(() => formatearFechaLarga(props.fecha));

const navegar = (dias: number) => emit('cambiar', sumarDias(props.fecha, dias));

const cambiarDesdeInput = (valor: string | number) => emit('cambiar', String(valor));
</script>

<template>
    <div
        class="mt-8 flex flex-col gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex items-center gap-2">
            <Button
                type="button"
                variant="outline"
                size="icon"
                class="h-10 w-10 rounded-xl border-white/15 bg-transparent text-teal-100 hover:bg-white/10 hover:text-white"
                aria-label="Día anterior"
                @click="navegar(-1)"
            >
                <ChevronLeft class="h-5 w-5" />
            </Button>

            <Button
                type="button"
                variant="outline"
                class="h-10 rounded-xl border-white/15 bg-transparent px-4 text-sm font-semibold text-teal-100 hover:bg-white/10 hover:text-white"
                @click="emit('cambiar', fechaHoyIso())"
            >
                Hoy
            </Button>

            <Button
                type="button"
                variant="outline"
                size="icon"
                class="h-10 w-10 rounded-xl border-white/15 bg-transparent text-teal-100 hover:bg-white/10 hover:text-white"
                aria-label="Día siguiente"
                @click="navegar(1)"
            >
                <ChevronRight class="h-5 w-5" />
            </Button>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <p class="flex items-center gap-2 text-sm font-semibold capitalize text-white">
                <CalendarDays class="h-4 w-4 text-lime-400" />
                {{ titulo }}
            </p>

            <Input
                type="date"
                :model-value="fecha"
                :min="fechaHoyIso()"
                aria-label="Seleccionar fecha"
                class="h-10 w-auto rounded-xl border-white/15 bg-teal-950/60 text-sm text-teal-50 focus-visible:border-lime-400 focus-visible:ring-0"
                @update:model-value="cambiarDesdeInput"
            />
        </div>
    </div>
</template>
