<script setup lang="ts">
import BaseModal from '@/components/shared/BaseModal.vue';
import EstadoReservaBadge from '@/components/shared/EstadoReservaBadge.vue';
import { Button } from '@/components/ui/button';
import { formatHora, formatPrecio, formatearFecha, whatsappUrl } from '@/lib/formatos';
import { ESTADOS_RESERVA } from '@/lib/reservas';
import type { EstadoReserva, Reserva } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Calendar, Clock, DollarSign, FileText, Mail, Phone, Trash2, User } from 'lucide-vue-next';

const props = defineProps<{
    open: boolean;
    reserva: Reserva | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const formEstado = useForm({
    estado: 'confirmada' as EstadoReserva,
});

const formEliminar = useForm({});

const cambiarEstado = (nuevoEstado: EstadoReserva) => {
    if (!props.reserva) return;

    formEstado.estado = nuevoEstado;
    formEstado.patch(route('admin.reservas.cambiar-estado', { reserva: props.reserva.id }), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const cancelarReserva = () => {
    if (!props.reserva) return;
    if (confirm('¿Estás seguro de que deseas cancelar/eliminar esta reserva? El turno quedará disponible nuevamente.')) {
        formEliminar.delete(route('admin.reservas.destroy', { reserva: props.reserva.id }), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>

<template>
    <BaseModal
        :open="open"
        title="Detalle de la Reserva"
        description="Información general del turno y gestión de estado."
        maxWidth="max-w-md"
        @close="$emit('close')"
    >
        <div v-if="reserva" class="space-y-4">
            <div class="flex items-center justify-between rounded-lg border bg-muted/30 p-3">
                <div>
                    <h4 class="font-semibold text-foreground">{{ reserva.cancha?.nombre ?? 'Cancha' }}</h4>
                    <div class="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1"> <Calendar class="h-3.5 w-3.5" /> {{ formatearFecha(reserva.fecha_turno) }} </span>
                        <span class="flex items-center gap-1">
                            <Clock class="h-3.5 w-3.5" /> {{ formatHora(reserva.hora_inicio) }} - {{ formatHora(reserva.hora_fin) }} hs
                        </span>
                    </div>
                </div>
                <EstadoReservaBadge :estado="reserva.estado" />
            </div>

            <div class="space-y-2 border-t pt-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-xs text-muted-foreground"> <User class="h-3.5 w-3.5" /> Cliente: </span>
                    <span class="font-medium text-foreground">{{ reserva.nombre_cliente }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-xs text-muted-foreground"> <Mail class="h-3.5 w-3.5" /> Correo: </span>
                    <a v-if="reserva.email_cliente" :href="`mailto:${reserva.email_cliente}`" class="font-medium text-primary hover:underline">
                        {{ reserva.email_cliente }}
                    </a>
                    <span v-else class="font-medium text-muted-foreground">—</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-xs text-muted-foreground"> <Phone class="h-3.5 w-3.5" /> Teléfono / WhatsApp: </span>
                    <a
                        v-if="reserva.telefono_cliente"
                        :href="whatsappUrl(reserva.telefono_cliente)"
                        target="_blank"
                        class="font-medium text-primary hover:underline"
                    >
                        {{ reserva.telefono_cliente }}
                    </a>
                    <span v-else class="font-medium text-muted-foreground">—</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-xs text-muted-foreground"> <DollarSign class="h-3.5 w-3.5" /> Monto Total: </span>
                    <span class="font-semibold text-foreground">{{ formatPrecio(reserva.monto_total) }}</span>
                </div>

                <div v-if="reserva.observaciones" class="space-y-1 pt-1">
                    <span class="flex items-center gap-1.5 text-xs text-muted-foreground"> <FileText class="h-3.5 w-3.5" /> Observaciones: </span>
                    <p class="rounded-md bg-muted/50 p-2 text-xs italic text-muted-foreground">"{{ reserva.observaciones }}"</p>
                </div>
            </div>

            <div class="space-y-2 border-t pt-3">
                <div v-if="reserva.estado === 'completada'" class="rounded-md bg-muted p-2.5 text-center text-xs italic text-muted-foreground">
                    Esta reserva fue completada y su historial no se puede modificar ni eliminar.
                </div>
                <div v-else class="space-y-2 border-t pt-3">
                    <label class="block text-xs font-medium text-muted-foreground">Cambiar Estado:</label>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="estado in ESTADOS_RESERVA"
                            :key="estado.valor"
                            size="sm"
                            :variant="estado.variant"
                            class="h-7 text-xs"
                            :disabled="reserva.estado === estado.valor || formEstado.processing"
                            @click="cambiarEstado(estado.valor)"
                        >
                            {{ estado.etiqueta }}
                        </Button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t pt-4">
                <Button
                    v-if="reserva.estado !== 'completada'"
                    type="button"
                    variant="destructive"
                    size="sm"
                    class="h-8 gap-1 text-xs"
                    :disabled="formEliminar.processing"
                    @click="cancelarReserva"
                >
                    <Trash2 class="h-3.5 w-3.5" /> Eliminar Reserva
                </Button>
                <div v-else></div>

                <Button type="button" variant="outline" size="sm" class="h-8 text-xs" @click="$emit('close')"> Cerrar </Button>
            </div>
        </div>
    </BaseModal>
</template>
