import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: Admin;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
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
    nombreAdmin: string;
    correoAdmin: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

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