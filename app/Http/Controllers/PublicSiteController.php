<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\SiteSetting;
use App\Models\TreatmentPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        $packages = TreatmentPackage::where('is_active', true)->orderBy('name')->limit(6)->get();
        $landingPage = SiteSetting::landingPage();

        return view('public.home', compact('packages', 'landingPage'));
    }

    public function storeRequest(Request $request): RedirectResponse|JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'preferred_at' => ['nullable', 'date', 'after:today'],
            'treatment_package_id' => ['nullable', 'exists:treatment_packages,id'],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'max:0'],
        ];

        $landingPage = SiteSetting::landingPage();
        foreach ($landingPage['form_fields'] as $field) {
            $fieldRules = [$field['required'] ? 'required' : 'nullable'];
            $fieldRules[] = match ($field['type']) {
                'email' => 'email:rfc',
                'number' => 'numeric',
                'date' => 'date',
                'checkbox' => 'boolean',
                'select' => 'string',
                'textarea', 'text', 'tel' => 'string',
            };
            if ($field['type'] !== 'checkbox') {
                $fieldRules[] = 'max:'.($field['type'] === 'textarea' ? '3000' : '255');
            }
            if ($field['type'] === 'select' && count($field['options']) > 0) {
                $fieldRules[] = Rule::in($field['options']);
            }
            $rules['custom_fields.'.$field['key']] = $fieldRules;
        }

        $data = $request->validate($rules);

        unset($data['website']);
        $submittedFields = $data['custom_fields'] ?? [];
        $data['custom_fields'] = [];
        foreach ($landingPage['form_fields'] as $field) {
            if (array_key_exists($field['key'], $submittedFields)) {
                $data['custom_fields'][$field['key']] = [
                    'label' => $field['label'],
                    'value' => $submittedFields[$field['key']],
                ];
            }
        }
        if (! empty($data['treatment_package_id']) && ! TreatmentPackage::whereKey($data['treatment_package_id'])->where('is_active', true)->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pakej yang dipilih tidak tersedia.', 'errors' => ['treatment_package_id' => ['Pakej yang dipilih tidak tersedia.']]], 422);
            }

            return back()->withErrors(['treatment_package_id' => 'Pakej yang dipilih tidak tersedia.'])->withInput();
        }
        AppointmentRequest::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Permintaan diterima. Pasukan YUSTERAPI akan menghubungi anda untuk susulan.'], 201);
        }

        return redirect(route('home').'#tempah')->with('request_sent', true);
    }
}
