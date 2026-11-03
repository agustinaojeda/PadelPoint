<script setup lang="ts">
import CtaReserva from '@/components/shared/CtaReserva.vue';

interface Props {
    imagenSrc?: string;
}

withDefaults(defineProps<Props>(), {
    imagenSrc: '/images/galeria/foto-01.webp',
});
</script>
<template>
    <section class="relative mt-16 flex h-[60vh] w-full items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0 h-full w-full bg-cover bg-fixed bg-center" :style="{ backgroundImage: `url(${imagenSrc})` }"></div>

        <div class="absolute inset-0 z-10 bg-black/50"></div>

        <div
            class="container relative z-20 mx-auto flex max-w-5xl flex-col items-center justify-center px-6 text-center text-white md:px-12 lg:px-16"
        >
            <h1 class="mb-6 text-3xl font-extrabold leading-tight tracking-tight md:text-4xl lg:text-5xl">¿Listo para jugar?</h1>

            <p class="mx-auto mb-8 max-w-2xl pt-5 text-lg font-light leading-relaxed text-gray-200 md:text-xl">
                Reservá tu cancha en segundos y asegurá tu turno online.
            </p>

            <div>
                <CtaReserva />
            </div>
        </div>
    </section>
</template>
