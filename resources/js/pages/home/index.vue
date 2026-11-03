<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Cancha } from '@/types';
import { Head } from '@inertiajs/vue3';
import CarruselCanchas from './components/CarruselCanchas.vue';
import ExperienciaSection from './components/ExperienciaSection.vue';
import FinalSection from './components/FinalSection.vue';
import HeroHeader from './components/HeroHeader.vue';
import PasosSection from './components/PasosSection.vue';

defineProps<{
    canchas: Cancha[];
}>();
</script>

<template>
    <Head title="PadelPoint" />

    <PublicLayout>
        <HeroHeader />

        <CarruselCanchas :canchas="canchas" />

        <PasosSection />

        <ExperienciaSection />

        <FinalSection />
    </PublicLayout>
</template>
