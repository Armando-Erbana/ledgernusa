<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Semua company yang sudah ada = user lama = sudah onboarding (bypass)
        // company yang dibuat SETELAH migration ini akan NULL (wajib onboarding)
        DB::table('companies')
            ->whereNull('onboarding_completed_at')
            ->update(['onboarding_completed_at' => DB::raw('created_at')]);
    }

    public function down(): void {}
};