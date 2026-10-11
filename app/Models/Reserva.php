<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'cancha_id',
        'fecha_turno',
        'hora_inicio',
        'hora_fin',
        'nombre_cliente',
        'telefono_cliente',
        'monto_total',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_turno' => 'date:Y-m-d',
        'monto_total' => 'float',
    ];

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class);
    }

    public function scopeConfirmadasOCompletadas($query)
    {
        return $query->whereIn('estado', ['confirmada', 'completada']);
    }

    public function scopeEnRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_turno', [$desde, $hasta]);
    }

    public static function tieneSolapamiento(int $canchaId, string $fecha, string $horaInicio, string $horaFin, ?int $ignorarReservaId = null): bool
    {
        return self::where('cancha_id', $canchaId)
            ->where('fecha_turno', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->when($ignorarReservaId, fn($q) => $q->where('id', '!=', $ignorarReservaId))
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->where('hora_inicio', '<', $horaFin)
                      ->where('hora_fin', '>', $horaInicio);
            })
            ->exists();
    }

    public static function marcarVencidasComoCompletadas(): void
    {
        $ahoraFecha = now()->toDateString();
        $ahoraHora = now()->toTimeString();

        self::where('estado', 'confirmada')
            ->where(function ($query) use ($ahoraFecha, $ahoraHora) {
                $query->where('fecha_turno', '<', $ahoraFecha)
                    ->orWhere(function ($q) use ($ahoraFecha, $ahoraHora) {
                        $q->where('fecha_turno', '=', $ahoraFecha)
                          ->where('hora_fin', '<=', $ahoraHora);
                    });
            })
            ->update(['estado' => 'completada']);
    }

    public static function obtenerDatosDashboard(): array
    {
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $metricasHoy = self::confirmadasOCompletadas()
            ->where('fecha_turno', $hoy)
            ->selectRaw('COUNT(*) as total_reservas, COALESCE(SUM(monto_total), 0) as total_ingresos')
            ->first();

        $metricasMes = self::confirmadasOCompletadas()
            ->enRangoFechas($inicioMes, $finMes)
            ->selectRaw('COUNT(*) as total_reservas, COALESCE(SUM(monto_total), 0) as total_ingresos')
            ->first();

        $totalReservasMes = (int) $metricasMes->total_reservas;

        //prox reservas activas del día
        $proximasReservas = self::with('cancha')
            ->where('fecha_turno', $hoy)
            ->confirmadasOCompletadas()
            ->orderBy('hora_inicio', 'asc')
            ->take(6)
            ->get();

        $canchasResumen = Cancha::where('esta_disponible', true)
            ->withCount(['reservas as reservas_mes' => fn($q) => $q->confirmadasOCompletadas()->enRangoFechas($inicioMes, $finMes)])
            ->withSum(['reservas as recaudado_mes' => fn($q) => $q->confirmadasOCompletadas()->enRangoFechas($inicioMes, $finMes)], 'monto_total')
            ->get()
            ->map(fn($cancha) => [
                'id' => $cancha->id,
                'nombre' => $cancha->nombre,
                'es_techada' => $cancha->es_techada,
                'reservas_mes' => $cancha->reservas_mes ?? 0,
                'recaudado_mes' => (float) ($cancha->recaudado_mes ?? 0),
            ]);

        return [
            'metrics' => [
                'ingresos_hoy' => (float) $metricasHoy->total_ingresos,
                'reservas_hoy' => (int) $metricasHoy->total_reservas,
                'ingresos_mes' => (float) $metricasMes->total_ingresos,
                'reservas_mes' => $totalReservasMes,
                'total_canchas' => Cancha::where('esta_disponible', true)->count(),
            ],
            'franjasHorarias' => self::obtenerFranjasHorarias($inicioMes, $finMes, $totalReservasMes),
            'ocupacionDias' => self::obtenerOcupacionPorDia($inicioMes, $finMes, $totalReservasMes),
            'proximasReservas' => $proximasReservas,
            'canchasResumen' => $canchasResumen,
        ];
    }

    /**
     * agrupa reservas por franja horaria 
     */
    private static function obtenerFranjasHorarias(string $inicioMes, string $finMes, int $totalReservasMes): array
    {
        $conteoFranjas = self::confirmadasOCompletadas()
            ->enRangoFechas($inicioMes, $finMes)
            ->selectRaw("
                SUM(CASE WHEN hora_inicio < '14:00:00' THEN 1 ELSE 0 END) as manana,
                SUM(CASE WHEN hora_inicio >= '14:00:00' AND hora_inicio < '18:00:00' THEN 1 ELSE 0 END) as tarde,
                SUM(CASE WHEN hora_inicio >= '18:00:00' THEN 1 ELSE 0 END) as noche
            ")
            ->first();

        $mananaCount = (int) ($conteoFranjas->manana ?? 0);
        $tardeCount  = (int) ($conteoFranjas->tarde ?? 0);
        $nocheCount  = (int) ($conteoFranjas->noche ?? 0);

        $calcularPorcentaje = fn($cantidad) => $totalReservasMes > 0
            ? round(($cantidad / $totalReservasMes) * 100)
            : 0;

        return [
            [
                'key' => 'manana',
                'label' => 'Mañana (08:00 - 14:00 hs)',
                'total' => $mananaCount,
                'porcentaje' => $calcularPorcentaje($mananaCount),
                'color' => '#f59e0b',
            ],
            [
                'key' => 'tarde',
                'label' => 'Tarde (14:00 - 18:00 hs)',
                'total' => $tardeCount,
                'porcentaje' => $calcularPorcentaje($tardeCount),
                'color' => '#3b82f6',
            ],
            [
                'key' => 'noche',
                'label' => 'Noche (18:00 - 00:00 hs)',
                'total' => $nocheCount,
                'porcentaje' => $calcularPorcentaje($nocheCount),
                'color' => '#10b981',
            ],
        ];
    }

    /**
     * calcula ocupacion por dia de la semana 
     */
    private static function obtenerOcupacionPorDia(string $inicioMes, string $finMes, int $totalReservasMes): array
    {
        $driver = DB::connection()->getDriverName();
        $diaSql = $driver === 'sqlite' ? "strftime('%w', fecha_turno)" : "DAYOFWEEK(fecha_turno)";

        $conteoPorDiaBD = self::confirmadasOCompletadas()
            ->enRangoFechas($inicioMes, $finMes)
            ->selectRaw("{$diaSql} as dia_sql, COUNT(*) as total")
            ->groupBy('dia_sql')
            ->pluck('total', 'dia_sql');

        $diasConfig = [
            ['id' => $driver === 'sqlite' ? '1' : 2, 'nombre' => 'Lunes'],
            ['id' => $driver === 'sqlite' ? '2' : 3, 'nombre' => 'Martes'],
            ['id' => $driver === 'sqlite' ? '3' : 4, 'nombre' => 'Miércoles'],
            ['id' => $driver === 'sqlite' ? '4' : 5, 'nombre' => 'Jueves'],
            ['id' => $driver === 'sqlite' ? '5' : 6, 'nombre' => 'Viernes'],
            ['id' => $driver === 'sqlite' ? '6' : 7, 'nombre' => 'Sábado'],
            ['id' => $driver === 'sqlite' ? '0' : 1, 'nombre' => 'Domingo'],
        ];

        $totalSegmentos = max($totalReservasMes, 1);

        return collect($diasConfig)->map(function ($dia) use ($conteoPorDiaBD, $totalSegmentos) {
            $cantidad = (int) $conteoPorDiaBD->get($dia['id'], 0);

            return [
                'dia' => $dia['nombre'],
                'total' => $cantidad,
                'porcentaje' => round(($cantidad / $totalSegmentos) * 100),
            ];
        })->values()->all();
    }
}