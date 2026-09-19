<?php

namespace App\Console\Commands;

use App\Models\Cancha;
use Illuminate\Console\Command;

class PurgeDesactivadasCanchas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-desactivadas-canchas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina de la base de datos las canchas desactivadas hace más de 30 días';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $afectadas = Cancha::where('esta_disponible', false)
            ->whereNotNull('desactivada_en')
            ->where('desactivada_en', '<=', now()->subDays(30))
            ->delete(); // Elimina físicamente la fila de la BD

        $this->info("Se eliminaron definitivamente {$afectadas} canchas.");

        return Command::SUCCESS;
    }
}
