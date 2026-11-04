<script setup lang="ts">
interface Props {
    fotos?: string[];
}

withDefaults(defineProps<Props>(), {
    fotos: () => [
        '/images/galeria/foto-01.webp',
        '/images/galeria/foto-02.webp',
        '/images/galeria/foto-03.webp',
        '/images/galeria/foto-04.webp',
        '/images/galeria/foto-05.webp',
        '/images/galeria/foto-06.webp',
        '/images/galeria/foto-07.webp',
        '/images/galeria/foto-08.webp',
        '/images/galeria/foto-09.webp',
    ],
});

const getSpanClass = (index: number) => {
    if (index === 0 || index === 5) return 'col-span-2 row-span-2';
    return 'col-span-1 row-span-1';
};
</script>

<template>
    <section id="experiencia" class="w-full scroll-mt-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">
                    La experiencia <span class="relative inline-block underline decoration-lime-400 decoration-4 underline-offset-8">completa</span>
                </h2>
                <p class="mt-4 text-sm text-slate-500 md:text-base">
                    Creamos un espacio donde la pasión por el deporte y la vida social se cruzan. Descubrí nuestro ecosistema de canchas techadas de
                    cristal panorámico, áreas de descanso, servicios gastronómicos y estacionamiento privado para que solo te preocupes por jugar.
                </p>
            </div>

            <div class="grid auto-rows-[160px] grid-cols-2 gap-4 md:auto-rows-[200px] md:grid-cols-4 lg:grid-cols-5">
                <div
                    v-for="(foto, index) in fotos"
                    :key="index"
                    :class="[
                        getSpanClass(index),
                        'relative overflow-hidden rounded-2xl border border-slate-200/60 bg-slate-200 shadow-sm ',
                    ]"
                >
                    <img :src="foto" :alt="`Experiencia PadelPoint ${index + 1}`" class="h-full w-full object-cover" loading="lazy" />
                </div>
            </div>
        </div>
    </section>
</template>
