<?php

namespace App\Http\Controllers;

use App\Models\TreatmentPackage;
use App\Models\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function packages(): View
    {
        $packages = TreatmentPackage::latest()->paginate(12);

        return view('packages.index', compact('packages'));
    }

    public function storePackage(Request $request): RedirectResponse
    {
        $package = TreatmentPackage::create($this->packageData($request));

        $message = $package->background_image
            ? 'Pakej rawatan berjaya ditambah bersama gambar latar.'
            : 'Pakej berjaya ditambah, tetapi tiada gambar latar dikaitkan.';

        return back()->with('success', $message);
    }

    public function updatePackage(Request $request, TreatmentPackage $treatmentPackage): RedirectResponse
    {
        $previousBackgroundImage = $treatmentPackage->background_image;
        $treatmentPackage->update($this->packageData($request, $treatmentPackage));
        if ($previousBackgroundImage !== $treatmentPackage->background_image && $previousBackgroundImage !== null) {
            SupabaseStorage::delete($previousBackgroundImage);
        }

        $message = $treatmentPackage->background_image
            ? 'Pakej rawatan dikemas kini bersama gambar latar.'
            : 'Butiran pakej dikemas kini, tetapi tiada gambar latar dikaitkan. Upload gambar sebelum menyimpan jika kad ini perlu imej.';

        return back()->with('success', $message);
    }

    public function deletePackage(TreatmentPackage $treatmentPackage): RedirectResponse
    {
        $backgroundImage = $treatmentPackage->background_image;
        $treatmentPackage->delete();
        if ($backgroundImage !== null) {
            SupabaseStorage::delete($backgroundImage);
        }

        return back()->with('success', 'Pakej rawatan dipadam.');
    }

    private function packageData(Request $request, ?TreatmentPackage $treatmentPackage = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['nullable', 'integer', 'between:1,1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'name_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'duration_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'price_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'package_label_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'background_image_path' => ['nullable', 'string', 'max:120'],
            'remove_background_image' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->boolean('remove_background_image')) {
            $data['background_image'] = null;
            unset($data['remove_background_image']);
        } elseif ($request->filled('background_image_path')) {
            $path = $request->string('background_image_path')->toString();
            if (! SupabaseStorage::objectExists($path, 'packages')) {
                throw \Illuminate\Validation\ValidationException::withMessages(['background_image_path' => 'Gambar belum selesai dihantar ke Supabase. Pilih semula gambar dan tunggu sehingga upload selesai.']);
            }
            $data['background_image'] = $path;
            unset($data['remove_background_image']);
        } else {
            $data['background_image'] = $treatmentPackage?->background_image;
            unset($data['remove_background_image']);
        }
        unset($data['background_image_path']);

        return $data;
    }
}
