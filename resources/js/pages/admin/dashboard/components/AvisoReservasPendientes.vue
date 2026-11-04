<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowRight } from 'lucide-vue-next';

defineProps<{
    cantidad: number;
}>();
</script>

<template>
    <div
        v-if="cantidad > 0"
        class="flex flex-col gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/40 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex items-start gap-3">
            <AlertTriangle class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" />
            <div>
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                    {{ cantidad === 1 ? 'Tenés 1 reserva pendiente para hoy' : `Tenés ${cantidad} reservas pendientes para hoy` }}
                </p>
                <p class="mt-1 text-xs text-amber-800 dark:text-amber-200/80">Revisá la grilla para confirmarlas o gestionarlas.</p>
            </div>
        </div>
        <Button
            as-child
            size="sm"
            variant="outline"
            class="shrink-0 gap-1.5 border-amber-300 bg-white/60 text-amber-900 hover:bg-amber-100 dark:border-amber-800 dark:bg-transparent dark:text-amber-100 dark:hover:bg-amber-900/40"
        >
            <Link :href="route('admin.reservas.index')">
                Ver reservas
                <ArrowRight class="h-4 w-4" />
            </Link>
        </Button>
    </div>
</template>
