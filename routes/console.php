<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('staff:create {email} {name}', function (): void {
    $password = $this->secret('Kata laluan (sekurang-kurangnya 12 aksara)');
    if (strlen((string) $password) < 12) {
        $this->error('Kata laluan mesti sekurang-kurangnya 12 aksara.');

        return;
    }

    $email = strtolower((string) $this->argument('email'));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
        $this->error('Emel tidak sah atau sudah digunakan.');

        return;
    }

    User::create(['name' => $this->argument('name'), 'email' => $email, 'password' => $password]);
    $this->info('Akaun staf berjaya dicipta.');
})->purpose('Create a staff login for YUSTERAPI');

Artisan::command('staff:reset-password {email}', function (): void {
    $email = strtolower(trim((string) $this->argument('email')));
    $user = User::where('email', $email)->first();

    if ($user === null) {
        $this->error('Akaun staf dengan emel itu tidak ditemui.');

        return;
    }

    $password = $this->secret('Kata laluan baharu (sekurang-kurangnya 12 aksara)');
    if (! is_string($password) || strlen($password) < 12) {
        $this->error('Kata laluan mesti sekurang-kurangnya 12 aksara.');

        return;
    }

    $confirmation = $this->secret('Ulang kata laluan baharu');
    if (! is_string($confirmation) || ! hash_equals($password, $confirmation)) {
        $this->error('Kata laluan tidak sepadan. Sila jalankan arahan semula.');

        return;
    }

    $user->forceFill(['password' => Hash::make($password)])->save();
    $this->info('Kata laluan akaun staf berjaya ditetapkan semula.');
})->purpose('Reset an existing YUSTERAPI staff password');

Artisan::command('yusterapi:move-to-supabase', function (): void {
    $url = config('database.supabase_url');

    if (! is_string($url) || ! str_starts_with($url, 'postgres')) {
        $this->error('Tetapkan SUPABASE_DB_URL kepada Session pooler URL (port 5432) dalam .env dahulu.');

        return;
    }

    if (config('database.default') !== 'sqlite') {
        $this->error('Arahan ini memerlukan SQLite sebagai sumber. Jangan jalankan selepas DB_CONNECTION ditukar.');

        return;
    }

    config(['database.connections.supabase' => array_merge(config('database.connections.pgsql'), [
        'url' => $url,
        'sslmode' => 'require',
    ])]);

    $source = DB::connection('sqlite');
    $target = DB::connection('supabase');
    $tables = ['users', 'customers', 'treatment_packages', 'bookings', 'payments', 'customer_notes', 'appointment_requests'];

    try {
        $target->select('select 1');
    } catch (Throwable $exception) {
        $this->error('Sambungan Supabase gagal. Semak URL, kata laluan dan rangkaian.');

        return;
    }

    if (Artisan::call('migrate', ['--database' => 'supabase', '--force' => true]) !== 0) {
        $this->error('Migrasi jadual Supabase gagal. Sambungan SQLite tidak diubah.');

        return;
    }

    foreach ($tables as $table) {
        if ($target->table($table)->exists()) {
            $this->error("Jadual {$table} di Supabase sudah ada data. Pemindahan dihentikan untuk mengelakkan pertindihan.");

            return;
        }
    }

    $target->transaction(function () use ($source, $target, $tables): void {
        foreach ($tables as $table) {
            $source->table($table)->orderBy('id')->chunkById(100, function ($rows) use ($target, $table): void {
                $target->table($table)->insert($rows->map(fn ($row): array => (array) $row)->all());
            });

            $target->statement("select setval(pg_get_serial_sequence('public.{$table}', 'id'), coalesce(max(id), 1), max(id) is not null) from public.{$table}");
        }
    });

    foreach ($tables as $table) {
        $localCount = $source->table($table)->count();
        $remoteCount = $target->table($table)->count();
        if ($localCount !== $remoteCount) {
            $this->error("Semakan {$table} gagal: bilangan rekod tidak sama.");

            return;
        }

        $this->line("{$table}: {$remoteCount} rekod dipindahkan");
    }

    $this->info('Pemindahan selesai. Sambungan SQLite asal masih tersedia sebagai sandaran.');
})->purpose('Create protected Supabase tables and copy current YUSTERAPI records');
