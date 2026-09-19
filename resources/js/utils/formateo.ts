/**
 * formatea minutos a una representación legible de tiempo.
 */
export const formatDuracion = (minutos: number): string => {
    if (!minutos || minutos <= 0) return '0 min';

    const horas = Math.floor(minutos / 60);
    const minsRestantes = minutos % 60;

    const textoHoras = horas > 0 ? `${horas} h` : '';
    const textoMins = minsRestantes > 0 ? `${minsRestantes} min` : '';

    if (horas > 0 && minsRestantes > 0) {
        return `${textoHoras} y ${textoMins}`;
    }

    return textoHoras || textoMins;
};

/**
 * formateador de precios, agrega el $ y el . para miles , para decimales
 */
export const formatPrecio = (precio: number): string => {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        maximumFractionDigits: 0,
    }).format(precio);
};

/**pasa de string o array a array de números */
export const formatDiasDisponibles = (dias: any): number[] => {
    if (Array.isArray(dias)) return dias.map(Number)
    if (typeof dias === 'string') {
        try {
            const parsed = JSON.parse(dias)
            if (Array.isArray(parsed)) return parsed.map(Number)
        } catch {
            // Si viene como "1,2,3,4,5" en lugar de JSON
            return dias.split(',').map(n => parseInt(n.trim())).filter(n => !isNaN(n))
        }
    }
    return [1, 2, 3, 4, 5] // Valor por defecto
}

export const diasSemana = [
    { id: 1, label: 'Lun' },
    { id: 2, label: 'Mar' },
    { id: 3, label: 'Mié' },
    { id: 4, label: 'Jue' },
    { id: 5, label: 'Vie' },
    { id: 6, label: 'Sáb' },
    { id: 0, label: 'Dom' },
]