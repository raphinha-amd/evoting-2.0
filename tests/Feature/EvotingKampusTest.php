<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pemilu;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EvotingKampusTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $prodiSI;
    protected $kelasSI;
    protected $studentSI;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'fullname' => 'Admin KPUM Test',
                'password' => bcrypt('admin'),
                'unencrypted_password' => 'admin',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->prodiSI = Prodi::firstOrCreate(
            ['slug' => 'sistem-informasi-s1-test'],
            [
                'name' => 'S1 Sistem Informasi Test',
                'code' => 'SI-T',
                'degree' => 'S1',
                'is_active' => true,
            ]
        );

        $this->kelasSI = Kelas::firstOrCreate(
            ['slug' => 'si-2024-test'],
            [
                'name' => 'SI-2024-Test',
                'prodi_id' => $this->prodiSI->id,
                'is_active' => true,
            ]
        );

        $this->studentSI = User::firstOrCreate(
            ['username' => '12219998'],
            [
                'fullname' => 'Mahasiswa Test SI',
                'password' => bcrypt('password'),
                'unencrypted_password' => 'password',
                'role' => 'mahasiswa',
                'kelas_id' => $this->kelasSI->id,
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_access_prodi_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.prodi'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Program Studi');
    }

    public function test_admin_can_access_roles_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.roles'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Role Pengguna');
    }

    public function test_private_pemilu_restricts_access_to_designated_prodi_and_requires_password(): void
    {
        // 1. Buat pemilu private khusus prodi SI
        $pemilu = Pemilu::create([
            'name' => 'Pemilu Private Test',
            'slug' => 'pemilu-private-test',
            'description' => 'Khusus SI',
            'is_private' => 1,
            'password' => 'secret123',
            'status' => 1,
            'user_id' => $this->admin->id,
            'total_participants' => 1,
        ]);
        $pemilu->prodis()->sync([$this->prodiSI->id]);

        // 2. Mahasiswa SI belum verifikasi password -> harus dialihkan dengan pesan error
        $response = $this->actingAs($this->studentSI)->get(route('user.pemilu.join', $pemilu->slug));
        $response->assertRedirect(route('user.dashboard'));

        // 3. Mahasiswa SI memverifikasi password yang benar
        $verifyResponse = $this->actingAs($this->studentSI)->post(route('user.pemilu.verify-password.join', $pemilu->slug), [
            'password' => 'secret123',
        ]);
        $verifyResponse->assertRedirect(route('user.pemilu.join', $pemilu->slug));

        // 4. Setelah terverifikasi, Mahasiswa SI dapat mengakses halaman voting pemilu
        $joinResponse = $this->actingAs($this->studentSI)->withSession(['pemilu_' . $pemilu->slug . '_verified' => true])
                             ->get(route('user.pemilu.join', $pemilu->slug));
        $joinResponse->assertStatus(200);
    }

    public function test_other_prodi_cannot_verify_or_join_private_pemilu(): void
    {
        $prodiIK = Prodi::firstOrCreate(
            ['slug' => 'ilmu-komputer-s1-test'],
            [
                'name' => 'S1 Ilmu Komputer Test',
                'code' => 'IK-T',
                'degree' => 'S1',
                'is_active' => true,
            ]
        );

        $kelasIK = Kelas::firstOrCreate(
            ['slug' => 'ik-2024-test'],
            [
                'name' => 'IK-2024-Test',
                'prodi_id' => $prodiIK->id,
                'is_active' => true,
            ]
        );

        $studentIK = User::firstOrCreate(
            ['username' => '20219998'],
            [
                'fullname' => 'Mahasiswa Test IK',
                'password' => bcrypt('password'),
                'unencrypted_password' => 'password',
                'role' => 'mahasiswa',
                'kelas_id' => $kelasIK->id,
                'is_active' => true,
            ]
        );

        $pemilu = Pemilu::create([
            'name' => 'Pemilu Khusus SI Saja',
            'slug' => 'pemilu-khusus-si-saja',
            'description' => 'Khusus SI',
            'is_private' => 1,
            'password' => 'secret123',
            'status' => 1,
            'user_id' => $this->admin->id,
            'total_participants' => 1,
        ]);
        $pemilu->prodis()->sync([$this->prodiSI->id]);

        // Mahasiswa IK mencoba verifikasi password -> harus ditolak
        $verifyResponse = $this->actingAs($studentIK)->post(route('user.pemilu.verify-password.join', $pemilu->slug), [
            'password' => 'secret123',
        ]);
        $verifyResponse->assertRedirect(route('user.dashboard'));

        // Mahasiswa IK mencoba langsung join -> harus ditolak
        $joinResponse = $this->actingAs($studentIK)->get(route('user.pemilu.join', $pemilu->slug));
        $joinResponse->assertRedirect(route('user.dashboard'));
    }

    public function test_admin_can_access_ukm_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.manage.ukm'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Unit Kegiatan Mahasiswa');
    }

    public function test_admin_can_create_ukm_and_add_member(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.manage.ukm.add'), [
            'name' => 'UKM Robotika Test',
            'code' => 'ROBOT',
            'description' => 'UKM robotik kampus',
            'is_active' => 1,
        ]);

        $response->assertSessionHas('success');
        $ukm = \App\Models\Ukm::where('code', 'ROBOT')->first();
        $this->assertNotNull($ukm);

        // Tambah anggota manual
        $memberResponse = $this->actingAs($this->admin)->post(route('admin.manage.ukm.members.add', $ukm->id), [
            'username' => $this->studentSI->username,
        ]);
        $memberResponse->assertSessionHas('success');
        $this->assertTrue($ukm->users()->where('users.id', $this->studentSI->id)->exists());
    }

    public function test_private_ukm_pemilu_restricts_access_to_ukm_members_and_requires_password(): void
    {
        $ukm = \App\Models\Ukm::create([
            'name' => 'UKM Musik Test',
            'slug' => 'ukm-musik-test',
            'code' => 'MUSIK-T',
            'is_active' => true,
        ]);

        // Student SI adalah anggota UKM Musik
        $ukm->users()->attach($this->studentSI->id);

        // Buat student lain yang BUKAN anggota UKM Musik
        $nonMember = User::create([
            'fullname' => 'Mahasiswa Non UKM',
            'username' => '33339999',
            'password' => bcrypt('password'),
            'unencrypted_password' => 'password',
            'role' => 'mahasiswa',
            'kelas_id' => $this->kelasSI->id,
            'is_active' => true,
        ]);

        // Buat Pemilu Private UKM (is_private = 2)
        $pemilu = Pemilu::create([
            'name' => 'Pemilihan Ketua UKM Musik',
            'slug' => 'pemilihan-ketua-ukm-musik',
            'description' => 'Khusus Anggota Musik',
            'is_private' => 2,
            'ukm_id' => $ukm->id,
            'password' => 'musikpass',
            'status' => 1,
            'user_id' => $this->admin->id,
            'total_participants' => 1,
        ]);

        // 1. Non-member mencoba verifikasi password -> harus ditolak
        $nonMemberVerify = $this->actingAs($nonMember)->post(route('user.pemilu.verify-password.join', $pemilu->slug), [
            'password' => 'musikpass',
        ]);
        $nonMemberVerify->assertRedirect(route('user.dashboard'));

        // 2. Member UKM memverifikasi password
        $memberVerify = $this->actingAs($this->studentSI)->post(route('user.pemilu.verify-password.join', $pemilu->slug), [
            'password' => 'musikpass',
        ]);
        $memberVerify->assertRedirect(route('user.pemilu.join', $pemilu->slug));

        // 3. Member UKM dapat join halaman voting
        $joinResponse = $this->actingAs($this->studentSI)->withSession(['pemilu_' . $pemilu->slug . '_verified' => true])
                             ->get(route('user.pemilu.join', $pemilu->slug));
        $joinResponse->assertStatus(200);
    }

    public function test_ukm_member_import_by_nim(): void
    {
        $ukm = \App\Models\Ukm::create([
            'name' => 'UKM Robotika Import Test',
            'slug' => 'ukm-robotika-import-test',
            'code' => 'ROBOT-IMP',
            'is_active' => true,
        ]);

        $student2 = User::create([
            'fullname' => 'Mahasiswa Test 2',
            'username' => '12219997',
            'password' => bcrypt('password'),
            'unencrypted_password' => 'password',
            'role' => 'mahasiswa',
            'kelas_id' => $this->kelasSI->id,
            'is_active' => true,
        ]);

        // Simulasi baris Excel dengan NIM (ada header dan NIM valid & tidak valid)
        $rows = collect([
            ['NIM'],
            [$this->studentSI->username],
            ['12219997'],
            ['99999999'], // NIM tidak ada di database
        ]);

        $import = new \App\Imports\UkmMemberImport($ukm);
        $import->collection($rows);

        $this->assertEquals(2, $import->getImportedCount());
        $this->assertContains('99999999', $import->getNotFoundNims());
        $this->assertTrue($ukm->users()->where('users.id', $this->studentSI->id)->exists());
        $this->assertTrue($ukm->users()->where('users.id', $student2->id)->exists());
    }

    public function test_private_pemilu_without_password_allows_direct_join_for_eligible_voter(): void
    {
        // Pemilu private tanpa password (password = null)
        $pemilu = Pemilu::create([
            'name' => 'Pemilu Private Tanpa Password',
            'slug' => 'pemilu-private-tanpa-password',
            'description' => 'Khusus SI langsung masuk',
            'is_private' => 1,
            'password' => null,
            'status' => 1,
            'user_id' => $this->admin->id,
            'total_participants' => 1,
        ]);
        $pemilu->prodis()->sync([$this->prodiSI->id]);

        // Mahasiswa SI langsung dapat mengakses surat suara tanpa perlu verifikasi session password
        $response = $this->actingAs($this->studentSI)->get(route('user.pemilu.join', $pemilu->slug));
        $response->assertStatus(200);
        $response->assertSee('Pemilu Private Tanpa Password');
    }

    public function test_copyright_page_displays_creators(): void
    {
        $response = $this->get(route('copyright'));
        $response->assertStatus(200);
        $response->assertSee('Raffi Ahmad');
        $response->assertSee('Muhamad Hilal');
        $response->assertSee('Farhan Dika Alsani');
        $response->assertSee('Ketua Divisi Teknis Penyelenggara');
    }
}
