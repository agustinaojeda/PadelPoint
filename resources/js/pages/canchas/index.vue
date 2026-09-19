<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Cancha } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

const busqueda = ref('');

const canchasFiltradas = computed(() => {
    if (!busqueda.value.trim()) return listaCanchas.value;
    const query = busqueda.value.toLowerCase().trim();
    return listaCanchas.value.filter(
        (cancha) =>
            cancha.nombre.toLowerCase().includes(query) ||
            (cancha.descripcion && cancha.descripcion.toLowerCase().includes(query))
    );
});

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
            <div class="mb-6 flex w-full flex-col items-start justify-between gap-4 text-left sm:flex-row sm:items-center">
                <div class="text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Gestión de Canchas</h1>
                    <p class="mt-1 text-muted-foreground">Administra las instalaciones del complejo.</p>
                </div>
                <Button @click="abrirCrearModal">+ Nueva Cancha</Button>
            </div>

            <div v-if="listaCanchas.length > 0" class="mb-8 max-w-md">
                <div class="relative">
                    <svg
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <Input
                        v-model="busqueda"
                        type="text"
                        placeholder="Buscar cancha por nombre..."
                        class="pl-9"
                    />
                </div>
            </div>

            <template v-if="canchasFiltradas.length > 0">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <CanchaCard
                        v-for="cancha in canchasFiltradas"
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

            <div v-else-if="listaCanchas.length > 0" class="rounded-xl border border-dashed bg-card px-4 py-12 text-center">
                <p class="text-muted-foreground">No se encontraron canchas que coincidan con "{{ busqueda }}".</p>
                <Button variant="ghost" class="mt-2" @click="busqueda = ''">Limpiar búsqueda</Button>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed bg-card px-4 py-16 text-center shadow-sm">
                <p class="text-muted-foreground">¡Aún no hay canchas!</p>
                <Button variant="outline" class="mt-4" @click="abrirCrearModal"> Crear primera cancha </Button>
            </div>
        </div>

        <CanchaFormModal :open="showModal" :cancha="canchaSeleccionada" @close="cerrarModal" />
    </AppLayout>
</template>