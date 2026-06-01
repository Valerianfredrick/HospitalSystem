<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |---------------------------------------
        | 1. STOCK ITEMS (PARENT TABLE)
        |---------------------------------------
        */
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();

            // Core identification
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->string('sku')->nullable()->unique();

            // Classification
            $table->string('category')->default('Other');
            $table->string('unit')->default('Tablet');

            // Stock levels
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reorder_level')->default(10);

            // Pricing
            $table->decimal('unit_price', 12, 2)->nullable();

            // Supplier
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();

            // Dates
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();

            // Extra
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        /*
        |---------------------------------------
        | 2. PATIENT NOTES
        |---------------------------------------
        */
        Schema::create('patient_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        /*
        |---------------------------------------
        | 3. PRESCRIPTIONS
        |---------------------------------------
        */
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

            $table->foreignId('dispensed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        /*
        |---------------------------------------
        | 4. STOCK MOVEMENTS (CHILD TABLE)
        |---------------------------------------
        */
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_item_id')
                ->constrained('stock_items')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('prescription_id')
                ->nullable()
                ->constrained('prescriptions')
                ->nullOnDelete();

            $table->enum('type', ['in', 'out', 'adjustment', 'expired']);
            $table->integer('quantity');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('patient_notes');
        Schema::dropIfExists('stock_items');
    }
};
