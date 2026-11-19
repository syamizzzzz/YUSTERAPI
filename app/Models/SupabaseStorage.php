<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SupabaseStorage
{
    /** @return array{upload_url: string, object_path: string} */
    public static function createSignedUpload(string $folder, string $mimeType, string $inputName): array
    {
        $settings = self::settings($inputName);
        self::ensureBucket($settings, $inputName);
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw ValidationException::withMessages([$inputName => 'Pilih imej PNG, JPG atau WebP.']),
        };
        $objectPath = trim($folder, '/').'/'.Str::uuid().'.'.$extension;
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $objectPath)));
        $response = Http::timeout(20)->withHeaders(self::headers($settings['key']))
            ->post($settings['url'].'/storage/v1/object/upload/sign/'.rawurlencode($settings['bucket']).'/'.$encodedPath, [
                'upsert' => false,
            ]);

        $returnedUrl = $response->json('url') ?? $response->json('signedURL');
        $token = $response->json('token');
        if (is_string($returnedUrl) && ! is_string($token)) {
            parse_str((string) parse_url($returnedUrl, PHP_URL_QUERY), $query);
            $token = $query['token'] ?? null;
        }
        if ($response->failed() || ! is_string($returnedUrl) || ! is_string($token)) {
            throw ValidationException::withMessages([$inputName => 'Supabase tidak dapat menyediakan pautan upload. Semak bucket Storage dan kunci server.']);
        }

        $signedUrl = $returnedUrl;
        if (! str_starts_with($signedUrl, 'http')) {
            $signedUrl = $settings['url'].'/storage/v1'.'/'.ltrim($signedUrl, '/');
        }
        if (! str_contains($signedUrl, 'token=')) {
            $signedUrl .= (str_contains($signedUrl, '?') ? '&' : '?').'token='.rawurlencode($token);
        }

        return ['upload_url' => $signedUrl, 'object_path' => $objectPath];
    }

    public static function objectExists(string $objectPath, string $folder): bool
    {
        if (! preg_match('#^'.preg_quote(trim($folder, '/'), '#').'/[0-9a-f-]{36}\.(jpg|png|webp)$#i', $objectPath)) {
            return false;
        }

        $settings = self::settings('background_image');
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $objectPath)));
        $response = Http::timeout(15)->withHeaders(self::headers($settings['key']))
            ->get($settings['url'].'/storage/v1/object/info/'.rawurlencode($settings['bucket']).'/'.$encodedPath);

        return $response->successful();
    }

    public static function delete(?string $objectPath): void
    {
        if ($objectPath === null || $objectPath === '') {
            return;
        }

        $settings = self::settings('background_image');
        Http::timeout(20)->withHeaders(self::headers($settings['key']))
            ->delete(self::objectUrl($settings, $objectPath));
    }

    public static function publicUrl(?string $objectPath): ?string
    {
        if ($objectPath === null || $objectPath === '') {
            return null;
        }

        $settings = self::settings();
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $objectPath)));

        return $settings['url'].'/storage/v1/object/public/'.rawurlencode($settings['bucket']).'/'.$encodedPath;
    }

    /** @return array{url: string, key: string, bucket: string} */
    private static function settings(?string $inputName = null): array
    {
        $url = rtrim((string) config('services.supabase.url'), '/');
        $key = (string) config('services.supabase.secret_key');
        $bucket = (string) config('services.supabase.storage_bucket');

        if ($url === '' || $bucket === '' || ($inputName !== null && $key === '')) {
            throw ValidationException::withMessages([
                $inputName ?? 'background_image' => 'Upload Supabase belum disediakan. Isi SUPABASE_SECRET_KEY dalam .env dengan secret key server Supabase, kemudian jalankan php artisan config:clear.',
            ]);
        }

        return ['url' => $url, 'key' => $key, 'bucket' => $bucket];
    }

    /** @return array<string, string> */
    private static function headers(string $key): array
    {
        $headers = ['apikey' => $key];
        if (! str_starts_with($key, 'sb_')) {
            $headers['Authorization'] = 'Bearer '.$key;
        }

        return $headers;
    }

    /** @param array{url: string, key: string, bucket: string} $settings */
    private static function ensureBucket(array $settings, string $inputName): void
    {
        $bucketUrl = $settings['url'].'/storage/v1/bucket/'.rawurlencode($settings['bucket']);
        $headers = self::headers($settings['key']);
        $bucket = Http::timeout(20)->withHeaders($headers)->get($bucketUrl);
        $bucketMessage = strtolower((string) ($bucket->json('message') ?? $bucket->json('error') ?? ''));
        $bucketIsMissing = $bucket->notFound() || ($bucket->status() === 400 && str_contains($bucketMessage, 'bucket not found'));

        if ($bucketIsMissing) {
            $createdBucket = Http::timeout(20)->withHeaders($headers)->post($settings['url'].'/storage/v1/bucket', [
                'id' => $settings['bucket'],
                'name' => $settings['bucket'],
                'public' => true,
                'file_size_limit' => 2097152,
                'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
            ]);
            if ($createdBucket->failed()) {
                $creationMessage = strtolower((string) ($createdBucket->json('message') ?? $createdBucket->json('error') ?? ''));
                if ($createdBucket->status() !== 409 && ! str_contains($creationMessage, 'already exists')) {
                    throw ValidationException::withMessages([$inputName => 'Supabase gagal menyediakan bucket gambar (HTTP '.$createdBucket->status().'). Semak kunci server dan tetapan Storage.']);
                }
            }
        } elseif ($bucket->failed()) {
            $reason = $bucket->json('message') ?? $bucket->json('error') ?? 'semak kunci server dan tetapan Storage';
            throw ValidationException::withMessages([$inputName => 'Supabase Storage memberi HTTP '.$bucket->status().': '.mb_substr((string) $reason, 0, 120).'.']);
        }
    }

    /** @param array{url: string, key: string, bucket: string} $settings */
    private static function objectUrl(array $settings, string $objectPath): string
    {
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $objectPath)));

        return $settings['url'].'/storage/v1/object/'.rawurlencode($settings['bucket']).'/'.$encodedPath;
    }
}
