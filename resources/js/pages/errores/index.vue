<script setup lang="ts">
import CtaReserva from '@/components/shared/CtaReserva.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, RefreshCw } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

interface DetalleError {
    titulo: string;
    descripcion: string;
}

const detalles: Record<number, DetalleError> = {
    404: {
        titulo: 'Página no encontrada',
        descripcion: 'La página que buscás no existe o cambió de dirección. Revisá el enlace o volvé al inicio para reservar tu turno.',
    },
    419: {
        titulo: 'Tu sesión expiró',
        descripcion: 'Pasó demasiado tiempo desde que abriste el formulario y la sesión venció. Recargá la página y volvé a intentarlo.',
    },
    500: {
        titulo: 'Algo salió mal',
        descripcion: 'Tuvimos un problema al procesar tu solicitud. Esperá unos minutos y volvé a intentarlo.',
    },
    503: {
        titulo: 'Estamos en mantenimiento',
        descripcion: 'PadelPoint está actualizándose. En breve volvemos a estar disponibles para que reserves tu cancha.',
    },
};

const detalle = computed<DetalleError>(
    () =>
        detalles[props.status] ?? {
            titulo: 'Ocurrió un error',
            descripcion: 'No pudimos completar tu solicitud. Volvé al inicio e intentá de nuevo.',
        },
);

const recargar = (): void => {
    window.location.reload();
};
</script>

<template>
    <Head :title="`${detalle.titulo} | PadelPoint`" />

    <PublicLayout>
        <section class="relative flex min-h-[80vh] items-center overflow-hidden bg-teal-950 pb-16 pt-28 text-white md:pt-32">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-lime-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 h-72 w-72 rounded-full bg-teal-500/30 blur-3xl"></div>

            <div class="relative mx-auto w-full max-w-3xl px-6 text-center md:px-12">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-lime-400">Error {{ status }}</p>

                <p class="mt-6 bg-gradient-to-b from-white to-teal-300/60 bg-clip-text text-8xl font-extrabold leading-none text-transparent">
                    {{ status }}
                </p>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight md:text-4xl">{{ detalle.titulo }}</h1>

                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-teal-100/80 md:text-base">
                    {{ detalle.descripcion }}
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <Button
                        v-if="status === 419"
                        type="button"
                        class="h-auto rounded-xl bg-lime-400 px-6 py-3 text-base font-bold text-slate-950 shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-lime-500"
                        @click="recargar"
                    >
                        <RefreshCw />
                        Recargar página
                    </Button>

                    <CtaReserva v-else />

                    <Button
                        as-child
                        variant="outline"
                        class="h-auto rounded-xl border-white/30 bg-transparent px-6 py-3 text-base font-semibold text-white hover:bg-white/10 hover:text-white"
                    >
                        <Link :href="route('home')">
                            <ArrowLeft />
                            Volver al inicio
                        </Link>
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
