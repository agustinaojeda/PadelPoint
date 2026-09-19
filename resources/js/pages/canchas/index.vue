<script setup lang="ts">
import { ref } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem, Cancha } from '@/types'
import { Button } from '@/components/ui/button'

import CanchaCard from './components/CanchaCard.vue'
import CanchaFormModal from './components/CanchaFormModal.vue'

import { useScrollInfinito } from '@/composables/useScrollInfinito'
import { useFeedback } from '@/composables/useFeedback'

interface CanchasPaginadas {
    data: Cancha[];
    current_page: number;
    next_page_url: string | null;
}

const props = defineProps<{
    canchas: CanchasPaginadas;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Canchas', href: '/canchas' },
];

useFeedback()
const { items: listaCanchas, cargando, triggerScroll } = useScrollInfinito(
    () => props.canchas,
    'canchas'
)

// Gestión de Modales y Acciones
const showModal = ref(false)
const canchaSeleccionada = ref<Cancha | null>(null)

const abrirCrearModal = () => {
    canchaSeleccionada.value = null
    showModal.value = true
}

const abrirEditarModal = (cancha: Cancha) => {
    canchaSeleccionada.value = { ...cancha }
    showModal.value = true
}

const cerrarModal = () => {
    showModal.value = false
    canchaSeleccionada.value = null 
}

const desactivarCancha = (id: number) => {
    if (confirm(`¿Seguro que deseas desactivar esta cancha?\nSe eliminará completamente luego de 30 días.`)) {
        router.delete(`/canchas/${id}`, { preserveScroll: true })
    }
}

const activarCancha = (cancha: Cancha) => {
    router.patch(`/canchas/${cancha.id}/activar`, {}, { preserveScroll: true })
}
</script>

<template>
    <Head title="Canchas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Gestión de Canchas</h1>
                    <p class="text-muted-foreground mt-1">Administra las instalaciones del complejo.</p>
                </div>
                <Button @click="abrirCrearModal">+ Nueva Cancha</Button>
            </div>

            <template v-if="listaCanchas.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <CanchaCard
                        v-for="cancha in listaCanchas"
                        :key="cancha.id"
                        :cancha="cancha"
                        @edit="abrirEditarModal"
                        @delete="desactivarCancha"
                        @activar="activarCancha"
                    />
                </div>

                <div ref="triggerScroll" class="py-8 text-center">
                    <div v-if="cargando" class="text-sm text-muted-foreground flex items-center justify-center gap-2">
                        <span class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></span>
                        Cargando más canchas...
                    </div>
                    <p v-else-if="!canchas.next_page_url" class="text-sm text-muted-foreground">
                        No hay más canchas para mostrar.
                    </p>
                </div>
            </template>

            <div v-else class="text-center py-16 bg-muted/20 rounded-xl border border-dashed">
                <p class="text-muted-foreground">No hay canchas activas actualmente.</p>
                <Button variant="outline" class="mt-4" @click="abrirCrearModal">
                    Crear primera cancha
                </Button>
            </div>
        </div>

        <CanchaFormModal
            :open="showModal"
            :cancha="canchaSeleccionada"
            @close="cerrarModal"
        />
    </AppLayout>
</template>