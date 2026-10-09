import type { Cancha, Reserva, SlotTurno } from '@/types';

function horaAMinutos(horaStr: string): number {
    const [h, m] = horaStr.split(':').map(Number);
    return h * 60 + m;
}

function minutosAHora(minutos: number): string {
    const h = Math.floor(minutos / 60).toString().padStart(2, '0');
    const m = (minutos % 60).toString().padStart(2, '0');
    return `${h}:${m}`;
}

export function generarSlotsCancha(cancha: Cancha, reservasDelDia: Reserva[]): SlotTurno[] {
    const slots: SlotTurno[] = [];

    const inicioMin = horaAMinutos(cancha.hora_apertura);
    const finMin = horaAMinutos(cancha.hora_cierre);
    const duracion = cancha.duracion_turno;

    const reservasCancha = reservasDelDia.filter(
        (r) => r.cancha_id === cancha.id && r.estado !== 'cancelada'
    );

    let actual = inicioMin;

    while (actual + duracion <= finMin) {
        const horaInicioStr = minutosAHora(actual);
        const horaFinStr = minutosAHora(actual + duracion);

        const reservaEncontrada = reservasCancha.find(
            (r) => r.hora_inicio.substring(0, 5) === horaInicioStr
        );

        slots.push({
            hora_inicio: horaInicioStr,
            hora_fin: horaFinStr,
            disponible: !reservaEncontrada,
            reserva: reservaEncontrada,
        });

        actual += duracion;
    }

    return slots;
}