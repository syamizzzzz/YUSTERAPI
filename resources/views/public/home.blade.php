@php
    $heroImagePath = $landingPage['section_images']['hero'] ?? $landingPage['hero_image'] ?? null;
    $sectionStyle = function (string $section) use ($landingPage): string {
        $design = $landingPage['section_design'][$section];
        $image = $landingPage['section_images'][$section] ?? null;
        $style = '--section-bg:'.$design['background_color'].';--section-overlay:'.$design['overlay_color'].';--section-overlay-opacity:'.$design['overlay_opacity'].'%;';
        if ($image) {
            $style .= '--section-image:url("'.\App\Models\SupabaseStorage::publicUrl($image).'");';
        }
        if ($design['use_background_color'] && ! $image) {
            $style .= 'background:'.$design['background_color'].'!important;';
        }
        return $style;
    };
@endphp
<!doctype html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $landingPage['hero_description'] }}">
    <title>{{ $landingPage['brand_name'] }} · {{ $landingPage['brand_tagline'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2026112002">
    <style>
        :root { --deep: {{ $landingPage['theme']['deep'] }}; --green: {{ $landingPage['theme']['green'] }}; --accent: {{ $landingPage['theme']['accent'] }}; --cream: {{ $landingPage['theme']['cream'] }}; --paper: {{ $landingPage['theme']['paper'] }}; }
        .hero { background: radial-gradient(circle at 78% 37%, color-mix(in srgb, var(--green), white 25%), var(--green) 29%, var(--deep) 69%); }
        .hero h1 em, .booking-copy h2 em { color: var(--accent); }
        .intro { background: var(--cream); }
        .packages-section, .steps-section { background: color-mix(in srgb, var(--cream), var(--green) 5%); }
        .quote-section { background: var(--deep); }
        .booking-section { background: radial-gradient(circle at 3% 100%, var(--green), var(--deep) 63%); }
        .button-dark, .site-nav .nav-cta { background-color: var(--green); }
        .public-package, .booking-form-card { background-color: var(--paper); }
        .hero-photo { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.26; }
        .hero-grid, .booking-grid { position:relative; }
        .about-photo { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
        .intro-art { background:var(--accent); }
        .site-logo .logo-mark { background:var(--green); }
        .site-nav .nav-cta { color:white; }
        .public-form-grid select { width:100%; padding:13px 12px; border:1px solid #dce7dc; border-radius:4px; color:var(--ink); background:#fff; }
        .public-form-grid .check-field { display:flex; align-items:center; gap:10px; }
        .public-form-grid .check-field input { width:auto; }
        .site-header { background-color: var(--section-bg, white); }
        .hero { background-color: var(--section-bg, var(--green)); }
        .intro { background-color: var(--section-bg, var(--cream)); }
        .packages-section { background-color: var(--section-bg, color-mix(in srgb, var(--cream), var(--green) 5%)); }
        .quote-section { background-color: var(--section-bg, var(--deep)); }
        .steps-section { background-color: var(--section-bg, color-mix(in srgb, var(--cream), var(--green) 5%)); }
        .booking-section { background-color: var(--section-bg, var(--deep)); }
        .site-footer { background-color: var(--section-bg, #092f2b); }
        .section-has-background-image { background-image: linear-gradient(color-mix(in srgb, var(--section-overlay, #000) var(--section-overlay-opacity, 0%), transparent), color-mix(in srgb, var(--section-overlay, #000) var(--section-overlay-opacity, 0%), transparent)), var(--section-image) !important; background-position: center !important; background-repeat: no-repeat !important; background-size: cover !important; }
        .site-nav a:not(.nav-cta) { color: {{ $landingPage['text_colors']['navigation_links'] }}; }
        .site-nav .nav-cta { color: {{ $landingPage['text_colors']['navigation_button_text'] }}; }
        .site-footer a { color: {{ $landingPage['text_colors']['footer_links'] }}; }
        .site-footer .footer-heading { color: {{ $landingPage['text_colors']['footer_headings'] }}; }
        @foreach($landingPage['text_colors'] as $textKey => $textColor).text-color-{{ $textKey }}{color:{{ $textColor }}!important;}@endforeach
    </style>
</head>
<body>
    <header class="site-header {{ !empty($landingPage['section_images']['header']) ? 'section-has-background-image' : '' }}" style="{{ $sectionStyle('header') }}"><div class="container header-inner">
        <a class="site-logo" href="{{ route('home') }}" aria-label="{{ $landingPage['brand_name'] }} laman utama">
            @if($landingPage['logo_image'])<img class="logo-mark" src="{{ \App\Models\SupabaseStorage::publicUrl($landingPage['logo_image']) }}" alt="Logo {{ $landingPage['brand_name'] }}">@else<span class="logo-mark">{{ mb_substr($landingPage['brand_name'], 0, 1) }}</span>@endif
            <span><strong class="text-color-brand_name">{{ $landingPage['brand_name'] }}</strong><small class="text-color-brand_tagline">{{ $landingPage['brand_tagline'] }}</small></span>
        </a>
        <nav class="site-nav" aria-label="Navigasi laman"><a href="#tentang">Tentang</a><a href="#pakej">Pakej Rawatan</a><a href="#cara-tempah">Cara Tempah</a><a class="nav-cta" href="#tempah">Buat Temu Janji <span>→</span></a></nav>
    </div></header>
    <main>
        <section class="hero" style="{{ $sectionStyle('hero') }}"><div class="container hero-grid">
            <div class="hero-copy"><span class="section-kicker text-color-hero_eyebrow"><span class="kicker-line"></span>{{ $landingPage['hero_eyebrow'] }}</span><h1><span class="text-color-hero_title">{{ $landingPage['hero_title'] }}</span><br><em class="text-color-hero_highlight">{{ $landingPage['hero_highlight'] }}</em></h1><p class="text-color-hero_description">{{ $landingPage['hero_description'] }}</p><div class="hero-actions"><a class="button button-light" href="#tempah">Buat Temu Janji <span>→</span></a><a class="hero-link" href="#tentang">Kenali {{ $landingPage['brand_name'] }} <span>↓</span></a></div><div class="hero-smallprint"><span class="line"></span> RUANG TENANG · TEMU JANJI TERATUR</div></div>
            @if($heroImagePath)
                <div class="hero-visual"><img class="hero-image" src="{{ \App\Models\SupabaseStorage::publicUrl($heroImagePath) }}" alt="Gambar muka depan {{ $landingPage['brand_name'] }}"></div>
            @endif
        </div><div class="hero-bottom"><div class="container"><span>01 / {{ $landingPage['brand_name'] }}</span><span>SCROLL UNTUK MENEROKA ↓</span></div></div></section>
        <section class="intro section {{ !empty($landingPage['section_images']['about']) ? 'section-has-background-image' : '' }}" id="tentang" style="{{ $sectionStyle('about') }}"><div class="container intro-grid"><div class="intro-side"><span class="section-kicker dark text-color-about_eyebrow"><span class="kicker-line"></span>{{ $landingPage['about_eyebrow'] }}</span><div class="intro-art" aria-hidden="true">@if($landingPage['about_image'])<img class="about-photo" src="{{ \App\Models\SupabaseStorage::publicUrl($landingPage['about_image']) }}" alt="">@else<div class="art-arch"><div class="art-arch-inner"></div><span>{{ mb_substr($landingPage['brand_name'], 0, 1) }}.</span></div><div class="art-tag">{{ $landingPage['brand_tagline'] }}</div>@endif</div></div><div class="intro-copy"><h2 class="text-color-about_title">{{ $landingPage['about_title'] }}</h2><p class="text-color-about_description">{{ $landingPage['about_description'] }}</p><div class="intro-points"><div><span>01</span><strong class="text-color-about_point_one_title">{{ $landingPage['about_point_one_title'] }}</strong><p class="text-color-about_point_one_body">{{ $landingPage['about_point_one_body'] }}</p></div><div><span>02</span><strong class="text-color-about_point_two_title">{{ $landingPage['about_point_two_title'] }}</strong><p class="text-color-about_point_two_body">{{ $landingPage['about_point_two_body'] }}</p></div></div></div></div></section>
        <section class="packages-section section {{ !empty($landingPage['section_images']['packages']) ? 'section-has-background-image' : '' }}" id="pakej" style="{{ $sectionStyle('packages') }}"><div class="container"><div class="section-head"><div><span class="section-kicker dark text-color-packages_eyebrow"><span class="kicker-line"></span>{{ $landingPage['packages_eyebrow'] }}</span><h2 class="text-color-packages_title">{{ $landingPage['packages_title'] }}</h2></div><p class="text-color-packages_description">{{ $landingPage['packages_description'] }}</p></div><div class="public-packages">
            @forelse($packages as $package)
                <article class="public-package {{ $package->background_image ? 'public-package-with-image' : '' }}">
                    @if($package->background_image)
                        <img class="public-package-cover" src="{{ \App\Models\SupabaseStorage::publicUrl($package->background_image) }}" alt="{{ $package->name }}" loading="lazy">
                        <div class="public-package-details">
                    @endif
                    <div class="package-icon" aria-hidden="true">✳</div>
                    <span class="package-index" style="color: {{ $package->package_label_color ?: '#17685F' }}">PAKEJ {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 style="color: {{ $package->name_color ?: '#173F39' }}">{{ $package->name }}</h3>
                    <p style="color: {{ $package->description_color ?: '#788D80' }}">{{ $package->description ?: 'Hubungi kami untuk mengetahui lebih lanjut tentang pakej ini.' }}</p>
                    <div class="public-package-foot"><span style="color: {{ $package->duration_color ?: '#89A093' }}">{{ $package->duration_minutes ? $package->duration_minutes.' minit' : 'Tempoh mengikut sesi' }}</span><strong style="color: {{ $package->price_color ?: '#17685F' }}">RM {{ number_format($package->price, 2) }}</strong></div>
                    @if($package->background_image)
                        </div>
                    @endif
                </article>
            @empty
                <article class="public-package package-placeholder"><div class="package-icon" aria-hidden="true">✳</div><span class="package-index">PAKEJ RAWATAN</span><h3>Rawatan mengikut keperluan anda</h3><p>Senarai pakej sedang dikemas kini. Hantar permintaan temu janji untuk berbincang dengan pasukan kami.</p><a href="#tempah">Tanya tentang rawatan →</a></article>
            @endforelse
        </div></div></section>
        <section class="quote-section {{ !empty($landingPage['section_images']['quote']) ? 'section-has-background-image' : '' }}" style="{{ $sectionStyle('quote') }}"><div class="container quote-grid"><span>{{ mb_substr($landingPage['brand_name'], 0, 1) }}</span><div><span class="section-kicker text-color-quote_eyebrow">{{ $landingPage['quote_eyebrow'] }}</span><blockquote class="text-color-quote">“{{ $landingPage['quote'] }}”</blockquote></div></div></section>
        <section class="steps-section section {{ !empty($landingPage['section_images']['steps']) ? 'section-has-background-image' : '' }}" id="cara-tempah" style="{{ $sectionStyle('steps') }}"><div class="container"><div class="section-head"><div><span class="section-kicker dark text-color-steps_eyebrow"><span class="kicker-line"></span>{{ $landingPage['steps_eyebrow'] }}</span><h2 class="text-color-steps_title">{{ $landingPage['steps_title'] }}</h2></div></div><div class="steps-grid">@foreach(['one','two','three'] as $index => $word)<div class="step"><span class="step-no">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><div class="step-icon">{{ ['◇','◧','✳'][$index] }}</div><h3 class="text-color-step_{{ $word }}_title">{{ $landingPage['step_'.$word.'_title'] }}</h3><p class="text-color-step_{{ $word }}_body">{{ $landingPage['step_'.$word.'_body'] }}</p></div>@endforeach</div></div></section>
        <section class="booking-section section {{ !empty($landingPage['section_images']['booking']) ? 'section-has-background-image' : '' }}" id="tempah" style="{{ $sectionStyle('booking') }}"><div class="container booking-grid"><div class="booking-copy"><span class="section-kicker text-color-booking_eyebrow"><span class="kicker-line"></span>{{ $landingPage['booking_eyebrow'] }}</span><h2 class="text-color-booking_title">{{ $landingPage['booking_title'] }}</h2><p class="text-color-booking_description">{{ $landingPage['booking_description'] }}</p><div class="booking-note"><span>✳</span><p class="text-color-booking_note">{{ $landingPage['booking_note'] }}</p></div></div><div class="booking-form-card">
            @if(session('request_sent'))<div class="public-success" role="status"><strong>Permintaan diterima.</strong><span>Terima kasih. Pasukan kami akan menghubungi anda untuk susulan.</span></div>@endif
            @if($errors->any())<div class="public-error" role="alert"><strong>Sila semak borang:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div id="appointment-request-feedback" role="status" aria-live="polite" hidden></div><form id="appointment-request-form" method="post" action="{{ route('appointment-requests.store') }}">@csrf<div class="form-card-head"><span>PERMINTAAN TEMU JANJI</span><b>→</b></div><div class="public-form-grid">
                <label>Nama penuh <input name="name" value="{{ old('name') }}" maxlength="255" required placeholder="Nama anda"></label><label>Nombor telefon <input name="phone" type="tel" value="{{ old('phone') }}" maxlength="30" required placeholder="01X-XXXXXXX"></label>
                <label class="full">Pakej rawatan <select name="treatment_package_id"><option value="">Bincang dengan staf dahulu</option>@foreach($packages as $package)<option value="{{ $package->id }}" @selected((string) old('treatment_package_id') === (string) $package->id)>{{ $package->name }} · RM {{ number_format($package->price, 2) }}</option>@endforeach</select></label>
                <label class="full">Tarikh & masa pilihan <input name="preferred_at" type="datetime-local" value="{{ old('preferred_at') }}" min="{{ now()->addDay()->format('Y-m-d\TH:i') }}"></label><label class="full">Catatan ringkas <textarea name="message" rows="3" maxlength="2000" placeholder="Apa yang ingin anda tanyakan? (pilihan)">{{ old('message') }}</textarea></label>
                @foreach($landingPage['form_fields'] as $field)<label class="{{ in_array($field['type'], ['textarea','select']) ? 'full' : '' }} {{ $field['type'] === 'checkbox' ? 'check-field' : '' }}">{{ $field['label'] }}
                    @if($field['type'] === 'textarea')<textarea name="custom_fields[{{ $field['key'] }}]" rows="3" placeholder="{{ $field['placeholder'] }}" @required($field['required'])>{{ old('custom_fields.'.$field['key']) }}</textarea>
                    @elseif($field['type'] === 'select')<select name="custom_fields[{{ $field['key'] }}]" @required($field['required'])><option value="">Pilih satu</option>@foreach($field['options'] as $option)<option value="{{ $option }}" @selected(old('custom_fields.'.$field['key']) === $option)>{{ $option }}</option>@endforeach</select>
                    @elseif($field['type'] === 'checkbox')<input type="checkbox" name="custom_fields[{{ $field['key'] }}]" value="1" @checked(old('custom_fields.'.$field['key'])) @required($field['required'])>
                    @else<input type="{{ $field['type'] }}" name="custom_fields[{{ $field['key'] }}]" value="{{ old('custom_fields.'.$field['key']) }}" placeholder="{{ $field['placeholder'] }}" @required($field['required'])>
                    @endif
                </label>@endforeach
                <div class="trap" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
            </div><button class="button button-dark" type="submit"><span class="text-color-booking_button">{{ $landingPage['booking_button'] }}</span> <span>→</span></button><small>Dengan menghantar borang, anda bersetuju dihubungi mengenai permintaan ini.</small></form>
        </div></div></section>
    </main>
    <footer class="site-footer {{ !empty($landingPage['section_images']['footer']) ? 'section-has-background-image' : '' }}" style="{{ $sectionStyle('footer') }}"><div class="container footer-top"><div><a class="site-logo footer-logo" href="{{ route('home') }}"><span class="logo-mark">{{ mb_substr($landingPage['brand_name'], 0, 1) }}</span><span><strong class="text-color-brand_name">{{ $landingPage['brand_name'] }}</strong><small class="text-color-brand_tagline">{{ $landingPage['brand_tagline'] }}</small></span></a><p class="text-color-footer_description">{{ $landingPage['footer_description'] }}</p></div><div><span class="footer-heading">TEROKAI</span><a href="#tentang">Tentang</a><a href="#pakej">Pakej Rawatan</a><a href="#cara-tempah">Cara Tempah</a></div><div><span class="footer-heading">BERMULA</span><a href="#tempah">Buat Temu Janji</a><a href="{{ route('login') }}">Log Masuk Staf</a></div></div><div class="container footer-bottom"><span>© {{ now()->year }} {{ $landingPage['brand_name'] }}. Hak cipta terpelihara.</span><span>Direka untuk perjalanan yang lebih teratur.</span></div></footer>
    <script src="{{ asset('js/appointment-request.js') }}" defer></script>
</body>
</html>
