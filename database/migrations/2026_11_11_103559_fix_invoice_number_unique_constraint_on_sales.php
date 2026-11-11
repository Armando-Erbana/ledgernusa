<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_invoice_number_unique');
            
            $table->unique(['company_id', 'invoice_number'], 'sales_company_invoice_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_company_invoice_unique');
            $table->unique('invoice_number', 'sales_invoice_number_unique');
        });
    }
};