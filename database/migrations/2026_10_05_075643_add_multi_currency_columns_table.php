<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // journals: currency + exchange_rate
        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'currency')) {
                $table->string('currency', 3)->default('IDR')->after('date');
            }
            if (!Schema::hasColumn('journals', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->default(1)->after('currency');
            }
        });

        // journal_entries: foreign_debit + foreign_credit
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'foreign_debit')) {
                $table->decimal('foreign_debit', 18, 2)->nullable()->after('credit');
            }
            if (!Schema::hasColumn('journal_entries', 'foreign_credit')) {
                $table->decimal('foreign_credit', 18, 2)->nullable()->after('foreign_debit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['foreign_debit', 'foreign_credit']);
        });
    }
};