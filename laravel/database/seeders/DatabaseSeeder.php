<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            [
                'email' => 'autor@neves.com'
            ],
            [
                'name' => 'Autor das Neves',
                'password' => Hash::make('12345678')
            ]
        );

        User::firstOrCreate(
            [
                'email' => 'fulana@couves.com'
            ],
            [
                'name' => 'Fulana das Couves',
                'password' => Hash::make('87654321')
            ]
        );
    }
}
