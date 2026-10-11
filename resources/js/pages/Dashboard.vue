<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, CanchaResumen, FranjaHoraria, MetricProps, OcupacionDia, Reserva } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar } from 'lucide-vue-next';

import DemandaDiasCard from '@/components/dashboard/DemandaDiasCard.vue';
import MetricasCard from '@/components/dashboard/MetricasCard.vue';
import OcupacionFranjasCard from '@/components/dashboard/OcupacionFranjasCard.vue';
import ProxReservasCard from '@/components/dashboard/ProxReservasCard.vue';
import RendimientoCanchasCard from '@/components/dashboard/RendimientoCanchasCard.vue';

withDefaults(
    defineProps<{
        metrics?: MetricProps;
        franjasHorarias?: FranjaHoraria[];
        ocupacionDias?: OcupacionDia[];
        proximasReservas?: Reserva[];
        canchasResumen?: CanchaResumen[];
    }>(),
    {
        metrics: () => ({
            ingresos_hoy: 0,
            reservas_hoy: 0,
            ingresos_mes: 0,
            reservas_mes: 0,
            total_canchas: 0,
        }),
        franjasHorarias: () => [],
        ocupacionDias: () => [],
        proximasReservas: () => [],
        canchasResumen: () => [],
    },
);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: route('dashboard') }];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Panel de Control</h1>
                    <p class="mt-1 text-muted-foreground">
                        Métricas de rendimiento, ocupación e ingresos del complejo PadelPoint.
                    </p>
                </div>
                <Button as-child variant="default" size="sm" class="gap-1.5">
                    <Link :href="route('reservas.index')">
                        <Calendar class="h-4 w-4" />
                        Ver Grilla de Reservas
                    </Link>
                </Button>
            </div>

            <MetricasCard :metrics="metrics" />

            <div class="grid gap-6 md:grid-cols-2">
                <OcupacionFranjasCard :franjas="franjasHorarias" />
                <DemandaDiasCard :dias="ocupacionDias" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <ProxReservasCard :reservas="proximasReservas" />
                <RendimientoCanchasCard :canchas="canchasResumen" />
            </div>
        </div>
    </AppLayout>
</template>