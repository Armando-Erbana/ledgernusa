<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_pkp')->default(false)->after('npwp');        // Pengusaha Kena Pajak
            $table->decimal('ppn_rate', 5, 2)->default(11.00)->after('is_pkp'); // tarif PPN %
            $table->boolean('ppn_included')->default(false)->after('ppn_rate'); // harga sudah termasuk PPN?
            $table->string('tax_office')->nullable()->after('ppn_included');   // KPP
            $table->string('efin')->nullable()->after('tax_office');          // e-FIN
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['is_pkp', 'ppn_rate', 'ppn_included', 'tax_office', 'efin']);
        });
    }
};