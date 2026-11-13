<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->decimal('min_amount', 18, 2)->default(0);
            $table->decimal('max_amount', 18, 2)->nullable();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->timestamps();
        });

        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->morphs('approvable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('approval_status')->nullable();
            $table->foreignId('current_approver_position_id')->nullable()->constrained('positions')->nullOnDelete();
        });
        
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('approval_status')->nullable();
            $table->foreignId('current_approver_position_id')->nullable()->constrained('positions')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['current_approver_position_id']);
            $table->dropColumn(['approval_status', 'current_approver_position_id']);
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['current_approver_position_id']);
            $table->dropColumn(['approval_status', 'current_approver_position_id']);
        });
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approval_rules');
    }
};