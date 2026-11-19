@php
    $design = $landingPage['section_design'][$section];
    $sectionImagePath = $landingPage['section_images'][$section] ?? null;
    $imagePath = $sectionImagePath ?? ($section === 'hero' ? $landingPage['hero_image'] : null);
    $uploadContext = 'section_'.$section;
    $statusId = 'section-'.$section.'-upload-status';
@endphp
<details class="section-design-card">
    <summary class="section-design-summary">
        <span class="cms-step">{{ str_pad((string) $sectionIndex, 2, '0', STR_PAD_LEFT) }}</span>
        <span><strong>{{ $sectionLabel }}</strong><small>Klik di sini untuk ubah gambar atau warna.</small></span>
        <b>⌄</b>
    </summary>
    <div class="section-design-body">
        <div class="cms-fields cms-colors">
            <label>Warna belakang<span class="color-input"><input type="color" form="landing-page-form" name="section_design[{{ $section }}][background_color]" value="{{ old('section_design.'.$section.'.background_color', $design['background_color']) }}"><input class="hex-value" value="{{ old('section_design.'.$section.'.background_color', $design['background_color']) }}" readonly></span></label>
            <label class="check-label section-background-toggle"><input type="checkbox" form="landing-page-form" name="section_design[{{ $section }}][use_background_color]" value="1" @checked(old('section_design.'.$section.'.use_background_color', $design['use_background_color']))> Gunakan warna ini jika tiada gambar</label>
            <label>Warna gelap atas gambar<span class="color-input"><input type="color" form="landing-page-form" name="section_design[{{ $section }}][overlay_color]" value="{{ old('section_design.'.$section.'.overlay_color', $design['overlay_color']) }}"><input class="hex-value" value="{{ old('section_design.'.$section.'.overlay_color', $design['overlay_color']) }}" readonly></span></label>
            <label>Tahap gelap <output data-overlay-value>{{ old('section_design.'.$section.'.overlay_opacity', $design['overlay_opacity']) }}%</output><input type="range" form="landing-page-form" min="0" max="80" step="5" name="section_design[{{ $section }}][overlay_opacity]" value="{{ old('section_design.'.$section.'.overlay_opacity', $design['overlay_opacity']) }}" data-overlay-range></label>
        </div>
        <div class="cms-upload"><label>{{ $section === 'hero' ? 'Gambar di sebelah kanan teks (pilihan)' : 'Gambar latar '.$sectionLabel.' (pilihan)' }}<input type="file" form="landing-page-form" accept="image/png,image/jpeg,image/webp" data-supabase-image-upload data-context="{{ $uploadContext }}" data-target="section_image_paths[{{ $section }}]" data-remove-target="remove_section_images[{{ $section }}]" data-status="{{ $statusId }}"><input type="hidden" form="landing-page-form" name="section_image_paths[{{ $section }}]" value="{{ $sectionImagePath }}"><span id="{{ $statusId }}" class="upload-status" role="status">Pilih PNG, JPG atau WebP sehingga 2 MB. Tunggu sehingga tertulis upload berjaya, kemudian simpan.</span><small>{{ $section === 'hero' ? 'Gambar ini akan dipaparkan di sebelah kanan teks muka depan.' : 'Gambar ini jadi latar bahagian tersebut.' }}</small></label>
            @if($imagePath)<div class="cms-image-preview"><img src="{{ \App\Models\SupabaseStorage::publicUrl($imagePath) }}" alt="Pratonton latar {{ $sectionLabel }}"><label class="check-label"><input type="checkbox" form="landing-page-form" name="remove_section_images[{{ $section }}]" value="1"> Buang gambar latar</label></div>@endif
        </div>
    </div>
</details>
