/**
 * Validaciones de formularios del frontend.
 *
 * Solo cubren reglas deterministas y baratas de evaluar en el navegador. Las
 * reglas que dependen del estado de la base de datos (unicidad, existencia,
 * solapamiento de turnos, disponibilidad, etc.) quedan exclusivamente en el
 * backend, que sigue siendo la autoridad final.
 */
import { fechaHoyIso } from '@/lib/fechas';

export interface ErrorValidacion<Campo extends string = string> {
    campo: Campo;
    mensaje: string;
}

export type CampoReservaPublica =
    | 'cancha_id'
    | 'fecha_turno'
    | 'hora_inicio'
    | 'nombre_cliente'
    | 'email_cliente'
    | 'telefono_cliente'
    | 'observaciones';

export type CampoReservaAdmin = CampoReservaPublica | 'hora_fin' | 'monto_total';

export type CampoCancha =
    | 'nombre'
    | 'superficie'
    | 'precio'
    | 'duracion_turno'
    | 'cantidad_jugadores'
    | 'hora_apertura'
    | 'hora_cierre'
    | 'dias_disponibles'
    | 'imagen';

export type CampoPassword = 'current_password' | 'password' | 'password_confirmation';

export interface DatosReservaPublica {
    cancha_id: number | null;
    fecha_turno: string;
    hora_inicio: string;
    nombre_cliente: string;
    email_cliente: string;
    telefono_cliente: string;
    observaciones: string;
}

export interface DatosReservaAdmin {
    cancha_id: number | null;
    fecha_turno: string;
    hora_inicio: string;
    hora_fin: string;
    nombre_cliente: string;
    email_cliente: string;
    telefono_cliente: string;
    monto_total: number | string;
    observaciones: string;
}

export interface DatosCancha {
    nombre: string;
    superficie: string;
    precio: number | string;
    duracion_turno: number | string;
    cantidad_jugadores: number | string;
    hora_apertura: string;
    hora_cierre: string;
    dias_disponibles: number[];
    imagen: File | null;
}

export interface DatosPassword {
    password: string;
    password_confirmation: string;
    current_password?: string;
}

const FORMATO_FECHA = /^\d{4}-\d{2}-\d{2}$/;
const FORMATO_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const TIPOS_IMAGEN = ['image/jpeg', 'image/png', 'image/webp'];
const TAMANIO_MAXIMO_IMAGEN = 2 * 1024 * 1024;

export function esEmailValido(email: string): boolean {
    return FORMATO_EMAIL.test(email.trim());
}

/**
 * Convierte una hora "HH:mm" (o "HH:mm:ss") a minutos desde la medianoche.
 * Devuelve null si el formato no es válido.
 */
export function horaAMinutos(hora: string): number | null {
    const partes = hora.trim().split(':');

    if (partes.length < 2) {
        return null;
    }

    const horas = Number(partes[0]);
    const minutos = Number(partes[1]);

    if (!Number.isInteger(horas) || !Number.isInteger(minutos) || horas < 0 || horas > 23 || minutos < 0 || minutos > 59) {
        return null;
    }

    return horas * 60 + minutos;
}

export function ahoraEnMinutos(ahora: Date = new Date()): number {
    return ahora.getHours() * 60 + ahora.getMinutes();
}

/**
 * Indica si un turno ya pasó. Funciona con la hora local del navegador; el
 * backend vuelve a validarlo con la zona horaria de la aplicación.
 */
export function turnoYaComenzo(fecha: string, horaInicio: string, ahora: Date = new Date()): boolean {
    const minutos = horaAMinutos(horaInicio);

    if (minutos === null) {
        return false;
    }

    const hoy = fechaHoyIso();

    if (fecha < hoy) {
        return true;
    }

    if (fecha > hoy) {
        return false;
    }

    return minutos <= ahoraEnMinutos(ahora);
}

function aNumero(valor: number | string | null | undefined): number | null {
    if (valor === null || valor === undefined || String(valor).trim() === '') {
        return null;
    }

    const numero = Number(valor);

    return Number.isFinite(numero) ? numero : null;
}

