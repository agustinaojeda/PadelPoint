import type { Component } from 'vue';

export interface Auth {
    user: User | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: Component;
    isActive?: boolean;
}

export interface SharedData extends Record<string, unknown> {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name?: string;
    nombreAdmin: string;
    correoAdmin: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

//canchas
export interface Cancha{
    id: number;
    nombre: string;
    superficie: string | null;
    es_techada: boolean;
    esta_disponible: boolean;
    precio: number;
    imagen_url: string | null;
    descripcion: string | null;
    duracion_turno: number;
    cantidad_jugadores: number;
    hora_apertura: string;
    hora_cierre: string;
    dias_disponibles: number[];
}

export interface FormCanchaData {
    [key: string]: any 
    nombre: string
    superficie: string
    es_techada: boolean
    esta_disponible: boolean;
    precio: number
    descripcion: string
    duracion_turno: number
    cantidad_jugadores: number
    hora_apertura: string
    hora_cierre: string
    dias_disponibles: number[]
    imagen: File | null
    _method?: string
}

//reservas
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

export interface SlotTurno {
    hora_inicio: string;
    hora_fin: string;
    disponible: boolean;
    reserva?: Reserva;
}