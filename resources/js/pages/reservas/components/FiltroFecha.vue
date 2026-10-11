<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    fecha: string;
}>();

const emit = defineEmits<{
    (e: 'update:fecha', nuevaFecha: string): void;
    (e: 'cambiar', nuevaFecha: string): void;
}>();

const navegarDia = (dias: number) => {
    const [year, month, day] = props.fecha.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    date.setDate(date.getDate() + dias);

    const isoDate = date.toISOString().split('T')[0];
    emit('cambiar', isoDate);
};

const irAHoy = () => {
    emit('cambiar', new Date().toISOString().split('T')[0]);
};
</script>

<template>
    <div class="flex items-center gap-2">
        <Button variant="outline" size="icon" @click="navegarDia(-1)">
            <ChevronLeft class="h-4 w-4" />
        </Button>
        <Button variant="outline" @click="irAHoy">
            Hoy
        </Button>
        <input
            type="date"
            :value="fecha"
            @change="(e) => $emit('cambiar', (e.target as HTMLInputElement).value)"
            class="h-9 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
        <Button variant="outline" size="icon" @click="navegarDia(1)">
            <ChevronRight class="h-4 w-4" />
        </Button>
    </div>
</template>