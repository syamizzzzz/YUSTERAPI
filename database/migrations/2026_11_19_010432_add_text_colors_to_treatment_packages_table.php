<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatment_packages', function (Blueprint $table): void {
            if (! Schema::hasColumn('treatment_packages', 'name_color')) {
                $table->string('name_color', 7)->nullable();
            }
            if (! Schema::hasColumn('treatment_packages', 'description_color')) {
                $table->string('description_color', 7)->nullable();
            }
            if (! Schema::hasColumn('treatment_packages', 'duration_color')) {
                $table->string('duration_color', 7)->nullable();
            }
            if (! Schema::hasColumn('treatment_packages', 'price_color')) {
                $table->string('price_color', 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treatment_packages', function (Blueprint $table): void {
            $table->dropColumn(['name_color', 'description_color', 'duration_color', 'price_color']);
        });
    }
};
