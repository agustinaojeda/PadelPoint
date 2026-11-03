import type { Cancha } from './cancha';

export type EstadoReserva = 'pendiente' | 'confirmada' | 'cancelada' | 'completada';

export interface Reserva {
    id: number;
    cancha_id: number;
    fecha_turno: string;
    hora_inicio: string;
    hora_fin: string;
    nombre_cliente: string;
    telefono_cliente: string;
    monto_total: number;
    estado: EstadoReserva;
    observaciones: string | null;
    created_at?: string;
    updated_at?: string;
    cancha?: Cancha;
}

export interface SlotTurno {
    hora_inicio: string;
    hora_fin: string;
    disponible: boolean;
    reserva?: Reserva;
}

export interface FormReservaData {
    cancha_id: number | null;
    fecha_turno: string;
    hora_inicio: string;
    hora_fin: string;
    nombre_cliente: string;
    telefono_cliente: string;
    monto_total: number;
    observaciones?: string;
}

export interface SlotPublico {
    hora_inicio: string;
    hora_fin: string;
    disponible: boolean;
}

export interface CanchaConSlots extends Cancha {
    slots: SlotPublico[];
}

export interface ReservaConfirmada {
    cancha: string;
    fecha: string;
    hora_inicio: string;
    hora_fin: string;
}
