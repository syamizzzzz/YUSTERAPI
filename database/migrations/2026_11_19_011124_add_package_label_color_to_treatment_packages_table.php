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
        Schema::table('treatment_packages', function (Blueprint $table) {
            if (! Schema::hasColumn('treatment_packages', 'package_label_color')) {
                $table->string('package_label_color', 7)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatment_packages', function (Blueprint $table) {
            $table->dropColumn('package_label_color');
        });
    }
};
