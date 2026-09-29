<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin {email} {--name=} {--password=}';
    protected $description = 'Buat akun Super Admin LedgerNusa';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name') ?? 'Super Admin';
        $password = $this->option('password') ?? $this->secret('Password (min 8 karakter):');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $err) {
                $this->error($err);
            }
            return 1;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_super_admin' => true,
            ]
        );

        $this->info("✅ Super Admin dibuat/diupdate:");
        $this->line("   Email: {$user->email}");
        $this->line("   Nama:  {$user->name}");

        return 0;
    }
}