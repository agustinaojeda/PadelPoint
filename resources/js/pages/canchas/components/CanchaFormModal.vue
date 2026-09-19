<script setup lang="ts">
import type { Cancha } from '@/types'
import { Button } from '@/components/ui/button'
import { diasSemana } from '@/utils/formateo'
import { useCanchaForm } from '@/composables/useCanchaForm'

const props = defineProps<{
    open: boolean
    cancha?: Cancha | null
}>()

const emit = defineEmits<{
    (e: 'close'): void
}>()

const {
    form,
    imagePreview,
    handleImageChange,
    clearSelectedImage,
    submit
} = useCanchaForm(props, emit)
</script>

<template>
    <div v-if="open" @click.self="$emit('close')" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-background rounded-xl shadow-xl max-w-lg w-full p-6 border max-h-[90vh] overflow-y-auto cursor-default">
            <h2 class="text-xl font-bold mb-4">
                {{ cancha ? 'Editar Cancha' : 'Registrar Nueva Cancha' }}
            </h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="text-sm font-medium block mb-1">Imagen de la Cancha (opcional)</label>
                    <div class="flex items-center gap-4">
                        <div class="relative w-24 h-24 rounded-lg overflow-hidden border bg-muted flex items-center shrink-0">
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Vista previa"
                                class="w-full h-full object-cover"
                            />
                            <span v-else class="text-xs text-muted-foreground text-center px-1">
                                Imagen por defecto
                            </span>
                        </div>

                        <div class="flex-1 space-y-2">
                            <input
                                type="file"
                                id="imagen"
                                accept="image/*"
                                class="hidden"
                                @change="handleImageChange"
                            />
                            <label
                                for="imagen"
                                class="inline-flex items-center justify-center px-3 py-2 border rounded-md text-xs font-medium cursor-pointer bg-background hover:bg-muted transition-colors border-input"
                            >
                                {{ imagePreview ? 'Cambiar Imagen' : 'Subir Imagen' }}
                            </label>
                            <p v-if="!imagePreview" class="text-[11px] text-muted-foreground">
                                Si no seleccionas ninguna, se asignará una imagen por defecto.
                            </p>
                            <button
                                v-if="form.imagen"
                                type="button"
                                @click="clearSelectedImage"
                                class="text-xs text-destructive hover:underline block"
                            >
                                Quitar selección
                            </button>
                        </div>
                    </div>
                    <span v-if="form.errors.imagen" class="text-xs text-destructive mt-1 block">{{ form.errors.imagen }}</span>
                </div>

                <div>
                    <label class="text-sm font-medium">Nombre</label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                        required
                    />
                    <span v-if="form.errors.nombre" class="text-xs text-destructive mt-1 block">{{ form.errors.nombre }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium">Precio por Turno ($)</label>
                        <input
                            v-model="form.precio"
                            type="number"
                            step="0.01"
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium">Superficie (opcional)</label>
                        <input
                            v-model="form.superficie"
                            type="text"
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
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
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                            required
                        />
                        <span v-if="form.errors.cantidad_jugadores" class="text-xs text-destructive mt-1 block">{{ form.errors.cantidad_jugadores }}</span>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Duración Turno (min)</label>
                        <input
                            v-model="form.duracion_turno"
                            type="number"
                            step="1"
                            min="30"
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                            required
                        />
                        <span v-if="form.errors.duracion_turno" class="text-xs text-destructive mt-1 block">{{ form.errors.duracion_turno }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium">Hora Apertura</label>
                        <input
                            v-model="form.hora_apertura"
                            type="time"
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                            required
                        />
                        <span v-if="form.errors.hora_apertura" class="text-xs text-destructive mt-1 block">{{ form.errors.hora_apertura }}</span>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Hora Cierre</label>
                        <input
                            v-model="form.hora_cierre"
                            type="time"
                            class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                            required
                        />
                        <span v-if="form.errors.hora_cierre" class="text-xs text-destructive mt-1 block">{{ form.errors.hora_cierre }}</span>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium block mb-2">Días Disponibles</label>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="dia in diasSemana"
                            :key="dia.id"
                            class="flex items-center justify-center px-3 py-1.5 border rounded-lg text-xs font-medium cursor-pointer transition-colors"
                            :class="form.dias_disponibles.includes(dia.id) ? 'bg-emerald-500 text-primary-foreground border-emerald-600' : 'bg-background hover:bg-muted'"
                        >
                            <input
                                type="checkbox"
                                :value="dia.id"
                                v-model="form.dias_disponibles"
                                class="sr-only"
                            />
                            {{ dia.label }}
                        </label>
                    </div>
                    <span v-if="form.errors.dias_disponibles" class="text-xs text-destructive mt-1 block">{{ form.errors.dias_disponibles }}</span>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input
                        id="es_techada"
                        v-model="form.es_techada"
                        type="checkbox"
                        class="rounded border-gray-300 text-primary h-4 w-4"
                    />
                    <label for="es_techada" class="text-sm font-medium cursor-pointer">¿Es cancha techada?</label>
                </div>

                <div>
                    <label class="text-sm font-medium">Descripción (opcional)</label>
                    <textarea
                        v-model="form.descripcion"
                        rows="2"
                        class="w-full mt-1 px-3 py-2 border rounded-md bg-background text-foreground text-sm"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <Button type="button" variant="outline" @click="$emit('close')">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ cancha ? 'Guardar Cambios' : 'Crear Cancha' }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>