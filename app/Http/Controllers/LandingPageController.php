<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\SiteSettingRevision;
use App\Models\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function edit(): View
    {
        $siteSetting = SiteSetting::where('key', 'landing_page')->first();
        $revisions = $siteSetting?->revisions()->limit(10)->get() ?? collect();

        return view('landing-page.edit', [
            'landingPage' => SiteSetting::landingPage(),
            'revisions' => $revisions,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $sections = ['header', 'hero', 'about', 'packages', 'quote', 'steps', 'booking', 'footer'];
        $textColorFields = ['brand_name', 'brand_tagline', 'hero_eyebrow', 'hero_title', 'hero_highlight', 'hero_description', 'about_eyebrow', 'about_title', 'about_description', 'about_point_one_title', 'about_point_one_body', 'about_point_two_title', 'about_point_two_body', 'packages_eyebrow', 'packages_title', 'packages_description', 'quote_eyebrow', 'quote', 'steps_eyebrow', 'steps_title', 'step_one_title', 'step_one_body', 'step_two_title', 'step_two_body', 'step_three_title', 'step_three_body', 'booking_eyebrow', 'booking_title', 'booking_description', 'booking_note', 'booking_button', 'footer_description', 'navigation_links', 'navigation_button_text', 'footer_links', 'footer_headings'];

        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:80'],
            'brand_tagline' => ['nullable', 'string', 'max:120'],
            'hero_eyebrow' => ['required', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_highlight' => ['required', 'string', 'max:120'],
            'hero_description' => ['required', 'string', 'max:1000'],
            'about_eyebrow' => ['required', 'string', 'max:120'],
            'about_title' => ['required', 'string', 'max:180'],
            'about_description' => ['required', 'string', 'max:1500'],
            'about_point_one_title' => ['required', 'string', 'max:80'],
            'about_point_one_body' => ['required', 'string', 'max:300'],
            'about_point_two_title' => ['required', 'string', 'max:80'],
            'about_point_two_body' => ['required', 'string', 'max:300'],
            'packages_eyebrow' => ['required', 'string', 'max:120'],
            'packages_title' => ['required', 'string', 'max:180'],
            'packages_description' => ['required', 'string', 'max:1000'],
            'quote_eyebrow' => ['required', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:400'],
            'steps_eyebrow' => ['required', 'string', 'max:120'],
            'steps_title' => ['required', 'string', 'max:180'],
            'step_one_title' => ['required', 'string', 'max:80'],
            'step_one_body' => ['required', 'string', 'max:300'],
            'step_two_title' => ['required', 'string', 'max:80'],
            'step_two_body' => ['required', 'string', 'max:300'],
            'step_three_title' => ['required', 'string', 'max:80'],
            'step_three_body' => ['required', 'string', 'max:300'],
            'booking_eyebrow' => ['required', 'string', 'max:120'],
            'booking_title' => ['required', 'string', 'max:180'],
            'booking_description' => ['required', 'string', 'max:1000'],
            'booking_note' => ['required', 'string', 'max:500'],
            'booking_button' => ['required', 'string', 'max:80'],
            'footer_description' => ['required', 'string', 'max:300'],
            'theme.deep' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.green' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.accent' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.cream' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme.paper' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_colors' => ['required', 'array:'.implode(',', $textColorFields)],
            ...collect($textColorFields)
                ->mapWithKeys(fn (string $field): array => ["text_colors.{$field}" => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']])
                ->all(),
            'form_fields' => ['nullable', 'array', 'max:15'],
            'form_fields.*.key' => ['required', 'alpha_dash:ascii', 'max:40', 'not_in:name,phone,preferred_at,treatment_package_id,message,website'],
            'form_fields.*.label' => ['required', 'string', 'max:80'],
            'form_fields.*.type' => ['required', Rule::in(['text', 'email', 'tel', 'number', 'date', 'textarea', 'select', 'checkbox'])],
            'form_fields.*.placeholder' => ['nullable', 'string', 'max:120'],
            'form_fields.*.options' => ['nullable', 'string', 'max:1000'],
            'form_fields.*.required' => ['nullable', 'boolean'],
            'logo_image_path' => ['nullable', 'string', 'max:180'],
            'hero_image_path' => ['nullable', 'string', 'max:180'],
            'about_image_path' => ['nullable', 'string', 'max:180'],
            'remove_logo_image' => ['nullable', 'boolean'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'remove_about_image' => ['nullable', 'boolean'],
            'section_design' => ['required', 'array:'.implode(',', $sections)],
            'section_image_paths' => ['nullable', 'array:'.implode(',', $sections)],
            'section_image_paths.*' => ['nullable', 'string', 'max:180'],
            'remove_section_images' => ['nullable', 'array:'.implode(',', $sections)],
            'remove_section_images.*' => ['nullable', 'boolean'],
            ...collect($sections)->flatMap(fn (string $section): array => [
                "section_design.{$section}.background_color" => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                "section_design.{$section}.use_background_color" => ['nullable', 'boolean'],
                "section_design.{$section}.overlay_color" => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                "section_design.{$section}.overlay_opacity" => ['required', 'integer', 'between:0,80'],
            ])->all(),
        ]);

        $landingPage = SiteSetting::landingPage();
        unset($data['logo_image_path'], $data['hero_image_path'], $data['about_image_path'], $data['section_image_paths'], $data['remove_section_images']);
        foreach ($sections as $section) {
            $data['section_design'][$section]['use_background_color'] = $request->boolean("section_design.{$section}.use_background_color");
            $path = $request->input("section_image_paths.{$section}");
            if ($request->boolean("remove_section_images.{$section}")) {
                $data['section_images'][$section] = null;
            } elseif (filled($path)) {
                if (! SupabaseStorage::objectExists($path, 'landing/sections/'.$section)) {
                    return back()->withErrors(["section_image_paths.{$section}" => 'Gambar belum selesai dihantar ke Supabase. Tunggu status upload berjaya sebelum menyimpan.'])->withInput();
                }
                $data['section_images'][$section] = $path;
            } else {
                $data['section_images'][$section] = $landingPage['section_images'][$section] ?? null;
            }
        }
        $data['form_fields'] = collect($request->input('form_fields', []))
            ->map(function (array $field): array {
                return [
                    'key' => $field['key'],
                    'label' => trim($field['label']),
                    'type' => $field['type'],
                    'placeholder' => trim($field['placeholder'] ?? ''),
                    'required' => filter_var($field['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'options' => collect(preg_split('/\r\n|\r|\n/', $field['options'] ?? ''))
                        ->map(fn (string $option): string => trim($option))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        foreach ($data['form_fields'] as $index => $field) {
            if ($field['type'] === 'select' && $field['options'] === []) {
                return back()->withErrors([
                    "form_fields.{$index}.options" => 'Masukkan sekurang-kurangnya satu pilihan untuk medan senarai.',
                ])->withInput();
            }
        }

        foreach (['logo_image', 'hero_image', 'about_image'] as $imageField) {
            $removeField = 'remove_'.$imageField;
            if ($request->boolean($removeField)) {
                $data[$imageField] = null;
            } elseif ($request->filled($imageField.'_path')) {
                $path = $request->string($imageField.'_path')->toString();
                if (! SupabaseStorage::objectExists($path, 'landing/'.$imageField)) {
                    return back()->withErrors([$imageField.'_path' => 'Gambar belum selesai dihantar ke Supabase. Pilih semula gambar dan tunggu sehingga upload selesai.'])->withInput();
                }
                $data[$imageField] = $path;
            } else {
                $data[$imageField] = $landingPage[$imageField] ?? null;
            }
            unset($data[$removeField]);
        }

        DB::transaction(function () use ($data, $landingPage): void {
            $siteSetting = SiteSetting::firstOrNew(['key' => 'landing_page']);
            $previousValue = $siteSetting->exists ? $siteSetting->value : $landingPage;
            if (! $siteSetting->exists) {
                $siteSetting->value = $previousValue;
                $siteSetting->save();
            }
            $siteSetting->revisions()->create(['value' => $previousValue]);
            $siteSetting->value = array_replace($landingPage, $data);
            $siteSetting->save();

            $retainedRevisionIds = $siteSetting->revisions()->limit(30)->pluck('id');
            $siteSetting->revisions()->whereNotIn('id', $retainedRevisionIds)->delete();
        });

        return redirect()->route('landing-page.edit')->with('success', 'Landing page berjaya dikemas kini. Tekan “Lihat pratonton” untuk menyemak perubahan.');
    }

    public function restore(int $revision): RedirectResponse
    {
        DB::transaction(function () use ($revision): void {
            $siteSetting = SiteSetting::where('key', 'landing_page')->lockForUpdate()->firstOrFail();
            $selectedRevision = $siteSetting->revisions()->whereKey($revision)->firstOrFail();

            $siteSetting->revisions()->create(['value' => $siteSetting->value]);
            $siteSetting->update(['value' => $selectedRevision->value]);
        });

        return redirect()->route('landing-page.edit')->with('success', 'Versi landing page dipulihkan. Versi sebelum pemulihan turut disimpan dalam sejarah.');
    }
}
