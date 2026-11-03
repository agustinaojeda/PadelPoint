<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { fechaHoyIso, sumarDias } from '@/lib/fechas';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    fecha: string;
}>();

const emit = defineEmits<{
    (e: 'update:fecha', nuevaFecha: string): void;
    (e: 'cambiar', nuevaFecha: string): void;
}>();

const navegarDia = (dias: number) => emit('cambiar', sumarDias(props.fecha, dias));

const irAHoy = () => emit('cambiar', fechaHoyIso());

const cambiarDesdeInput = (valor: string | number) => emit('cambiar', String(valor));
</script>

<template>
    <div class="flex items-center gap-2">
        <Button variant="outline" size="icon" @click="navegarDia(-1)">
            <ChevronLeft class="h-4 w-4" />
        </Button>
        <Button variant="outline" @click="irAHoy"> Hoy </Button>
        <Input type="date" :model-value="fecha" class="h-9 w-auto" @update:model-value="cambiarDesdeInput" />
        <Button variant="outline" size="icon" @click="navegarDia(1)">
            <ChevronRight class="h-4 w-4" />
        </Button>
    </div>
</template>
