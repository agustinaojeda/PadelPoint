<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { OcupacionDia } from '@/types';
import { BarChart3 } from 'lucide-vue-next';

defineProps<{
    dias: OcupacionDia[];
}>();

const DIAS: Record<number, string> = {
    1: 'Lunes',
    2: 'Martes',
    3: 'Miércoles',
    4: 'Jueves',
    5: 'Viernes',
    6: 'Sábado',
    7: 'Domingo',
};
</script>

<template>
    <Card>
        <CardHeader class="border-b pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <BarChart3 class="h-4 w-4 text-blue-500" />
                Demanda por Día de la Semana
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-3 pt-4">
            <div v-if="dias.length === 0" class="p-4 text-center text-xs text-muted-foreground">Sin registros de días.</div>
            <div v-else v-for="dia in dias" :key="dia.dia_semana" class="flex items-center justify-between text-xs">
                <span class="w-20 font-medium text-foreground">{{ DIAS[dia.dia_semana] }}</span>
                <div class="mx-3 h-2 flex-1 overflow-hidden rounded-full bg-muted">
                    <div class="h-full rounded-full bg-primary transition-all duration-500" :style="{ width: `${dia.porcentaje}%` }"></div>
                </div>
                <span class="w-16 text-right font-medium text-muted-foreground"> {{ dia.total }} t. </span>
            </div>
        </CardContent>
    </Card>
</template>
