<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'migrations', 'users', 'password_reset_tokens', 'sessions',
            'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
            'customers', 'treatment_packages', 'bookings', 'payments',
            'customer_notes', 'appointment_requests',
        ];

        $hasSupabaseRoles = DB::selectOne("select exists (select 1 from pg_roles where rolname = 'anon') as present")->present;

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::statement("alter table public.{$table} enable row level security");

            if ($hasSupabaseRoles) {
                DB::statement("revoke all privileges on table public.{$table} from anon, authenticated");
            }
        }
    }

    public function down(): void
    {
        // Security settings are intentionally retained on rollback.
    }
};
