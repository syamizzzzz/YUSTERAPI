@extends('layouts.app')
@section('title', 'Edit Landing Page')
@section('content')
<div class="page-heading"><div><span class="eyebrow">LAMAN UNTUK PELANGGAN</span><h1>Ubah laman utama</h1><p>Tak perlu pandai coding. Pilih bahagian, buat perubahan, kemudian simpan.</p></div><a class="btn btn-outline" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat laman ↗</a></div>
<div class="cms-help"><strong>Cara paling mudah</strong><ol><li>Klik bahagian yang mahu diubah.</li><li>Tukar tulisan, gambar atau warna.</li><li>Tekan “Simpan semua perubahan”, kemudian pilih “Lihat laman” untuk semak.</li></ol><span>Untuk tambah atau ubah harga pakej, pergi ke <a href="{{ route('packages.index') }}">Pakej Rawatan</a>.</span></div>
<details class="panel cms-section cms-history"><summary><span class="cms-step">↶</span><span><strong>Sejarah simpanan</strong><small>Pulihkan perubahan lama jika perlu. Biasanya tak perlu buka bahagian ini.</small></span><b>⌄</b></summary><div class="cms-history-list">@forelse($revisions as $revision)<form method="post" action="{{ route('landing-page.restore', $revision) }}" onsubmit="return confirm('Pulihkan landing page ke versi ini? Perubahan semasa akan disimpan sebagai versi sejarah.');">@csrf<span>{{ $revision->created_at->format('d/m/Y, H:i') }}</span><button type="submit" class="btn btn-outline">Pulihkan versi ini</button></form>@empty<div class="cms-history-empty">Belum ada versi lama. Selepas perubahan disimpan, versi sebelum simpanan akan muncul di sini.</div>@endforelse</div></details>
<form id="landing-page-form" method="post" action="{{ route('landing-page.update') }}" class="cms-form">
    @csrf
    <input type="hidden" data-upload-sign-url="{{ route('media.sign') }}">
    @method('PUT')
    <details class="panel cms-section" open>
        <summary><span class="cms-step">01</span><span><strong>Nama & logo</strong><small>Ubah nama, logo, latar kepala laman dan warnanya.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Nama perniagaan<input name="brand_name" value="{{ old('brand_name', $landingPage['brand_name']) }}" required></label>
            <label>Ayat kecil di bawah nama<input name="brand_tagline" value="{{ old('brand_tagline', $landingPage['brand_tagline']) }}"></label>
            <div class="cms-upload">
                <label>Gambar logo (pilihan)<input type="file" accept="image/png,image/jpeg,image/webp" data-supabase-image-upload data-context="logo_image" data-target="logo_image_path" data-status="logo-upload-status"><input type="hidden" name="logo_image_path" value="{{ $landingPage['logo_image'] }}"><span id="logo-upload-status" class="upload-status" role="status">Pilih gambar, tunggu sehingga tertulis upload berjaya, kemudian simpan.</span><small>PNG, JPG atau WebP · maksimum 2 MB</small></label>
                @if($landingPage['logo_image'])
                    <div class="cms-image-preview"><img src="{{ \App\Models\SupabaseStorage::publicUrl($landingPage['logo_image']) }}" alt="Logo semasa"><label class="check-label"><input type="checkbox" name="remove_logo_image" value="1"> Buang logo dan guna huruf Y</label></div>
                @endif
            </div>
        </div>
        @include('landing-page._section-design', ['section' => 'header', 'sectionLabel' => 'Kepala laman'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Nama perniagaan' => 'brand_name', 'Tagline' => 'brand_tagline', 'Pautan navigasi' => 'navigation_links', 'Teks butang navigasi' => 'navigation_button_text']])
        <div class="cms-text-color-settings">
            <strong>Warna utama seluruh laman</strong>
            <div class="cms-fields cms-colors">
                @foreach(['deep'=>'Hijau gelap','green'=>'Warna utama','accent'=>'Warna lembut','cream'=>'Warna latar laman','paper'=>'Warna kad'] as $color => $label)
                    <label>{{ $label }}<span class="color-input"><input type="color" name="theme[{{ $color }}]" value="{{ old('theme.'.$color, $landingPage['theme'][$color]) }}"><input class="hex-value" value="{{ old('theme.'.$color, $landingPage['theme'][$color]) }}" readonly></span></label>
                @endforeach
            </div>
        </div>
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">02</span><span><strong>Muka depan laman</strong><small>Ubah ayat, gambar dan warna muka depan.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil di atas tajuk<input name="hero_eyebrow" value="{{ old('hero_eyebrow', $landingPage['hero_eyebrow']) }}" required></label>
            <label>Tajuk pertama<input name="hero_title" value="{{ old('hero_title', $landingPage['hero_title']) }}" required></label>
            <label>Tajuk kedua<input name="hero_highlight" value="{{ old('hero_highlight', $landingPage['hero_highlight']) }}" required></label>
            <label class="span-2">Ayat penerangan<textarea name="hero_description" rows="3" required>{{ old('hero_description', $landingPage['hero_description']) }}</textarea></label>
        </div>
        @include('landing-page._section-design', ['section' => 'hero', 'sectionLabel' => 'Muka depan'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'hero_eyebrow', 'Tajuk utama' => 'hero_title', 'Sorotan tajuk' => 'hero_highlight', 'Penerangan' => 'hero_description']])
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">03</span><span><strong>Tentang kami</strong><small>Ubah cerita, gambar dan warna bahagian ini.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil bahagian<input name="about_eyebrow" value="{{ old('about_eyebrow', $landingPage['about_eyebrow']) }}" required></label>
            <label>Tajuk bahagian<input name="about_title" value="{{ old('about_title', $landingPage['about_title']) }}" required></label>
            <label class="span-2">Ayat penerangan<textarea name="about_description" rows="3" required>{{ old('about_description', $landingPage['about_description']) }}</textarea></label>
            <label>Kelebihan pertama — tajuk<input name="about_point_one_title" value="{{ old('about_point_one_title', $landingPage['about_point_one_title']) }}" required></label>
            <label>Kelebihan pertama — ayat<input name="about_point_one_body" value="{{ old('about_point_one_body', $landingPage['about_point_one_body']) }}" required></label>
            <label>Kelebihan kedua — tajuk<input name="about_point_two_title" value="{{ old('about_point_two_title', $landingPage['about_point_two_title']) }}" required></label>
            <label>Kelebihan kedua — ayat<input name="about_point_two_body" value="{{ old('about_point_two_body', $landingPage['about_point_two_body']) }}" required></label>
            <div class="cms-upload">
                <label>Gambar hiasan di sebelah tulisan (pilihan)<input type="file" accept="image/png,image/jpeg,image/webp" data-supabase-image-upload data-context="about_image" data-target="about_image_path" data-status="about-upload-status"><input type="hidden" name="about_image_path" value="{{ $landingPage['about_image'] }}"><span id="about-upload-status" class="upload-status" role="status">Pilih gambar, tunggu sehingga tertulis upload berjaya, kemudian simpan.</span><small>PNG, JPG atau WebP · maksimum 2 MB</small></label>
                @if($landingPage['about_image'])
                    <div class="cms-image-preview"><img src="{{ \App\Models\SupabaseStorage::publicUrl($landingPage['about_image']) }}" alt="Gambar hiasan semasa"><label class="check-label"><input type="checkbox" name="remove_about_image" value="1"> Buang gambar hiasan</label></div>
                @endif
            </div>
        </div>
        @include('landing-page._section-design', ['section' => 'about', 'sectionLabel' => 'Tentang kami'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'about_eyebrow', 'Tajuk' => 'about_title', 'Penerangan' => 'about_description', 'Tajuk kelebihan 1' => 'about_point_one_title', 'Penerangan kelebihan 1' => 'about_point_one_body', 'Tajuk kelebihan 2' => 'about_point_two_title', 'Penerangan kelebihan 2' => 'about_point_two_body']])
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">04</span><span><strong>Pakej rawatan</strong><small>Ubah ayat dan warna pengenalan pakej. Harga dan gambar di menu Pakej Rawatan.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil bahagian<input name="packages_eyebrow" value="{{ old('packages_eyebrow', $landingPage['packages_eyebrow']) }}" required></label>
            <label>Tajuk bahagian<input name="packages_title" value="{{ old('packages_title', $landingPage['packages_title']) }}" required></label>
            <label class="span-2">Ayat penerangan<textarea name="packages_description" rows="3" required>{{ old('packages_description', $landingPage['packages_description']) }}</textarea></label>
            <div class="cms-tip span-2">Untuk tambah atau ubah harga dan gambar pakej, buka <a href="{{ route('packages.index') }}">Pakej Rawatan ↗</a>.</div>
        </div>
        @include('landing-page._section-design', ['section' => 'packages', 'sectionLabel' => 'Pakej rawatan'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'packages_eyebrow', 'Tajuk' => 'packages_title', 'Penerangan' => 'packages_description']])
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">05</span><span><strong>Petikan</strong><small>Ubah tulisan, latar dan warna petikan.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil petikan<input name="quote_eyebrow" value="{{ old('quote_eyebrow', $landingPage['quote_eyebrow']) }}" required></label>
            <label class="span-2">Petikan<textarea name="quote" rows="2" required>{{ old('quote', $landingPage['quote']) }}</textarea></label>
        </div>
        @include('landing-page._section-design', ['section' => 'quote', 'sectionLabel' => 'Petikan'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'quote_eyebrow', 'Petikan' => 'quote']])
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">06</span><span><strong>Cara tempahan</strong><small>Ubah langkah, latar dan warna bahagian ini.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil langkah<input name="steps_eyebrow" value="{{ old('steps_eyebrow', $landingPage['steps_eyebrow']) }}" required></label>
            <label>Tajuk langkah<input name="steps_title" value="{{ old('steps_title', $landingPage['steps_title']) }}" required></label>
            @foreach([1, 2, 3] as $step)
                @php($stepWord = ['one', 'two', 'three'][$step - 1])
                <label>Langkah {{ $step }} — tajuk<input name="step_{{ $stepWord }}_title" value="{{ old('step_'.$stepWord.'_title', $landingPage['step_'.$stepWord.'_title']) }}" required></label>
                <label>Langkah {{ $step }} — ayat<input name="step_{{ $stepWord }}_body" value="{{ old('step_'.$stepWord.'_body', $landingPage['step_'.$stepWord.'_body']) }}" required></label>
            @endforeach
        </div>
        @include('landing-page._section-design', ['section' => 'steps', 'sectionLabel' => 'Cara tempahan'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'steps_eyebrow', 'Tajuk' => 'steps_title', 'Langkah 1 — tajuk' => 'step_one_title', 'Langkah 1 — ayat' => 'step_one_body', 'Langkah 2 — tajuk' => 'step_two_title', 'Langkah 2 — ayat' => 'step_two_body', 'Langkah 3 — tajuk' => 'step_three_title', 'Langkah 3 — ayat' => 'step_three_body']])
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">07</span><span><strong>Borang tempahan</strong><small>Ubah tulisan, latar, warna dan soalan pelanggan.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label>Ayat kecil bahagian<input name="booking_eyebrow" value="{{ old('booking_eyebrow', $landingPage['booking_eyebrow']) }}" required></label>
            <label>Tajuk bahagian<input name="booking_title" value="{{ old('booking_title', $landingPage['booking_title']) }}" required></label>
            <label class="span-2">Ayat penerangan<textarea name="booking_description" rows="2" required>{{ old('booking_description', $landingPage['booking_description']) }}</textarea></label>
            <label class="span-2">Nota untuk pelanggan<textarea name="booking_note" rows="2" required>{{ old('booking_note', $landingPage['booking_note']) }}</textarea></label>
            <label>Ayat pada butang hantar<input name="booking_button" value="{{ old('booking_button', $landingPage['booking_button']) }}" required></label>
        </div>
        @include('landing-page._section-design', ['section' => 'booking', 'sectionLabel' => 'Tempahan'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Teks kecil' => 'booking_eyebrow', 'Tajuk' => 'booking_title', 'Penerangan' => 'booking_description', 'Nota' => 'booking_note', 'Teks butang' => 'booking_button']])
        <div class="cms-fields cms-fields-stack">
            <div class="cms-tip">Bahagian ini pilihan. Tambah soalan seperti alamat atau umur hanya jika memang diperlukan.</div>
            <div id="custom-fields-list">
                @foreach(old('form_fields', $landingPage['form_fields']) as $index => $field)
                    <div class="custom-field-card">
                        <div class="custom-field-heading"><strong>Soalan tambahan</strong><button type="button" class="danger-link remove-custom-field">Buang soalan</button></div>
                        <input type="hidden" name="form_fields[{{ $index }}][key]" value="{{ $field['key'] }}">
                        <div class="cms-fields">
                            <label>Soalan<input name="form_fields[{{ $index }}][label]" value="{{ $field['label'] }}" required></label>
                            <label>Jenis jawapan<select name="form_fields[{{ $index }}][type]" class="custom-field-type">@foreach(['text'=>'Teks ringkas','email'=>'Emel','tel'=>'Nombor telefon','number'=>'Nombor','date'=>'Tarikh','textarea'=>'Jawapan panjang','select'=>'Senarai pilihan','checkbox'=>'Kotak pilihan'] as $type => $label)<option value="{{ $type }}" @selected($field['type'] === $type)>{{ $label }}</option>@endforeach</select></label>
                            <label>Contoh jawapan (pilihan)<input name="form_fields[{{ $index }}][placeholder]" value="{{ $field['placeholder'] ?? '' }}"></label>
                            <label class="check-label"><input type="checkbox" name="form_fields[{{ $index }}][required]" value="1" @checked($field['required'])> Wajib dijawab</label>
                            <label class="field-options {{ $field['type'] === 'select' ? '' : 'hidden' }} span-2">Pilihan (satu pilihan setiap baris)<textarea name="form_fields[{{ $index }}][options]" rows="3">{{ implode("\n", $field['options'] ?? []) }}</textarea></label>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline" id="add-custom-field">+ Tambah soalan</button>
        </div>
    </details>

    <details class="panel cms-section">
        <summary><span class="cms-step">08</span><span><strong>Bahagian bawah laman</strong><small>Ubah ayat, latar dan warna bahagian bawah.</small></span><b>⌄</b></summary>
        <div class="cms-fields">
            <label class="span-2">Ayat penutup<input name="footer_description" value="{{ old('footer_description', $landingPage['footer_description']) }}" required></label>
        </div>
        @include('landing-page._section-design', ['section' => 'footer', 'sectionLabel' => 'Bahagian bawah'])
        @include('landing-page._text-colors', ['textColorLabels' => ['Penerangan' => 'footer_description', 'Pautan' => 'footer_links', 'Tajuk' => 'footer_headings']])
    </details>
    <div class="cms-savebar"><a class="btn btn-outline" href="{{ route('landing-page.edit') }}" title="Batalkan perubahan yang belum disimpan">↶ Batal perubahan</a><a class="btn btn-outline" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat laman</a><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
</form>
<template id="custom-field-template"><div class="custom-field-card"><div class="custom-field-heading"><strong>Medan tambahan</strong><button type="button" class="danger-link remove-custom-field">Buang medan</button></div><input type="hidden" data-field="key"><div class="cms-fields"><label>Label medan<input data-field="label" required placeholder="Contoh: Alamat"></label><label>Jenis medan<select data-field="type" class="custom-field-type"><option value="text">Teks ringkas</option><option value="email">Emel</option><option value="tel">Nombor telefon</option><option value="number">Nombor</option><option value="date">Tarikh</option><option value="textarea">Jawapan panjang</option><option value="select">Senarai pilihan</option><option value="checkbox">Kotak pilihan</option></select></label><label>Petunjuk ringkas<input data-field="placeholder" placeholder="Contoh: Tulis jawapan anda"></label><label class="check-label"><input data-field="required" type="checkbox" value="1"> Wajib diisi</label><label class="field-options hidden span-2">Pilihan (satu pilihan setiap baris)<textarea data-field="options" rows="3" placeholder="Pagi&#10;Petang"></textarea></label></div></div></template>
<script src="{{ asset('js/landing-page-editor.js') }}" defer></script>
@endsection
