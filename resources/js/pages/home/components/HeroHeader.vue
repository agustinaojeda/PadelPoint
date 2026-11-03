<script setup lang="ts">
import CtaReserva from '@/components/shared/CtaReserva.vue';

interface Props {
    videoSrc?: string;
    posterSrc?: string;
}

withDefaults(defineProps<Props>(), {
    videoSrc: 'https://cdn.every-day.nl/XNRGY%20Headervideo%20Edit.mp4', //'/videos/hero-padel.webm',
    posterSrc: '/images/hero-poster.webp',
});
</script>
<template>
    <header class="relative flex h-[95vh] w-full items-center overflow-hidden">
        <video autoplay loop muted playsinline :poster="posterSrc" class="absolute inset-0 z-0 h-full w-full object-cover">
            <source :src="videoSrc" type="video/mp4" />
        </video>

        <div class="absolute inset-0 z-10 bg-black/50"></div>

        <div class="container relative z-20 mx-auto max-w-5xl px-6 text-left text-white md:px-12 lg:px-16">
            <h1 class="mb-6 text-3xl font-extrabold leading-tight tracking-tight md:text-4xl lg:text-5xl">
                Elegí. <br class="hidden sm:block" />
                Reservá. <br class="hidden sm:block" />
                Jugá.
            </h1>

            <p class="mb-8 max-w-2xl pt-5 text-lg font-light leading-relaxed text-gray-200 md:text-xl">
                Reservá tu cancha en segundos y asegurá tu turno online.
            </p>

            <div>
                <CtaReserva />
            </div>
        </div>
    </header>
</template>
