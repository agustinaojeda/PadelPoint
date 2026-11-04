<script setup lang="ts">
import EmptyState from '@/components/shared/EmptyState.vue';
import { useFechaReservas } from '@/composables/useFechaReservas';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { turnoYaComenzo } from '@/lib/validaciones';
import type { CanchaConSlots, ReservaConfirmada, SlotPublico } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CalendarX } from 'lucide-vue-next';
import { nextTick, ref } from 'vue';
import { toast } from 'vue-sonner';

import CanchaDisponibleCard from './components/CanchaDisponibleCard.vue';
import ConfirmacionReserva from './components/ConfirmacionReserva.vue';
import ModalReservaPublica from './components/ModalReservaPublica.vue';
import SelectorFechaReservas from './components/SelectorFechaReservas.vue';

const props = defineProps<{
    canchas: CanchaConSlots[];
    fechaSeleccionada: string;
    canchaDestacada: number | null;
}>();

const { fecha, cambiarFecha } = useFechaReservas(() => props.fechaSeleccionada, route('reservas.index'));

const turnoSeleccionado = ref<{ cancha: CanchaConSlots; slot: SlotPublico } | null>(null);
const modalAbierto = ref(false);
const reservaConfirmada = ref<ReservaConfirmada | null>(null);

const abrirReserva = (cancha: CanchaConSlots, slot: SlotPublico) => {
    if (!slot.disponible) {
        return;
    }

    if (turnoYaComenzo(fecha.value, slot.hora_inicio)) {
        toast.error('Ese horario ya pasó. Elegí un turno posterior.');
        return;
    }

    turnoSeleccionado.value = { cancha, slot };
    modalAbierto.value = true;
};

const cerrarModal = () => {
    modalAbierto.value = false;
    turnoSeleccionado.value = null;
};

const confirmarReserva = (reserva: ReservaConfirmada) => {
    reservaConfirmada.value = reserva;
    cerrarModal();

    nextTick(() => {
        document.getElementById('confirmacion')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
};
</script>

<template>
    <Head title="Reservar turno | PadelPoint" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-teal-950 pb-16 pt-28 text-white md:pt-32">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-lime-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 h-72 w-72 rounded-full bg-teal-500/30 blur-3xl"></div>

            <div class="relative mx-auto max-w-6xl px-6 md:px-12">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-lime-400">Reservas online</p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight md:text-5xl">Elegí tu cancha y tu horario</h1>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-teal-100/80 md:text-base">
                    Consultá la disponibilidad actualizada, seleccioná un turno y completá tus datos. No necesitás crear una cuenta.
                </p>

                <SelectorFechaReservas :fecha="fecha" @cambiar="cambiarFecha" />
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 py-12 md:px-12">
            <ConfirmacionReserva v-if="reservaConfirmada" :reserva="reservaConfirmada" @close="reservaConfirmada = null" />

            <EmptyState
                v-if="canchas.length === 0"
                titulo="No hay turnos para esta fecha"
                descripcion="Ninguna cancha atiende el día seleccionado. Probá con otra fecha."
                class="mx-auto max-w-lg rounded-3xl border-slate-300 bg-white p-10"
            >
                <template #icon>
                    <div class="mb-4 rounded-full bg-slate-100 p-4 text-slate-500">
                        <CalendarX class="h-10 w-10" />
                    </div>
                </template>
            </EmptyState>

            <div v-else class="space-y-6">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-extrabold text-slate-900">Canchas disponibles</h2>
                    <p class="text-xs text-slate-500">Los horarios tachados ya están reservados.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <CanchaDisponibleCard
                        v-for="cancha in canchas"
                        :id="`cancha-${cancha.id}`"
                        :key="cancha.id"
                        :cancha="cancha"
                        :destacada="canchaDestacada === cancha.id"
                        @reservar="abrirReserva"
                    />
                </div>
            </div>
        </section>

        <ModalReservaPublica
            :open="modalAbierto"
            :cancha="turnoSeleccionado?.cancha ?? null"
            :turno="turnoSeleccionado?.slot ?? null"
            :fecha="fecha"
            @close="cerrarModal"
            @success="confirmarReserva"
        />
    </PublicLayout>
</template>
