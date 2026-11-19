<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_setting_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_setting_id')->constrained()->cascadeOnDelete();
            $table->json('value');
            $table->timestamps();
            $table->index(['site_setting_id', 'created_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('alter table public.site_setting_revisions enable row level security');

            $hasSupabaseRoles = DB::selectOne("select exists (select 1 from pg_roles where rolname = 'anon') as present")->present;
            if ($hasSupabaseRoles) {
                DB::statement('revoke all privileges on table public.site_setting_revisions from anon, authenticated');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_setting_revisions');
    }
};
