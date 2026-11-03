import type { BadgeVariants } from '@/components/ui/badge';
import type { EstadoReserva } from '@/types';

export interface EstadoReservaMeta {
    valor: EstadoReserva;
    etiqueta: string;
    variant: NonNullable<BadgeVariants['variant']>;
}

/**
 * Fuente única de verdad para etiquetas y variantes visuales de los estados.
 */
export const ESTADOS_RESERVA: EstadoReservaMeta[] = [
    { valor: 'pendiente', etiqueta: 'Pendiente', variant: 'outline' },
    { valor: 'confirmada', etiqueta: 'Confirmada', variant: 'default' },
    { valor: 'completada', etiqueta: 'Completada', variant: 'secondary' },
    { valor: 'cancelada', etiqueta: 'Cancelada', variant: 'destructive' },
];

export const metaEstadoReserva = (estado: EstadoReserva): EstadoReservaMeta =>
    ESTADOS_RESERVA.find((item) => item.valor === estado) ?? ESTADOS_RESERVA[0];
