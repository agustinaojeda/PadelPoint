<?php

namespace App\Console\Commands;

use App\Actions\Cancha\DeleteCancha;
use App\Models\Cancha;
use Illuminate\Console\Command;

class PurgeDesactivadasCanchas extends Command
{
    protected $signature = 'app:purge-desactivadas-canchas';

    protected $description = 'Elimina las canchas desactivadas hace más de 30 días que no tienen reservas';

    public function __construct(private DeleteCancha $deleteCancha)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $canchas = Cancha::query()
            ->where('esta_disponible', false)
            ->whereNotNull('desactivada_en')
            ->where('desactivada_en', '<=', now()->subDays(30))
            ->doesntHave('reservas')
            ->get();

        if ($canchas->isEmpty()) {
            $this->info('No hay canchas para eliminar.');

            return Command::SUCCESS;
        }

        $canchas->each(fn (Cancha $cancha) => ($this->deleteCancha)($cancha));

        $this->info("Se eliminaron definitivamente {$canchas->count()} canchas con sus respectivas imágenes.");

        return Command::SUCCESS;
    }
}
