<?php

namespace App\Console\Commands;

use App\Cancha\Actions\DeleteCancha;
use App\Cancha\Models\Cancha;
use App\Cancha\Queries\GetCanchasParaPurgar;
use Illuminate\Console\Command;

class PurgeDesactivadasCanchas extends Command
{
    protected $signature = 'app:purge-desactivadas-canchas';

    protected $description = 'Elimina las canchas desactivadas hace más de 30 días que no tienen reservas';

    public function __construct(private DeleteCancha $deleteCancha)
    {
        parent::__construct();
    }

    public function handle(GetCanchasParaPurgar $getCanchasParaPurgar): int
    {
        $canchas = $getCanchasParaPurgar();

        if ($canchas->isEmpty()) {
            $this->info('No hay canchas para eliminar.');

            return Command::SUCCESS;
        }

        $canchas->each(fn (Cancha $cancha) => ($this->deleteCancha)($cancha));

        $this->info("Se eliminaron definitivamente {$canchas->count()} canchas con sus respectivas imágenes.");

        return Command::SUCCESS;
    }
}
