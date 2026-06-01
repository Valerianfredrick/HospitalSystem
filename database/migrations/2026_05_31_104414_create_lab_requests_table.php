<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();  // doctor
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // lab attendant
            $table->string('test_name');
            $table->text('clinical_notes')->nullable();   // doctor's notes / what to measure
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('results')->nullable();          // lab attendant fills this
            $table->text('interpretation')->nullable();   // lab attendant's interpretation
            $table->enum('result_flag', ['normal', 'abnormal', 'critical'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
    }
};
