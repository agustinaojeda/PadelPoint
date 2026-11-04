<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import BaseModal from '@/components/shared/BaseModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatearFechaLarga } from '@/lib/fechas';
import { formatHora, formatPrecio } from '@/lib/formatos';
import { validarReservaPublica } from '@/lib/validaciones';
import type { CanchaConSlots, ReservaConfirmada, SlotPublico } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { CalendarDays, Clock, Loader2 } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
    cancha: CanchaConSlots | null;
    turno: SlotPublico | null;
    fecha: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'success', reserva: ReservaConfirmada): void;
}>();

const form = useForm({
    cancha_id: null as number | null,
    fecha_turno: '',
    hora_inicio: '',
    nombre_cliente: '',
    email_cliente: '',
    telefono_cliente: '',
    observaciones: '',
});

watch(
    [() => props.cancha, () => props.turno, () => props.fecha],
    () => {
        if (props.cancha && props.turno) {
            form.cancha_id = props.cancha.id;
            form.fecha_turno = props.fecha;
            form.hora_inicio = props.turno.hora_inicio;
            form.clearErrors();
        }
    },
    { immediate: true },
);

const cerrarModal = () => {
    form.clearErrors();
    emit('close');
};

const submit = () => {
    form.clearErrors();

    const errores = validarReservaPublica(form);

    if (errores.length > 0) {
        errores.forEach(({ campo, mensaje }) => form.setError(campo, mensaje));
        return;
    }

    form.post(route('reservas.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            if (props.cancha && props.turno) {
                emit('success', {
                    cancha: props.cancha.nombre,
                    fecha: form.fecha_turno,
                    hora_inicio: form.hora_inicio,
                    hora_fin: props.turno.hora_fin,
                });
            }

            form.reset();
        },
    });
};
</script>

<template>
    <BaseModal
        :open="open"
        title="Confirmá tu reserva"
        description="Completá tus datos y nos comunicamos para confirmar el turno."
        maxWidth="max-w-md"
        @close="cerrarModal"
    >
        <div v-if="cancha && turno" class="mb-5 space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
            <div class="flex items-center justify-between gap-3">
                <span class="font-semibold text-slate-900">{{ cancha.nombre }}</span>
                <span class="font-bold text-lime-600">{{ formatPrecio(cancha.precio) }}</span>
            </div>
            <p class="flex items-center gap-1.5 text-xs capitalize text-slate-600">
                <CalendarDays class="h-3.5 w-3.5 text-lime-600" />
                {{ formatearFechaLarga(fecha) }}
            </p>
            <p class="flex items-center gap-1.5 text-xs text-slate-600">
                <Clock class="h-3.5 w-3.5 text-lime-600" />
                {{ formatHora(turno.hora_inicio) }} a {{ formatHora(turno.hora_fin) }} hs
            </p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <Label for="nombre_cliente" class="mb-1 block text-xs font-semibold text-slate-700">Nombre completo *</Label>
                <Input
                    id="nombre_cliente"
                    v-model="form.nombre_cliente"
                    type="text"
                    autocomplete="name"
                    placeholder="Ej. Juan Pérez"
                    class="rounded-xl border-slate-200 bg-white text-sm text-slate-900 focus-visible:ring-lime-200"
                    minlength="3"
                    maxlength="255"
                    required
                    :disabled="form.processing"
                />
                <InputError :message="form.errors.nombre_cliente" class="mt-1" />
            </div>

            <div>
                <Label for="email_cliente" class="mb-1 block text-xs font-semibold text-slate-700">Correo electrónico *</Label>
                <Input
                    id="email_cliente"
                    v-model="form.email_cliente"
                    type="email"
                    autocomplete="email"
                    placeholder="Ej. juan@correo.com"
                    class="rounded-xl border-slate-200 bg-white text-sm text-slate-900 focus-visible:ring-lime-200"
                    maxlength="255"
                    :disabled="form.processing"
                    required
                />
                <InputError :message="form.errors.email_cliente" class="mt-1" />
            </div>

            <div>
                <Label for="telefono_cliente" class="mb-1 block text-xs font-semibold text-slate-700">Teléfono / WhatsApp (opcional)</Label>
                <Input
                    id="telefono_cliente"
                    v-model="form.telefono_cliente"
                    type="tel"
                    autocomplete="tel"
                    placeholder="Ej. 2664123456"
                    class="rounded-xl border-slate-200 bg-white text-sm text-slate-900 focus-visible:ring-lime-200"
                    minlength="6"
                    maxlength="50"
                    :disabled="form.processing"
                />
                <InputError :message="form.errors.telefono_cliente" class="mt-1" />
            </div>

            <div>
                <Label for="observaciones" class="mb-1 block text-xs font-semibold text-slate-700">Observaciones (opcional)</Label>
                <textarea
                    id="observaciones"
                    v-model="form.observaciones"
                    rows="2"
                    placeholder="Ej. Necesito paletas de alquiler."
                    class="flex w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none focus-visible:border-lime-400 focus-visible:ring-2 focus-visible:ring-lime-200 disabled:cursor-not-allowed disabled:opacity-50"
                    maxlength="500"
                    :disabled="form.processing"
                ></textarea>
                <InputError :message="form.errors.observaciones" class="mt-1" />
            </div>

            <InputError :message="form.errors.hora_inicio" />
            <InputError :message="form.errors.fecha_turno" />
            <InputError :message="form.errors.cancha_id" />

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                <Button type="button" variant="outline" class="rounded-xl px-5 py-2.5" :disabled="form.processing" @click="cerrarModal">
                    Cancelar
                </Button>
                <Button
                    type="submit"
                    class="rounded-xl bg-lime-400 px-5 py-2.5 font-bold text-slate-950 shadow-lg hover:-translate-y-0.5 hover:bg-lime-500"
                    :disabled="form.processing"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ form.processing ? 'Enviando...' : 'Solicitar reserva' }}
                </Button>
            </div>
        </form>
    </BaseModal>
</template>
