<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { metaEstadoReserva } from '@/lib/reservas';
import type { EstadoReserva } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    estado: EstadoReserva;
}>();

const meta = computed(() => metaEstadoReserva(props.estado));
</script>

<template>
    <Badge :variant="meta.variant" class="capitalize">{{ meta.etiqueta }}</Badge>
</template>
