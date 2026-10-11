import type { Cancha, Reserva, SlotTurno } from '@/types';

/**
 * convierte hora a minutos 
 */
function horaAMinutos(horaStr: string): number {
    const [h, m] = horaStr.split(':').map(Number);
    return h * 60 + m;
}

/**
 * convierte minutos a formato de hora
 */
function minutosAHora(minutos: number): string {
    const h = Math.floor(minutos / 60).toString().padStart(2, '0');
    const m = (minutos % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
}

/**
 * genera la grilla de slots de una cancha 
 */
export function generarSlotsCancha(cancha: Cancha, reservasDelDia: Reserva[]): SlotTurno[] {
    const slots: SlotTurno[] = [];

    const inicioMin = horaAMinutos(cancha.hora_apertura);
    const finMin = horaAMinutos(cancha.hora_cierre);
    const duracion = cancha.duracion_turno;

    if (duracion <= 0 || inicioMin >= finMin) {
        return slots;
    }

    const reservasProcesadas = reservasDelDia
        .filter((r) => r.cancha_id === cancha.id && r.estado !== 'cancelada')
        .map((r) => ({
            reserva: r,
            inicioMin: horaAMinutos(r.hora_inicio),
            finMin: horaAMinutos(r.hora_fin),
        }));

    let actualMin = inicioMin;

    while (actualMin + duracion <= finMin) {
        const slotInicioMin = actualMin;
        const slotFinMin = actualMin + duracion;

        const coincidencia = reservasProcesadas.find(
            (r) => slotInicioMin < r.finMin && slotFinMin > r.inicioMin
        );

        slots.push({
            hora_inicio: minutosAHora(slotInicioMin),
            hora_fin: minutosAHora(slotFinMin),
            disponible: !coincidencia,
            reserva: coincidencia ? coincidencia.reserva : undefined,
        });

        actualMin += duracion;
    }

    return slots;
}