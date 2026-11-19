<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('alter table public.site_settings enable row level security');

            $hasSupabaseRoles = DB::selectOne("select exists (select 1 from pg_roles where rolname = 'anon') as present")->present;
            if ($hasSupabaseRoles) {
                DB::statement('revoke all privileges on table public.site_settings from anon, authenticated');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
