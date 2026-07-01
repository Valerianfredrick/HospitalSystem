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
        Schema::table('mortuary_records', function (Blueprint $table) {
            $table->string('body_tag')->nullable()->unique()->after('patient_id');
        });
    }

    public function down(): void
    {
        Schema::table('mortuary_records', function (Blueprint $table) {
            $table->dropColumn('body_tag');
        });
    }
};
