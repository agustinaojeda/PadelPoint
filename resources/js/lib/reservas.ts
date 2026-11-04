import type { BadgeVariants } from '@/components/ui/badge';
import type { EstadoReserva } from '@/types';

export interface EstadoReservaMeta {
    valor: EstadoReserva;
    etiqueta: string;
    variant: NonNullable<BadgeVariants['variant']>;
}

export const ESTADOS_RESERVA: EstadoReservaMeta[] = [
    { valor: 'pendiente', etiqueta: 'Pendiente', variant: 'naranja' },
    { valor: 'confirmada', etiqueta: 'Confirmada', variant: 'verde' },
    { valor: 'completada', etiqueta: 'Completada', variant: 'gris' },
    { valor: 'cancelada', etiqueta: 'Cancelada', variant: 'rojo' },
];

export const metaEstadoReserva = (estado: EstadoReserva): EstadoReservaMeta =>
    ESTADOS_RESERVA.find((item) => item.valor === estado) ?? ESTADOS_RESERVA[0];
