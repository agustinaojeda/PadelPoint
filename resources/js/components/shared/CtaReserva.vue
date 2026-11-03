<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        etiqueta?: string;
        tamano?: 'sm' | 'lg';
    }>(),
    {
        etiqueta: 'Reservar cancha',
        tamano: 'lg',
    },
);
</script>

<template>
    <Button
        as-child
        class="h-auto rounded-xl bg-lime-400 font-bold text-slate-950 shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-lime-500"
        :class="tamano === 'lg' ? 'px-6 py-3 text-base' : 'px-5 py-2.5 text-sm'"
    >
        <Link :href="route('reservas.index')">{{ etiqueta }}</Link>
    </Button>
</template>
