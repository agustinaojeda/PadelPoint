/**
 * Utilidades de fecha para reservas. Trabajan siempre en hora local para evitar
 * el corrimiento de un día que produce `toISOString()` en zonas como Argentina.
 */
export function formatearIso(fecha: Date): string {
    const anio = fecha.getFullYear();
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    const dia = fecha.getDate().toString().padStart(2, '0');

    return `${anio}-${mes}-${dia}`;
}

export function fechaHoyIso(): string {
    return formatearIso(new Date());
}

export function sumarDias(fecha: string, dias: number): string {
    const [anio, mes, dia] = fecha.split('-').map(Number);
    const fechaObj = new Date(anio, mes - 1, dia);
    fechaObj.setDate(fechaObj.getDate() + dias);

    return formatearIso(fechaObj);
}

export function formatearFechaLarga(fecha: string): string {
    const [anio, mes, dia] = fecha.split('-').map(Number);

    return new Intl.DateTimeFormat('es-AR', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date(anio, mes - 1, dia));
}
