<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import BaseModal from '@/components/shared/BaseModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useCanchaForm } from '@/composables/useCanchaForm';
import { diasSemana } from '@/lib/formatos';
import type { Cancha } from '@/types';

const props = defineProps<{
    open: boolean;
    cancha?: Cancha | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const { form, imagePreview, handleImageChange, clearSelectedImage, submit } = useCanchaForm(props, emit);
</script>

<template>
    <BaseModal :open="open" :title="cancha ? 'Editar Cancha' : 'Registrar Nueva Cancha'" @close="$emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <Label class="mb-1 block text-sm font-medium">Imagen de la Cancha (opcional)</Label>
                <div class="flex items-center gap-4">
                    <div class="relative flex h-24 w-24 shrink-0 items-center overflow-hidden rounded-lg border bg-muted">
                        <img v-if="imagePreview" :src="imagePreview" alt="Vista previa" class="h-full w-full object-cover" />
                        <span v-else class="px-1 text-center text-xs text-muted-foreground"> Imagen por defecto </span>
                    </div>

                    <div class="flex-1 space-y-2">
                        <input type="file" id="imagen" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleImageChange" />
                        <label
                            for="imagen"
                            class="inline-flex cursor-pointer items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-xs font-medium transition-colors hover:bg-muted"
                        >
                            {{ imagePreview ? 'Cambiar Imagen' : 'Subir Imagen' }}
                        </label>
                        <p v-if="!imagePreview" class="text-[11px] text-muted-foreground">
                            Si no seleccionas ninguna, se asignará una imagen por defecto.
                        </p>
                        <button v-if="form.imagen" type="button" @click="clearSelectedImage" class="block text-xs text-destructive hover:underline">
                            Quitar selección
                        </button>
                    </div>
                </div>
                <InputError :message="form.errors.imagen" class="mt-1" />
            </div>

            <div>
                <Label for="cancha-nombre" class="text-sm font-medium">Nombre</Label>
                <Input id="cancha-nombre" v-model="form.nombre" type="text" class="mt-1" maxlength="255" required />
                <InputError :message="form.errors.nombre" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <Label for="cancha-precio" class="text-sm font-medium">Precio por Turno ($)</Label>
                    <Input id="cancha-precio" v-model="form.precio" type="number" min="0" step="0.01" class="mt-1" required />
                    <InputError :message="form.errors.precio" class="mt-1" />
                </div>
                <div>
                    <Label for="cancha-superficie" class="text-sm font-medium">Superficie (opcional)</Label>
                    <Input id="cancha-superficie" v-model="form.superficie" type="text" class="mt-1" maxlength="100" />
                    <InputError :message="form.errors.superficie" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <Label for="cancha-jugadores" class="text-sm font-medium">Cantidad de Jugadores</Label>
                    <Input id="cancha-jugadores" v-model="form.cantidad_jugadores" type="number" min="2" step="1" class="mt-1" required />
                    <InputError :message="form.errors.cantidad_jugadores" class="mt-1" />
                </div>
                <div>
                    <Label for="cancha-duracion" class="text-sm font-medium">Duración Turno (min)</Label>
                    <Input id="cancha-duracion" v-model="form.duracion_turno" type="number" step="1" min="30" class="mt-1" required />
                    <InputError :message="form.errors.duracion_turno" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <Label for="cancha-apertura" class="text-sm font-medium">Hora Apertura</Label>
                    <Input id="cancha-apertura" v-model="form.hora_apertura" type="time" class="mt-1" required />
                    <InputError :message="form.errors.hora_apertura" class="mt-1" />
                </div>
                <div>
                    <Label for="cancha-cierre" class="text-sm font-medium">Hora Cierre</Label>
                    <Input id="cancha-cierre" v-model="form.hora_cierre" type="time" class="mt-1" required />
                    <InputError :message="form.errors.hora_cierre" class="mt-1" />
                </div>
            </div>

            <div>
                <Label class="mb-2 block text-sm font-medium">Días Disponibles</Label>
                <div class="flex flex-wrap gap-2">
                    <label
                        v-for="dia in diasSemana"
                        :key="dia.id"
                        class="flex cursor-pointer items-center justify-center rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                        :class="
                            form.dias_disponibles.includes(Number(dia.id))
                                ? 'border-emerald-600 bg-emerald-500 text-primary-foreground'
                                : 'bg-background hover:bg-muted'
                        "
                    >
                        <input type="checkbox" :value="dia.id" v-model.number="form.dias_disponibles" class="sr-only" />
                        {{ dia.label }}
                    </label>
                </div>
                <InputError :message="form.errors.dias_disponibles" class="mt-1" />
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="es_techada" v-model="form.es_techada" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary" />
                <Label for="es_techada" class="cursor-pointer text-sm font-medium">¿Es cancha techada?</Label>
            </div>

            <div>
                <Label for="cancha-descripcion" class="text-sm font-medium">Descripción (opcional)</Label>
                <textarea
                    id="cancha-descripcion"
                    v-model="form.descripcion"
                    rows="2"
                    class="mt-1 flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <InputError :message="form.errors.descripcion" class="mt-1" />
            </div>

            <div class="flex justify-end gap-3 border-t pt-4">
                <Button type="button" variant="outline" @click="$emit('close')"> Cancelar </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ cancha ? 'Guardar Cambios' : 'Crear Cancha' }}
                </Button>
            </div>
        </form>
    </BaseModal>
</template>