export function validarReservaPublica(datos: DatosReservaPublica): ErrorValidacion<CampoReservaPublica>[] {
    const errores: ErrorValidacion<CampoReservaPublica>[] = [];
    const hoy = fechaHoyIso();

    if (!datos.cancha_id) {
        errores.push({ campo: 'cancha_id', mensaje: 'Seleccioná una cancha.' });
    }

    if (!datos.fecha_turno.trim()) {
        errores.push({ campo: 'fecha_turno', mensaje: 'Seleccioná la fecha del turno.' });
    } else if (!FORMATO_FECHA.test(datos.fecha_turno)) {
        errores.push({ campo: 'fecha_turno', mensaje: 'La fecha del turno no es válida.' });
    } else if (datos.fecha_turno < hoy) {
        errores.push({ campo: 'fecha_turno', mensaje: 'No podés reservar una fecha pasada.' });
    }

    if (!datos.hora_inicio.trim()) {
        errores.push({ campo: 'hora_inicio', mensaje: 'Seleccioná un horario.' });
    } else if (horaAMinutos(datos.hora_inicio) === null) {
        errores.push({ campo: 'hora_inicio', mensaje: 'El horario seleccionado no es válido.' });
    } else if (datos.fecha_turno === hoy && turnoYaComenzo(datos.fecha_turno, datos.hora_inicio)) {
        errores.push({ campo: 'hora_inicio', mensaje: 'Ese horario ya pasó. Elegí un turno posterior.' });
    }

    const nombre = datos.nombre_cliente.trim();

    if (!nombre) {
        errores.push({ campo: 'nombre_cliente', mensaje: 'Ingresá el nombre del cliente.' });
    } else if (nombre.length < 3) {
        errores.push({ campo: 'nombre_cliente', mensaje: 'Ingresá tu nombre completo.' });
    } else if (nombre.length > 255) {
        errores.push({ campo: 'nombre_cliente', mensaje: 'El nombre no puede superar los 255 caracteres.' });
    }

    const email = datos.email_cliente.trim();

    if (!email) {
        errores.push({ campo: 'email_cliente', mensaje: 'Ingresá tu correo electrónico para confirmar la reserva.' });
    } else if (!esEmailValido(email)) {
        errores.push({ campo: 'email_cliente', mensaje: 'Ingresá un correo electrónico válido.' });
    } else if (email.length > 255) {
        errores.push({ campo: 'email_cliente', mensaje: 'El correo no puede superar los 255 caracteres.' });
    }

    const telefono = datos.telefono_cliente.trim();

    if (telefono && telefono.length < 6) {
        errores.push({ campo: 'telefono_cliente', mensaje: 'Ingresá un teléfono de contacto válido.' });
    } else if (telefono.length > 50) {
        errores.push({ campo: 'telefono_cliente', mensaje: 'El teléfono no puede superar los 50 caracteres.' });
    }

    if (datos.observaciones.length > 500) {
        errores.push({ campo: 'observaciones', mensaje: 'Las observaciones no pueden superar los 500 caracteres.' });
    }

    return errores;
}

export function validarReservaAdmin(datos: DatosReservaAdmin): ErrorValidacion<CampoReservaAdmin>[] {
    const errores: ErrorValidacion<CampoReservaAdmin>[] = [];

    if (!datos.cancha_id) {
        errores.push({ campo: 'cancha_id', mensaje: 'Seleccioná una cancha.' });
    }

    if (!datos.fecha_turno.trim()) {
        errores.push({ campo: 'fecha_turno', mensaje: 'Seleccioná la fecha del turno.' });
    } else if (!FORMATO_FECHA.test(datos.fecha_turno)) {
        errores.push({ campo: 'fecha_turno', mensaje: 'La fecha del turno no es válida.' });
    }

    const inicio = horaAMinutos(datos.hora_inicio);

    if (!datos.hora_inicio.trim()) {
        errores.push({ campo: 'hora_inicio', mensaje: 'Seleccioná un horario.' });
    } else if (inicio === null) {
        errores.push({ campo: 'hora_inicio', mensaje: 'El horario seleccionado no es válido.' });
    }

    const fin = horaAMinutos(datos.hora_fin);

    if (!datos.hora_fin.trim()) {
        errores.push({ campo: 'hora_fin', mensaje: 'Ingresá la hora de fin.' });
    } else if (fin === null) {
        errores.push({ campo: 'hora_fin', mensaje: 'La hora de fin no es válida.' });
    } else if (inicio !== null && fin <= inicio) {
        errores.push({ campo: 'hora_fin', mensaje: 'La hora de fin debe ser posterior a la hora de inicio.' });
    }

    const nombre = datos.nombre_cliente.trim();

    if (!nombre) {
        errores.push({ campo: 'nombre_cliente', mensaje: 'Ingresá el nombre del cliente.' });
    } else if (nombre.length > 255) {
        errores.push({ campo: 'nombre_cliente', mensaje: 'El nombre no puede superar los 255 caracteres.' });
    }

    const email = datos.email_cliente.trim();

    if (email && !esEmailValido(email)) {
        errores.push({ campo: 'email_cliente', mensaje: 'Ingresá un correo electrónico válido.' });
    } else if (email.length > 255) {
        errores.push({ campo: 'email_cliente', mensaje: 'El correo no puede superar los 255 caracteres.' });
    }

    if (datos.telefono_cliente.trim().length > 50) {
        errores.push({ campo: 'telefono_cliente', mensaje: 'El teléfono no puede superar los 50 caracteres.' });
    }

    const monto = aNumero(datos.monto_total);

    if (monto === null) {
        errores.push({ campo: 'monto_total', mensaje: 'El monto total es obligatorio.' });
    } else if (monto < 0) {
        errores.push({ campo: 'monto_total', mensaje: 'El monto total no puede ser negativo.' });
    }

    return errores;
}

