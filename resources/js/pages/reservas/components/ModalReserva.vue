<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import BaseModal from '@/components/ui/BaseModal.vue';
import { Button } from '@/components/ui/button';
import type { Cancha, FormReservaData, SlotTurno } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Calendar, Clock, DollarSign, FileText, Phone, User } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
    cancha: Cancha | null;
    turnoSlot: SlotTurno | null;
    fecha: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'success'): void;
}>();

const form = useForm<FormReservaData>({
    cancha_id: null,
    fecha_turno: '',
    hora_inicio: '',
    hora_fin: '',
    nombre_cliente: '',
    telefono_cliente: '',
    monto_total: 0,
    observaciones: '',
});

watch(
    () => [props.cancha, props.turnoSlot, props.fecha],
    () => {
        if (props.cancha && props.turnoSlot) {
            form.cancha_id = props.cancha.id;
            form.fecha_turno = props.fecha;
            form.hora_inicio = props.turnoSlot.hora_inicio;
            form.hora_fin = props.turnoSlot.hora_fin;
            form.monto_total = props.cancha.precio;
            form.clearErrors();
        }
    },
    { immediate: true }
);

const submit = () => {
    form.post(route('reservas.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('nombre_cliente', 'telefono_cliente', 'observaciones');
            emit('close');
            emit('success');
        },
    });
};

const cerrarModal = () => {
    form.clearErrors();
    emit('close');
};
</script>

<template>
    <BaseModal
        :open="open"
        title="Nueva Reserva"
        description="Completa los datos del cliente para confirmar el turno."
        maxWidth="max-w-md"
        @close="cerrarModal"
    >
        <div v-if="cancha && turnoSlot" class="mb-4 space-y-2 rounded-lg border bg-muted/40 p-3 text-sm">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-foreground">{{ cancha.nombre }}</span>
                <Badge variant="outline">{{ cancha.es_techada ? 'Techada' : 'Descubierta' }}</Badge>
            </div>
            <div class="flex items-center gap-4 text-xs text-muted-foreground">
                <div class="flex items-center gap-1">
                    <Calendar class="h-3.5 w-3.5" />
                    <span>{{ fecha }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <Clock class="h-3.5 w-3.5" />
                    <span>{{ turnoSlot.hora_inicio }} - {{ turnoSlot.hora_fin }} hs</span>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="nombre_cliente" class="mb-1 flex items-center gap-1.5 text-xs font-medium">
                    <User class="h-3.5 w-3.5 text-muted-foreground" />
                    Nombre Completo del Cliente *
                </label>
                <input
                    id="nombre_cliente"
                    v-model="form.nombre_cliente"
                    type="text"
                    placeholder="Ej. Juan Pérez"
                    class="w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.nombre_cliente" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.nombre_cliente }}
                </span>
            </div>

            <div>
                <label for="telefono_cliente" class="mb-1 flex items-center gap-1.5 text-xs font-medium">
                    <Phone class="h-3.5 w-3.5 text-muted-foreground" />
                    Teléfono / WhatsApp *
                </label>
                <input
                    id="telefono_cliente"
                    v-model="form.telefono_cliente"
                    type="text"
                    placeholder="Ej. 2664123456"
                    class="w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.telefono_cliente" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.telefono_cliente }}
                </span>
            </div>

            <div>
                <label for="monto_total" class="mb-1 flex items-center gap-1.5 text-xs font-medium">
                    <DollarSign class="h-3.5 w-3.5 text-muted-foreground" />
                    Monto Total ($) *
                </label>
                <input
                    id="monto_total"
                    v-model="form.monto_total"
                    type="number"
                    step="0.01"
                    class="w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.monto_total" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.monto_total }}
                </span>
            </div>

            <div>
                <label for="observaciones" class="mb-1 flex items-center gap-1.5 text-xs font-medium">
                    <FileText class="h-3.5 w-3.5 text-muted-foreground" />
                    Observaciones (Opcional)
                </label>
                <textarea
                    id="observaciones"
                    v-model="form.observaciones"
                    rows="2"
                    placeholder="Ej. Dejó seña del 50%..."
                    class="w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    :disabled="form.processing"
                ></textarea>
            </div>

            <p v-if="form.errors.hora_inicio" class="text-xs font-medium text-destructive">
                {{ form.errors.hora_inicio }}
            </p>

            <div class="flex justify-end gap-3 border-t pt-4">
                <Button type="button" variant="outline" @click="cerrarModal" :disabled="form.processing">
                    Cancelar
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Guardando...' : 'Confirmar Reserva' }}
                </Button>
            </div>
        </form>
    </BaseModal>
</template>