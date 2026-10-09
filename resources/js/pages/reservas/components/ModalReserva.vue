<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Cancha, SlotTurno, FormReservaData } from '@/types';
import BaseModal from '@/components/ui/BaseModal.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Calendar, Clock, DollarSign, User, Phone, FileText } from 'lucide-vue-next';

const props = defineProps<{
    open: boolean;
    cancha: Cancha | null;
    slot: SlotTurno | null;
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
    () => [props.cancha, props.slot, props.fecha],
    () => {
        if (props.cancha && props.slot) {
            form.cancha_id = props.cancha.id;
            form.fecha_turno = props.fecha;
            form.hora_inicio = props.slot.hora_inicio;
            form.hora_fin = props.slot.hora_fin;
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
        <div v-if="cancha && slot" class="rounded-lg border bg-muted/40 p-3 text-sm space-y-2 mb-4">
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
                    <span>{{ slot.hora_inicio }} - {{ slot.hora_fin }} hs</span>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="nombre_cliente" class="flex items-center gap-1.5 text-xs font-medium mb-1">
                    <User class="h-3.5 w-3.5 text-muted-foreground" />
                    Nombre Completo del Cliente *
                </label>
                <input
                    id="nombre_cliente"
                    v-model="form.nombre_cliente"
                    type="text"
                    placeholder="Ej. Juan Pérez"
                    class="w-full px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.nombre_cliente" class="text-xs text-destructive mt-1 block">
                    {{ form.errors.nombre_cliente }}
                </span>
            </div>

            <div>
                <label for="telefono_cliente" class="flex items-center gap-1.5 text-xs font-medium mb-1">
                    <Phone class="h-3.5 w-3.5 text-muted-foreground" />
                    Teléfono / WhatsApp *
                </label>
                <input
                    id="telefono_cliente"
                    v-model="form.telefono_cliente"
                    type="text"
                    placeholder="Ej. 2664123456"
                    class="w-full px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.telefono_cliente" class="text-xs text-destructive mt-1 block">
                    {{ form.errors.telefono_cliente }}
                </span>
            </div>

            <div>
                <label for="monto_total" class="flex items-center gap-1.5 text-xs font-medium mb-1">
                    <DollarSign class="h-3.5 w-3.5 text-muted-foreground" />
                    Monto Total ($) *
                </label>
                <input
                    id="monto_total"
                    v-model="form.monto_total"
                    type="number"
                    step="0.01"
                    class="w-full px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                    :disabled="form.processing"
                    required
                />
                <span v-if="form.errors.monto_total" class="text-xs text-destructive mt-1 block">
                    {{ form.errors.monto_total }}
                </span>
            </div>

            <div>
                <label for="observaciones" class="flex items-center gap-1.5 text-xs font-medium mb-1">
                    <FileText class="h-3.5 w-3.5 text-muted-foreground" />
                    Observaciones (Opcional)
                </label>
                <textarea
                    id="observaciones"
                    v-model="form.observaciones"
                    rows="2"
                    placeholder="Ej. Dejó seña del 50%..."
                    class="w-full px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                    :disabled="form.processing"
                ></textarea>
            </div>

            <p v-if="form.errors.hora_inicio" class="text-xs font-medium text-destructive">
                {{ form.errors.hora_inicio }}
            </p>

            <div class="flex justify-end gap-3 pt-4 border-t">
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