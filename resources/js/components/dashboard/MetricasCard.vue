<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { MetricProps } from '@/types';
import { formatPrecio } from '@/utils/formateo';
import { CalendarCheck, DollarSign, Dumbbell, TrendingUp } from 'lucide-vue-next';

defineProps<{
    metrics: MetricProps;
}>();
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-xs font-medium text-muted-foreground">Ingresos Hoy</CardTitle>
                <DollarSign class="h-4 w-4 text-emerald-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatPrecio(metrics.ingresos_hoy) }}</div>
                <p class="mt-1 text-[11px] text-muted-foreground">Facturación acumulada en el día</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-xs font-medium text-muted-foreground">Turnos Hoy</CardTitle>
                <CalendarCheck class="h-4 w-4 text-primary" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ metrics.reservas_hoy }}</div>
                <p class="mt-1 text-[11px] text-muted-foreground">Reservas agendadas para hoy</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-xs font-medium text-muted-foreground">Ingresos del Mes</CardTitle>
                <TrendingUp class="h-4 w-4 text-blue-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatPrecio(metrics.ingresos_mes) }}</div>
                <p class="mt-1 text-[11px] text-muted-foreground">{{ metrics.reservas_mes }} turnos en este mes</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-xs font-medium text-muted-foreground">Canchas Habilitadas</CardTitle>
                <Dumbbell class="h-4 w-4 text-orange-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ metrics.total_canchas }}</div>
                <p class="mt-1 text-[11px] text-muted-foreground">Canchas operativas</p>
            </CardContent>
        </Card>
    </div>
</template>