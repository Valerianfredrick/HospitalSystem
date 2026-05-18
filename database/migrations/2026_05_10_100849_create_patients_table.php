<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_of_birth');
            $table->integer('age')->virtualAs('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Admission info
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ward')->nullable();
            $table->string('bed_number')->nullable();
            $table->enum('status', ['admitted', 'stable', 'critical', 'observation', 'recovering', 'discharged'])->default('admitted');
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('admitted_at')->nullable();

            // Vitals
            $table->string('blood_pressure')->nullable();
            $table->integer('pulse')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->decimal('weight', 5, 1)->nullable();

            // Discharge
            $table->timestamp('discharged_at')->nullable();
            $table->string('final_diagnosis')->nullable();
            $table->text('discharge_notes')->nullable();
            $table->date('followup_date')->nullable();
            $table->enum('discharge_condition', ['recovered', 'improved', 'transferred', 'against_advice', 'deceased'])->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
