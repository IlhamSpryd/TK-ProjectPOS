<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder untuk membuat staff uji coba (hanya untuk lingkungan lokal/testing).
 *
 * Password digenerate acak setiap kali dijalankan dan ditampilkan
 * di terminal — TIDAK disimpan di kode sumber.
 */
class TestStaffSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: tolak eksekusi di luar lingkungan lokal/testing
        if (! app()->environment('local', 'testing')) {
            $this->command->error('Seeder ini hanya boleh dijalankan di environment local atau testing.');
            return;
        }

        $managerPassword = Str::random(16);
        $cashierPassword = Str::random(16);

        $managerRole = Role::where('name', 'Manager')->first();
        $cashierRole = Role::where('name', 'Cashier')->first();

        if ($managerRole) {
            Staff::updateOrCreate(
                ['email' => 'manager@test.com'],
                [
                    'id' => (string) Str::uuid(),
                    'full_name' => 'Test Manager',
                    'password_hash' => $managerPassword,
                    'role_id' => $managerRole->id,
                    'active' => true,
                ]
            );
            $this->command->info("Manager: manager@test.com / {$managerPassword}");
        } else {
            $this->command->warn('Role "Manager" tidak ditemukan. Lewati pembuatan akun manager.');
        }

        if ($cashierRole) {
            Staff::updateOrCreate(
                ['email' => 'cashier@test.com'],
                [
                    'id' => (string) Str::uuid(),
                    'full_name' => 'Test Cashier',
                    'password_hash' => $cashierPassword,
                    'role_id' => $cashierRole->id,
                    'active' => true,
                ]
            );
            $this->command->info("Cashier: cashier@test.com / {$cashierPassword}");
        } else {
            $this->command->warn('Role "Cashier" tidak ditemukan. Lewati pembuatan akun kasir.');
        }

        $this->command->warn('⚠ Catat password di atas sekarang — tidak akan ditampilkan lagi.');
    }
}
