<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    titulo: string;
    descripcion?: string;
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center', props.class)">
        <slot name="icon" />

        <h3 class="mt-4 text-lg font-semibold">{{ titulo }}</h3>
        <p v-if="descripcion" class="mt-1 text-sm text-muted-foreground">{{ descripcion }}</p>

        <slot />
    </div>
</template>
