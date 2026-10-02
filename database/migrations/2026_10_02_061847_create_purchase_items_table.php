<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts'); // akun beban/persediaan
            $table->string('description');
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('price', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('purchase_items'); }
};