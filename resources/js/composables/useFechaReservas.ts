import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import { fechaHoyIso, sumarDias } from '@/lib/fechas';

/**
 * Mantiene sincronizada la fecha seleccionada con la URL y el prop de Inertia.
 */
export function useFechaReservas(fechaFuente: () => string, ruta: string) {
    const fecha = ref(fechaFuente());

    watch(fechaFuente, (nueva) => {
        if (nueva) {
            fecha.value = nueva;
        }
    });

    const cambiarFecha = (nuevaFecha: string) => {
        if (!nuevaFecha || nuevaFecha === fecha.value) {
            return;
        }

        fecha.value = nuevaFecha;

        router.get(ruta, { fecha: nuevaFecha }, { preserveState: true, preserveScroll: true, replace: true });
    };

    const navegarDia = (dias: number) => cambiarFecha(sumarDias(fecha.value, dias));

    const irAHoy = () => cambiarFecha(fechaHoyIso());

    return {
        fecha,
        cambiarFecha,
        navegarDia,
        irAHoy,
    };
}
