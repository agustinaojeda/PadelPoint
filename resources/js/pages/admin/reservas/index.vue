<script setup lang="ts">
import EmptyState from '@/components/shared/EmptyState.vue';
import { useFechaReservas } from '@/composables/useFechaReservas';
import AppLayout from '@/layouts/AppLayout.vue';
import { generarSlotsCancha } from '@/lib/turnos';
import type { BreadcrumbItem, Cancha, Reserva, SlotTurno } from '@/types';
import { Head } from '@inertiajs/vue3';
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
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Reservas', href: route('admin.reservas.index') },
];

const { fecha, cambiarFecha } = useFechaReservas(() => props.fechaSeleccionada, route('admin.reservas.index'));

const reservaParaDetalle = ref<Reserva | null>(null);
const modalDetalleAbierto = ref(false);
const modalAbierto = ref(false);
const turnoSeleccionado = ref<{ cancha: Cancha; slot: SlotTurno } | null>(null);

const mapaCanchasSlots = computed(() => {
    const [year, month, day] = fecha.value.split('-').map(Number);
    const fechaObj = new Date(year, month - 1, day);
    const diaSemana = fechaObj.getDay();

    const canchasDisponiblesHoy = props.canchas.filter((cancha) => {
        const dias = Array.isArray(cancha.dias_disponibles) ? cancha.dias_disponibles.map(Number) : [];

        return dias.includes(diaSemana);
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
                <EmptyState
                    v-if="canchas.length === 0"
                    titulo="No hay canchas disponibles"
                    descripcion="Primero debes registrar e habilitar canchas para comenzar a recibir reservas."
                >
                    <template #icon>
                        <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    </template>
                </EmptyState>

                <EmptyState
                    v-else-if="mapaCanchasSlots.length === 0"
                    titulo="No hay canchas operativas para esta fecha"
                    descripcion="Ninguna cancha atiende el día de la semana correspondiente a la fecha seleccionada."
                >
                    <template #icon>
                        <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    </template>
                </EmptyState>

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
        :turno-slot="turnoSeleccionado?.slot ?? null"
        :fecha="fecha"
        @close="cerrarModal"
    />

    <ModalDetalleReserva v-model:open="modalDetalleAbierto" :reserva="reservaParaDetalle" @close="modalDetalleAbierto = false" />
</template>
