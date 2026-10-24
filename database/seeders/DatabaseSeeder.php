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
        // 1. Seed Roles
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

        // 2. Seed Prodis (Bina Sarana Informatika Kampus Kota Sukabumi)
        $prodis = [
            [
                'name' => 'Sistem Informasi (S1)',
                'slug' => 'sistem-informasi-s1',
                'code' => 'SI-S1',
                'degree' => 'S1',
                'is_active' => true,
            ],
            [
                'name' => 'Sistem Informasi (D3)',
                'slug' => 'sistem-informasi-d3',
                'code' => 'SI-D3',
                'degree' => 'D3',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Komputer (S1)',
                'slug' => 'ilmu-komputer-s1',
                'code' => 'IK-S1',
                'degree' => 'S1',
                'is_active' => true,
            ],
        ];

        foreach ($prodis as $prodi) {
            Prodi::firstOrCreate(['slug' => $prodi['slug']], $prodi);
        }

        // 3. Seed Default UKMs
        $ukms = [
            [
                'name' => 'BSI Computer Club',
                'slug' => 'bsi-computer-club',
                'code' => 'BSICC',
                'description' => 'Unit Kegiatan Mahasiswa bidang teknologi informasi, pemrograman, dan multimedia.',
                'is_active' => true,
            ],
        ];

        foreach ($ukms as $ukm) {
            \App\Models\Ukm::firstOrCreate(['slug' => $ukm['slug']], $ukm);
        }

        // 4. Seed Default Admin KPUM
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
