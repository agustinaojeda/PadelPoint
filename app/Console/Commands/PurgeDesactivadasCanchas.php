<?php

namespace App\Console\Commands;

use App\Models\Cancha;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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
        $canchasAEliminar = Cancha::where('esta_disponible', false)
            ->whereNotNull('desactivada_en')
            ->where('desactivada_en', '<=', now()->subDays(30))
            ->get();

        if ($canchasAEliminar->isEmpty()) {
            $this->info("No hay canchas para eliminar.");
            return Command::SUCCESS;
        }

        $contador = 0;

        foreach ($canchasAEliminar as $cancha) { //primero borra la imagen de la cancha si es que tiene y despues la cancha de la bd
            if ($cancha->imagen_url && $cancha->imagen_url !== 'images/cancha-default.jpg') {
                $path = str_replace('storage/', '', $cancha->imagen_url);

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $cancha->delete();
            $contador++;
        }

        $this->info("Se eliminaron definitivamente {$contador} canchas con sus respectivas imágenes.");

        return Command::SUCCESS;
    }
}
