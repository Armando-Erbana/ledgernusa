<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@ledgernusa.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_super_admin' => true,
            ]
        );

        $this->command->info('Super Admin dibuat:');
        $this->command->info('- superadmin@ledgernusa.test / password123');
    }
}