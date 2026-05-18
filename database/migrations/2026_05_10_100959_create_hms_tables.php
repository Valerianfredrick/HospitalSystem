<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Patient clinical notes
        Schema::create('patient_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        // Prescriptions
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->integer('duration_days')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_dispensed')->default(false);
            $table->timestamp('dispensed_at')->nullable();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Pharmacy / Stock
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['medicine', 'consumable', 'equipment'])->default('medicine');
            $table->string('manufacturer')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('unit')->default('tablets');
            $table->integer('reorder_level')->default(10);
            $table->date('expiry_date')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('location')->nullable(); // e.g. shelf A3
            $table->timestamps();
            $table->softDeletes();
        });

        // Stock movement log
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'expired']);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('patient_notes');
    }
};
