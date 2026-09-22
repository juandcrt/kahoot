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

        // Usuario Estudiante de prueba 1
        User::create([
            'name' => 'Estudiante Juan',
            'email' => 'estudiante@kahoot.com',
            'password' => Hash::make('password123'),
            'role' => 'estudiante',
        ]);

        // Usuario Estudiante de prueba 2
        User::create([
            'name' => 'Estudiante Edu',
            'email' => 'estudiantee@kahoot.com',
            'password' => Hash::make('contraseña123'),
            'role' => 'estudiante',
        ]);

        // Usuario Estudiante adicional (Al azar)
        User::create([
            'name' => 'Estudiante Sofía',
            'email' => 'sofia@kahoot.com',
            'password' => Hash::make('password1234'),
            'role' => 'estudiante',
        ]);
    }
}