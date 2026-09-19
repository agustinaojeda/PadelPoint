<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->updateOrInsert(
            ['correoAdmin' => 'admin@padelpoint.com'], 
            [
                'nombreAdmin'     => 'Administrador PadelPoint',
                'contrasenaAdmin' => Hash::make('admin123'), 
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        );
    }
}