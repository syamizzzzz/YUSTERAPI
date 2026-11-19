<?php

namespace App\Http\Controllers;

use App\Models\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupabaseUploadController extends Controller
{
    public function sign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'context' => ['required', 'in:package,logo_image,hero_image,about_image,section_header,section_hero,section_about,section_packages,section_quote,section_steps,section_booking,section_footer'],
            'mime_type' => ['required', 'in:image/jpeg,image/png,image/webp'],
            'size' => ['required', 'integer', 'between:1,2097152'],
        ]);
        $folder = match ($data['context']) {
            'package' => 'packages',
            'section_header' => 'landing/sections/header',
            'section_hero' => 'landing/sections/hero',
            'section_about' => 'landing/sections/about',
            'section_packages' => 'landing/sections/packages',
            'section_quote' => 'landing/sections/quote',
            'section_steps' => 'landing/sections/steps',
            'section_booking' => 'landing/sections/booking',
            'section_footer' => 'landing/sections/footer',
            default => 'landing/'.$data['context'],
        };

        return response()->json(SupabaseStorage::createSignedUpload($folder, $data['mime_type'], 'image'));
    }
}
