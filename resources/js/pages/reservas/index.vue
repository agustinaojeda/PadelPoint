<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { generarSlotsCancha } from '@/lib/turnos';
import type { BreadcrumbItem, Cancha, Reserva, SlotTurno } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import CanchaGrillaCard from './components/CanchaGrillaCard.vue';
import FiltroFecha from './components/FiltroFecha.vue';
import ModalDetalleReserva from './components/ModalDetalleReserva.vue';
import ModalReserva from './components/ModalReserva.vue';

const props = defineProps<{
    canchas: Cancha[];
    reservas: Reserva[];
    fechaSeleccionada: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Reservas', href: route('reservas.index') },
];

const fecha = ref(props.fechaSeleccionada);
const reservaParaDetalle = ref<Reserva | null>(null);
const modalDetalleAbierto = ref(false);
const modalAbierto = ref(false);
const turnoSeleccionado = ref<{ cancha: Cancha; slot: SlotTurno } | null>(null);

const cambiarFecha = (nuevaFecha: string) => {
    fecha.value = nuevaFecha;
    router.get(route('reservas.index'), { fecha: nuevaFecha }, { preserveState: true, replace: true });
};

const mapaCanchasSlots = computed(() => {
    const [year, month, day] = fecha.value.split('-').map(Number);
    const fechaObj = new Date(year, month - 1, day);
    const diaSemana = fechaObj.getDay();

    const canchasDisponiblesHoy = props.canchas.filter((cancha) => {
        return Array.isArray(cancha.dias_disponibles) && cancha.dias_disponibles.includes(diaSemana);
    });

    return canchasDisponiblesHoy.map((cancha) => ({
        cancha,
        slots: generarSlotsCancha(cancha, props.reservas),
    }));
});

const verDetalleReserva = (reserva?: Reserva) => {
    if (!reserva) return;
    reservaParaDetalle.value = reserva;
    modalDetalleAbierto.value = true;
};

const abrirModalNuevaReserva = (cancha: Cancha, slot: SlotTurno) => {
    turnoSeleccionado.value = { cancha, slot };
    modalAbierto.value = true;
};

const cerrarModal = () => {
    modalAbierto.value = false;
    turnoSeleccionado.value = null;
};
</script>

<template>
    <Head title="Gestión de Reservas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Gestión de Reservas</h1>
                    <p class="mt-1 text-muted-foreground">Planificación de turnos y disponibilidad por cancha.</p>
                </div>

                <FiltroFecha :fecha="fecha" @cambiar="cambiarFecha" />
            </div>

            <div class="space-y-6 pt-6">
                <div
                    v-if="canchas.length === 0"
                    class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center"
                >
                    <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No hay canchas disponibles</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Primero debes registrar e habilitar canchas para comenzar a recibir reservas.</p>
                </div>

                <div
                    v-else-if="mapaCanchasSlots.length === 0"
                    class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center"
                >
                    <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No hay canchas operativas para esta fecha</h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Ninguna cancha atiende el día de la semana correspondiente a la fecha seleccionada.
                    </p>
                </div>

                <!--listado de canchas-->
                <div v-else class="flex flex-wrap gap-6">
                    <CanchaGrillaCard
                        v-for="{ cancha, slots } in mapaCanchasSlots"
                        :key="cancha.id"
                        :cancha="cancha"
                        :slots="slots"
                        @reservar-slot="(slot) => abrirModalNuevaReserva(cancha, slot)"
                        @ver-detalle-reserva="verDetalleReserva"
                    />
                </div>
            </div>
        </div>
    </AppLayout>

    <ModalReserva
        v-model:open="modalAbierto"
        :cancha="turnoSeleccionado?.cancha ?? null"
        :slot="turnoSeleccionado?.slot ?? null"
        :fecha="fecha"
        @close="cerrarModal"
    />

    <ModalDetalleReserva
        v-model:open="modalDetalleAbierto"
        :reserva="reservaParaDetalle"
        @close="modalDetalleAbierto = false"
    />
</template>