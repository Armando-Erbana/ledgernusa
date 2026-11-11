<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'business_type')) {
                $table->string('business_type', 20)->nullable()->after('name');
            }
            if (! Schema::hasColumn('companies', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('business_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'onboarding_completed_at']);
        });
    }
};