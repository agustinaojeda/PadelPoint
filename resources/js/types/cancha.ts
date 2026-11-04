import type { FormDataConvertible } from '@inertiajs/core';

export interface Cancha {
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

export type FormCanchaData = {
    nombre: string;
    superficie: string;
    es_techada: boolean;
    esta_disponible: boolean;
    precio: number;
    descripcion: string;
    duracion_turno: number;
    cantidad_jugadores: number;
    hora_apertura: string;
    hora_cierre: string;
    dias_disponibles: number[];
    imagen: File | null;
    _method?: string;
} & Record<string, FormDataConvertible>;
