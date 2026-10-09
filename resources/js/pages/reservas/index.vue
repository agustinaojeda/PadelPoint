<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { generarSlotsCancha } from '@/lib/turnos';
import type { BreadcrumbItem, Cancha, Reserva, SlotTurno } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight, Eye, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ModalDetalleReserva from './components/ModalDetalleReserva.vue';
import ModalReserva from './components/ModalReserva.vue';

const reservaParaDetalle = ref<Reserva | null>(null);
const modalDetalleAbierto = ref(false);

const verDetalleReserva = (reserva?: Reserva) => {
    if (!reserva) return;
    reservaParaDetalle.value = reserva;
    modalDetalleAbierto.value = true;
};

const modalAbierto = ref(false);

const props = defineProps<{
    canchas: Cancha[];
    reservas: Reserva[];
    fechaSeleccionada: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reservas', href: '/reservas' },
];

const fecha = ref(props.fechaSeleccionada);

const cambiarFecha = (nuevaFecha: string) => {
    fecha.value = nuevaFecha;
    router.get(route('reservas.index'), { fecha: nuevaFecha }, { preserveState: true, replace: true });
};

const navegarDia = (dias: number) => {
    const date = new Date(fecha.value + 'T00:00:00');
    date.setDate(date.getDate() + dias);
    const isoDate = date.toISOString().split('T')[0];
    cambiarFecha(isoDate);
};

const mapaCanchasSlots = computed(() => {
    const fechaObj = new Date(fecha.value + 'T00:00:00');
    const diaSemana = fechaObj.getDay();

    const canchasDisponiblesHoy = props.canchas.filter((cancha) => {
        return Array.isArray(cancha.dias_disponibles) && cancha.dias_disponibles.includes(diaSemana);
    });

    return canchasDisponiblesHoy.map((cancha) => ({
        cancha,
        slots: generarSlotsCancha(cancha, props.reservas),
    }));
});

const turnoSeleccionado = ref<{ cancha: Cancha; slot: SlotTurno } | null>(null);

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

                <div class="flex items-center gap-2">
                    <Button variant="outline" size="icon" @click="navegarDia(-1)">
                        <ChevronLeft class="h-4 w-4" />
                    </Button>
                    <Button variant="outline" @click="cambiarFecha(new Date().toISOString().split('T')[0])"> Hoy </Button>
                    <input
                        type="date"
                        v-model="fecha"
                        @change="cambiarFecha(fecha)"
                        class="h-9 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    />
                    <Button variant="outline" size="icon" @click="navegarDia(1)">
                        <ChevronRight class="h-4 w-4" />
                    </Button>
                </div>
            </div>
            <div class="space-y-6 p-6">
                <div
                    v-if="mapaCanchasSlots.length === 0"
                    class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center"
                >
                    <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No hay canchas operativas para esta fecha</h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Ninguna cancha atiende el día de la semana correspondiente a la fecha seleccionada.
                    </p>
                </div>
                <div v-if="canchas.length === 0" class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center">
                    <CalendarIcon class="h-12 w-12 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold">No hay canchas disponibles</h3>
                    <p class="mb-4 text-sm text-muted-foreground">Primero debes registrar e habilitar canchas para comenzar a recibir reservas.</p>
                </div>

                <div v-else class="flex flex-wrap gap-6">
                    <Card v-for="{ cancha, slots } in mapaCanchasSlots" :key="cancha.id" class="flex min-w-[280px] max-w-full flex-1 flex-col">
                        <CardHeader class="border-b pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-lg font-semibold">{{ cancha.nombre }}</CardTitle>
                                <Badge variant="outline">{{ cancha.es_techada ? 'Techada' : 'Descubierta' }}</Badge>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Turnos de {{ cancha.duracion_turno }} min | ${{ cancha.precio.toLocaleString('es-AR') }}
                            </p>
                        </CardHeader>

                        <CardContent class="flex-1 space-y-2 pt-4">
                            <div
                                v-for="slot in slots"
                                :key="slot.hora_inicio"
                                class="flex items-center justify-between rounded-lg border p-2.5 text-sm transition"
                                :class="[
                                    slot.disponible
                                        ? 'border-border bg-card hover:border-primary/50'
                                        : slot.reserva?.estado === 'confirmada'
                                          ? 'border-emerald-500/30 bg-emerald-500/10 dark:bg-emerald-950/20'
                                          : 'border-orange-500/30 bg-orange-500/10 dark:bg-orange-950/20',
                                ]"
                            >
                                <div class="flex flex-col">
                                    <span class="font-medium text-foreground"> {{ slot.hora_inicio }} - {{ slot.hora_fin }} hs </span>

                                    <span
                                        v-if="!slot.disponible && slot.reserva"
                                        class="text-xs font-medium"
                                        :class="
                                            slot.reserva.estado === 'confirmada'
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : 'text-orange-600 dark:text-orange-400'
                                        "
                                    >
                                        {{ slot.reserva.nombre_cliente }}
                                    </span>
                                </div>

                                <Button
                                    v-if="slot.disponible"
                                    size="sm"
                                    variant="ghost"
                                    class="h-8 gap-1 text-xs text-muted-foreground hover:text-foreground"
                                    @click="abrirModalNuevaReserva(cancha, slot)"
                                >
                                    <Plus class="h-3.5 w-3.5" /> Reservar
                                </Button>
                                <div v-else class="flex items-center gap-2">
                                    <div
                                        class="flex items-center gap-1 text-xs font-medium capitalize"
                                        :class="
                                            slot.reserva?.estado === 'confirmada'
                                                ? 'text-emerald-700 dark:text-emerald-300'
                                                : 'text-orange-700 dark:text-orange-300'
                                        "
                                    >
                                        <span>{{ slot.reserva?.estado ?? 'reservado' }}</span>
                                    </div>

                                    <Badge
                                        variant="secondary"
                                        class="flex cursor-pointer items-center gap-1 border-none text-xs capitalize transition-colors"
                                        :class="
                                            slot.reserva?.estado === 'confirmada'
                                                ? 'bg-emerald-500/20 text-emerald-700 hover:bg-emerald-500/30 dark:text-emerald-300'
                                                : 'bg-orange-500/20 text-orange-700 hover:bg-orange-500/30 dark:text-orange-300'
                                        "
                                        @click="verDetalleReserva(slot.reserva)"
                                    >
                                        <Eye class="h-3 w-3" />
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
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
    <ModalDetalleReserva v-model:open="modalDetalleAbierto" :reserva="reservaParaDetalle" @close="modalDetalleAbierto = false" />
</template>
