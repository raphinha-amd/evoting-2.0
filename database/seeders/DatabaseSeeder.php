<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin KPUM',
                'slug' => 'admin',
                'has_class' => false,
                'can_vote' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Mahasiswa',
                'slug' => 'mahasiswa',
                'has_class' => true,
                'can_vote' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Dosen',
                'slug' => 'dosen',
                'has_class' => false,
                'can_vote' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Staf',
                'slug' => 'staf',
                'has_class' => false,
                'can_vote' => false,
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        User::firstOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'fullname' => 'Administrator KPUM',
                'password' => bcrypt('admin'),
                'unencrypted_password' => 'admin',
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