export function validarCancha(datos: DatosCancha): ErrorValidacion<CampoCancha>[] {
    const errores: ErrorValidacion<CampoCancha>[] = [];

    const nombre = datos.nombre.trim();

    if (!nombre) {
        errores.push({ campo: 'nombre', mensaje: 'Ingresa un nombre para la cancha.' });
    } else if (nombre.length > 255) {
        errores.push({ campo: 'nombre', mensaje: 'El nombre no puede superar los 255 caracteres.' });
    }

    if (datos.superficie.trim().length > 100) {
        errores.push({ campo: 'superficie', mensaje: 'La superficie no puede superar los 100 caracteres.' });
    }

    const precio = aNumero(datos.precio);

    if (precio === null) {
        errores.push({ campo: 'precio', mensaje: 'Debes especificar el precio por turno.' });
    } else if (precio < 0) {
        errores.push({ campo: 'precio', mensaje: 'El precio no puede ser un número negativo.' });
    }

    const duracion = aNumero(datos.duracion_turno);

    if (duracion === null || !Number.isInteger(duracion)) {
        errores.push({ campo: 'duracion_turno', mensaje: 'Indica la duración de cada turno.' });
    } else if (duracion < 30) {
        errores.push({ campo: 'duracion_turno', mensaje: 'La duración del turno debe ser de al menos 30 minutos.' });
    }

    const jugadores = aNumero(datos.cantidad_jugadores);

    if (jugadores === null || !Number.isInteger(jugadores)) {
        errores.push({ campo: 'cantidad_jugadores', mensaje: 'Ingresa la cantidad de jugadores.' });
    } else if (jugadores < 2) {
        errores.push({ campo: 'cantidad_jugadores', mensaje: 'La cantidad mínima de jugadores es 2.' });
    }

    const apertura = horaAMinutos(datos.hora_apertura);

    if (!datos.hora_apertura.trim()) {
        errores.push({ campo: 'hora_apertura', mensaje: 'Ingresa el horario de apertura.' });
    } else if (apertura === null) {
        errores.push({ campo: 'hora_apertura', mensaje: 'La hora de apertura debe tener el formato HH:MM.' });
    }

    const cierre = horaAMinutos(datos.hora_cierre);

    if (!datos.hora_cierre.trim()) {
        errores.push({ campo: 'hora_cierre', mensaje: 'Ingresa el horario de cierre.' });
    } else if (cierre === null) {
        errores.push({ campo: 'hora_cierre', mensaje: 'La hora de cierre debe tener el formato HH:MM.' });
    } else if (apertura !== null && cierre <= apertura) {
        errores.push({ campo: 'hora_cierre', mensaje: 'La hora de cierre debe ser posterior a la hora de apertura.' });
    }

    if (!Array.isArray(datos.dias_disponibles) || datos.dias_disponibles.length === 0) {
        errores.push({ campo: 'dias_disponibles', mensaje: 'Selecciona al menos un día disponible.' });
    }

    if (datos.imagen instanceof File) {
        if (!TIPOS_IMAGEN.includes(datos.imagen.type)) {
            errores.push({ campo: 'imagen', mensaje: 'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.' });
        } else if (datos.imagen.size > TAMANIO_MAXIMO_IMAGEN) {
            errores.push({ campo: 'imagen', mensaje: 'La imagen es muy pesada. El tamaño máximo permitido es 2 MB.' });
        }
    }

    if (
        duracion !== null &&
        apertura !== null &&
        cierre !== null &&
        cierre > apertura &&
        duracion > cierre - apertura &&
        !errores.some((error) => error.campo === 'duracion_turno')
    ) {
        errores.push({ campo: 'duracion_turno', mensaje: 'La duración del turno no puede superar el horario de atención.' });
    }

    return errores;
}

export function validarPassword(datos: DatosPassword, opciones: { requiereActual: true }): ErrorValidacion<CampoPassword>[];
export function validarPassword(
    datos: DatosPassword,
    opciones?: { requiereActual?: false },
): ErrorValidacion<Exclude<CampoPassword, 'current_password'>>[];
export function validarPassword(datos: DatosPassword, opciones: { requiereActual?: boolean } = {}): ErrorValidacion<CampoPassword>[] {
    const errores: ErrorValidacion<CampoPassword>[] = [];

    if (opciones.requiereActual && !datos.current_password?.trim()) {
        errores.push({ campo: 'current_password', mensaje: 'Ingresá tu contraseña actual.' });
    }

    if (!datos.password) {
        errores.push({ campo: 'password', mensaje: 'Ingresá una contraseña.' });
    } else if (datos.password.length < 8) {
        errores.push({ campo: 'password', mensaje: 'La contraseña debe tener al menos 8 caracteres.' });
    }

    if (datos.password_confirmation !== datos.password) {
        errores.push({ campo: 'password_confirmation', mensaje: 'La confirmación de la contraseña no coincide.' });
    }

    return errores;
}
