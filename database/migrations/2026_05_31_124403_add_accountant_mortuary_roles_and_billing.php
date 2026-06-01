<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bills table
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');  // doctor who discharged
            $table->foreignId('processed_by')->nullable()->constrained('users'); // accountant

            // Auto-calculated line items
            $table->integer('bed_days')->default(0);
            $table->decimal('bed_rate_per_day', 10, 2)->default(10000); // adjust to your currency
            $table->decimal('bed_total', 10, 2)->default(0);

            $table->decimal('lab_total', 10, 2)->default(0);
            $table->decimal('drugs_total', 10, 2)->default(0);

            // Manual charges added by doctor/accountant
            $table->json('extra_charges')->nullable(); // [{label, amount}]

            $table->decimal('grand_total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);

            $table->enum('status', ['unpaid', 'partial', 'paid', 'waived'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'insurance', 'mobile_money', 'bank', 'waived'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Mortuary records table
        Schema::create('mortuary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_by')->constrained('users');   // doctor
            $table->foreignId('received_by')->nullable()->constrained('users'); // mortuary staff
            $table->string('cause_of_death')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'received', 'released'])->default('pending');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortuary_records');
        Schema::dropIfExists('bills');
    }
};
