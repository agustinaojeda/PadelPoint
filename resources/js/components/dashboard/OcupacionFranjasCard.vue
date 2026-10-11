<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FranjaHoraria } from '@/types';
import { Sun } from 'lucide-vue-next';

defineProps<{
    franjas: FranjaHoraria[];
}>();
</script>

<template>
    <Card>
        <CardHeader class="border-b pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <Sun class="h-4 w-4 text-amber-500" />
                Ocupación por Franja Horaria (Este Mes)
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4 pt-4">
            <div v-if="franjas.length === 0" class="p-4 text-center text-xs text-muted-foreground">
                Sin registros de reservas para este mes.
            </div>
            <div v-else v-for="franja in franjas" :key="franja.label" class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-foreground">{{ franja.label }}</span>
                    <span class="font-semibold text-muted-foreground">
                        {{ franja.total }} turnos ({{ franja.porcentaje }}%)
                    </span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full border border-border/20 bg-muted">
                    <div
                        class="h-full rounded-full transition-all duration-500"
                        :style="{
                            width: `${franja.porcentaje}%`,
                            backgroundColor: franja.color,
                        }"
                    ></div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>