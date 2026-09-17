<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario Docente de prueba
        User::create([
            'name' => 'Profesor Carlos',
            'email' => 'profesor@kahoot.com',
            'password' => Hash::make('password123'),
            'role' => 'docente',
        ]);

        // Usuario Estudiante de prueba
        User::create([
            'name' => 'Estudiante Juan',
            'email' => 'estudiante@kahoot.com',
            'password' => Hash::make('password123'),
            'role' => 'estudiante',
        ]);
    }
}