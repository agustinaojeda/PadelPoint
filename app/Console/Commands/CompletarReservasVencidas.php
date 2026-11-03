<?php

namespace App\Console\Commands;

use App\Reserva\Actions\CompleteExpiredReservas;
use Illuminate\Console\Command;

class CompletarReservasVencidas extends Command
{
    protected $signature = 'app:completar-reservas-vencidas';

    protected $description = 'Marca como completadas las reservas confirmadas cuyo horario ya terminó';

    public function __construct(private CompleteExpiredReservas $completeExpiredReservas)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $cantidad = ($this->completeExpiredReservas)();

        $this->info("Se completaron {$cantidad} reservas vencidas.");

        return Command::SUCCESS;
    }
}
