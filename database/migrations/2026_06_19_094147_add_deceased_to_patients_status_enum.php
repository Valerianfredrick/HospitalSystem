<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM('admitted','stable','critical','observation','recovering','discharged','deceased') NOT NULL DEFAULT 'admitted'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE patients MODIFY COLUMN status ENUM('admitted','stable','critical','observation','recovering','discharged') NOT NULL DEFAULT 'admitted'");
    }
};
