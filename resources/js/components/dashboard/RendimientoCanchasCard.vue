<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CanchaResumen } from '@/types';
import { formatPrecio } from '@/utils/formateo';

defineProps<{
    canchas: CanchaResumen[];
}>();
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader class="border-b pb-3">
            <CardTitle class="text-base font-semibold">Rendimiento por Cancha (Este Mes)</CardTitle>
        </CardHeader>
        <CardContent class="flex-1 pt-4">
            <div v-if="canchas.length === 0" class="p-8 text-center text-sm text-muted-foreground">
                No hay canchas registradas.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="cancha in canchas"
                    :key="cancha.id"
                    class="space-y-2 border-b pb-3 last:border-0 last:pb-0"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-foreground">{{ cancha.nombre }}</span>
                            <Badge variant="outline" class="text-[10px]">
                                {{ cancha.es_techada ? 'Techada' : 'Descubierta' }}
                            </Badge>
                        </div>
                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                            {{ formatPrecio(cancha.recaudado_mes || 0) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>{{ cancha.reservas_mes }} reservas este mes</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>