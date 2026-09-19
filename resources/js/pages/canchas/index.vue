<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Cancha } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import CanchaCard from './components/CanchaCard.vue';
import CanchaFormModal from './components/CanchaFormModal.vue';

import { useFeedback } from '@/composables/useFeedback';
import { useScrollInfinito } from '@/composables/useScrollInfinito';

interface CanchasPaginadas {
    data: Cancha[];
    current_page: number;
    next_page_url: string | null;
}

const props = defineProps<{
    canchas: CanchasPaginadas;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Canchas', href: '/canchas' }];

useFeedback();
const { items: listaCanchas, cargando, triggerScroll } = useScrollInfinito(() => props.canchas, 'canchas');

const showModal = ref(false);
const canchaSeleccionada = ref<Cancha | null>(null);

const abrirCrearModal = () => {
    canchaSeleccionada.value = null;
    showModal.value = true;
};

const abrirEditarModal = (cancha: Cancha) => {
    canchaSeleccionada.value = { ...cancha };
    showModal.value = true;
};

const cerrarModal = () => {
    showModal.value = false;
    canchaSeleccionada.value = null;
};

const desactivarCancha = (id: number) => {
    if (confirm(`¿Seguro que deseas desactivar esta cancha?\nSe eliminará completamente luego de 30 días.`)) {
        router.delete(`/canchas/${id}`, { preserveScroll: true });
    }
};

const activarCancha = (cancha: Cancha) => {
    router.patch(`/canchas/${cancha.id}/activar`, {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Canchas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex w-full flex-col items-start justify-between gap-4 text-left sm:flex-row sm:items-center">
                <div class="text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Gestión de Canchas</h1>
                    <p class="mt-1 text-muted-foreground">Administra las instalaciones del complejo.</p>
                </div>
                <Button @click="abrirCrearModal">+ Nueva Cancha</Button>
            </div>

            <template v-if="listaCanchas.length > 0">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
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
                    <div v-if="cargando" class="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                        <span class="h-4 w-4 animate-spin rounded-full border-b-2 border-primary"></span>
                        Cargando más canchas...
                    </div>
                    <p v-else-if="!canchas.next_page_url" class="text-sm text-muted-foreground">No hay más canchas para mostrar.</p>
                </div>
            </template>

            <div v-else class="rounded-xl border border-dashed bg-muted/20 py-16 text-center">
                <p class="text-muted-foreground">No hay canchas activas actualmente.</p>
                <Button variant="outline" class="mt-4" @click="abrirCrearModal"> Crear primera cancha </Button>
            </div>
        </div>

        <CanchaFormModal :open="showModal" :cancha="canchaSeleccionada" @close="cerrarModal" />
    </AppLayout>
</template>
