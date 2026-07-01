<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained()->cascadeOnDelete();
            $table->string('bed_number'); // e.g. "1", "ICU-2"
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');

            // Nullable + unique: a bed can hold at most one current patient,
            // and a patient can be linked back to exactly one bed (see patients.bed_id).
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->unique(['ward_id', 'bed_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
