import type { Reserva } from './reserva';

export interface MetricProps {
    ingresos_hoy: number;
    reservas_hoy: number;
    ingresos_mes: number;
    reservas_mes: number;
    total_canchas: number;
}

export interface FranjaHoraria {
    key: string;
    label: string;
    total: number;
    porcentaje: number;
    color: string;
}

export interface OcupacionDia {
    dia: string;
    total: number;
    porcentaje: number;
}

export interface CanchaResumen {
    id: number;
    nombre: string;
    es_techada: boolean;
    reservas_mes: number;
    recaudado_mes: number;
}

export interface BalanceAnual {
    mes: string;
    num_mes: number;
    total: number;
}

export interface DashboardProps {
    metrics: MetricProps;
    franjasHorarias: FranjaHoraria[];
    ocupacionDias: OcupacionDia[];
    proximasReservas: Reserva[];
    canchasResumen: CanchaResumen[];
    balanceAnual: BalanceAnual[];
}
