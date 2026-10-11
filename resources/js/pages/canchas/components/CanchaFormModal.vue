<script setup lang="ts">
import BaseModal from '@/components/ui/BaseModal.vue';
import { Button } from '@/components/ui/button';
import { useCanchaForm } from '@/composables/useCanchaForm';
import type { Cancha } from '@/types';
import { diasSemana } from '@/utils/formateo';

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
                <label class="mb-1 block text-sm font-medium">Imagen de la Cancha (opcional)</label>
                <div class="flex items-center gap-4">
                    <div class="relative flex h-24 w-24 shrink-0 items-center overflow-hidden rounded-lg border bg-muted">
                        <img v-if="imagePreview" :src="imagePreview" alt="Vista previa" class="h-full w-full object-cover" />
                        <span v-else class="px-1 text-center text-xs text-muted-foreground"> Imagen por defecto </span>
                    </div>

                    <div class="flex-1 space-y-2">
                        <input type="file" id="imagen" accept="image/*" class="hidden" @change="handleImageChange" />
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
                <span v-if="form.errors.imagen" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.imagen }}
                </span>
            </div>

            <div>
                <label class="text-sm font-medium">Nombre</label>
                <input
                    v-model="form.nombre"
                    type="text"
                    class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    required
                />
                <span v-if="form.errors.nombre" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.nombre }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Precio por Turno ($)</label>
                    <input
                        v-model="form.precio"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                        required
                    />
                </div>
                <div>
                    <label class="text-sm font-medium">Superficie (opcional)</label>
                    <input
                        v-model="form.superficie"
                        type="text"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Cantidad de Jugadores</label>
                    <input
                        v-model="form.cantidad_jugadores"
                        type="number"
                        min="1"
                        step="1"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                        required
                    />
                    <span v-if="form.errors.cantidad_jugadores" class="mt-1 block text-xs text-destructive">
                        {{ form.errors.cantidad_jugadores }}
                    </span>
                </div>
                <div>
                    <label class="text-sm font-medium">Duración Turno (min)</label>
                    <input
                        v-model="form.duracion_turno"
                        type="number"
                        step="1"
                        min="30"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                        required
                    />
                    <span v-if="form.errors.duracion_turno" class="mt-1 block text-xs text-destructive">
                        {{ form.errors.duracion_turno }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Hora Apertura</label>
                    <input
                        v-model="form.hora_apertura"
                        type="time"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                        required
                    />
                    <span v-if="form.errors.hora_apertura" class="mt-1 block text-xs text-destructive">
                        {{ form.errors.hora_apertura }}
                    </span>
                </div>
                <div>
                    <label class="text-sm font-medium">Hora Cierre</label>
                    <input
                        v-model="form.hora_cierre"
                        type="time"
                        class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                        required
                    />
                    <span v-if="form.errors.hora_cierre" class="mt-1 block text-xs text-destructive">
                        {{ form.errors.hora_cierre }}
                    </span>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Días Disponibles</label>
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
                <span v-if="form.errors.dias_disponibles" class="mt-1 block text-xs text-destructive">
                    {{ form.errors.dias_disponibles }}
                </span>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="es_techada" v-model="form.es_techada" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary" />
                <label for="es_techada" class="cursor-pointer text-sm font-medium"> ¿Es cancha techada? </label>
            </div>

            <div>
                <label class="text-sm font-medium">Descripción (opcional)</label>
                <textarea
                    v-model="form.descripcion"
                    rows="2"
                    class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm text-foreground"
                ></textarea>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end gap-3 border-t pt-4">
                <Button type="button" variant="outline" @click="$emit('close')"> Cancelar </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ cancha ? 'Guardar Cambios' : 'Crear Cancha' }}
                </Button>
            </div>
        </form>
    </BaseModal>
</template>
