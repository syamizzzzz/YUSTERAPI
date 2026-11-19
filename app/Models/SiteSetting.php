<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(SiteSettingRevision::class)->latest();
    }

    public static function landingPage(): array
    {
        $defaults = [
            'brand_name' => 'YUSTERAPI',
            'brand_tagline' => 'TERAPI & KESEJAHTERAAN',
            'hero_eyebrow' => 'SELAMAT DATANG KE YUSTERAPI',
            'hero_title' => 'Ruang untuk',
            'hero_highlight' => 'kembali kepada diri.',
            'hero_description' => 'Ambil masa untuk diri anda. Di YUSTERAPI, perjalanan rawatan bermula dengan mendengar keperluan anda dan merancang sesi yang terasa lebih peribadi.',
            'about_eyebrow' => 'TENTANG KAMI',
            'about_title' => 'Rawatan yang bermula dengan memahami anda.',
            'about_description' => 'Setiap orang datang dengan cerita dan keperluan yang berbeza. Kami percaya pengalaman yang baik bermula dengan masa untuk mendengar, ruang yang selesa dan langkah rawatan yang jelas.',
            'about_point_one_title' => 'Lebih peribadi',
            'about_point_one_body' => 'Maklumat dan keperluan anda diambil kira sebelum sesi bermula.',
            'about_point_two_title' => 'Lebih teratur',
            'about_point_two_body' => 'Permintaan temu janji direkodkan untuk memudahkan susulan.',
            'packages_eyebrow' => 'PILIHAN RAWATAN',
            'packages_title' => 'Pakej untuk langkah seterusnya.',
            'packages_description' => 'Lihat pilihan rawatan yang tersedia. Maklumat lanjut dan kesesuaian sesi boleh dibincangkan bersama pasukan kami.',
            'quote_eyebrow' => 'PENDEKATAN KAMI',
            'quote' => 'Masa untuk diri sendiri adalah sebahagian daripada cara kita menjaga kesejahteraan.',
            'steps_eyebrow' => 'MUDAH UNTUK BERMULA',
            'steps_title' => 'Tiga langkah, satu permulaan.',
            'step_one_title' => 'Terokai rawatan',
            'step_one_body' => 'Lihat pakej yang tersedia dan tentukan apa yang ingin anda tanyakan.',
            'step_two_title' => 'Hantar permintaan',
            'step_two_body' => 'Berikan nama, nombor telefon dan masa pilihan anda melalui borang ringkas.',
            'step_three_title' => 'Tunggu pengesahan',
            'step_three_body' => 'Pasukan YUSTERAPI akan menghubungi anda untuk mengesahkan butiran sesi.',
            'booking_eyebrow' => 'BUAT TEMU JANJI',
            'booking_title' => 'Mari mulakan perjalanan anda.',
            'booking_description' => 'Hantar permintaan anda di sini. Temu janji hanya disahkan selepas pasukan kami menghubungi anda.',
            'booking_note' => 'Tiada bayaran dibuat melalui borang ini. Kami hanya menggunakan butiran anda untuk menghubungi anda tentang permintaan temu janji.',
            'booking_button' => 'Hantar Permintaan',
            'footer_description' => 'Ruang yang lebih tenang untuk mengurus perjalanan rawatan anda.',
            'logo_image' => null,
            'hero_image' => null,
            'about_image' => null,
            'section_images' => [
                'header' => null,
                'hero' => null,
                'about' => null,
                'packages' => null,
                'quote' => null,
                'steps' => null,
                'booking' => null,
                'footer' => null,
            ],
            'section_design' => [
                'header' => ['background_color' => '#FFFFFF', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'hero' => ['background_color' => '#17685F', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'about' => ['background_color' => '#F9FAF6', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'packages' => ['background_color' => '#EDF3ED', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'quote' => ['background_color' => '#0F554B', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'steps' => ['background_color' => '#FBFCF8', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'booking' => ['background_color' => '#0C4C43', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
                'footer' => ['background_color' => '#092F2B', 'use_background_color' => false, 'overlay_color' => '#000000', 'overlay_opacity' => 0],
            ],
            'theme' => [
                'deep' => '#0D4942',
                'green' => '#17685F',
                'accent' => '#B9DED0',
                'cream' => '#F6F7F1',
                'paper' => '#FFFEFA',
            ],
            'text_colors' => [
                'brand_name' => '#FFFFFF', 'brand_tagline' => '#D3EAE0',
                'hero_eyebrow' => '#BCE3D2', 'hero_title' => '#FFFFFF', 'hero_highlight' => '#B9E4D5', 'hero_description' => '#D3EAE0',
                'about_eyebrow' => '#488B79', 'about_title' => '#173F39', 'about_description' => '#788D80', 'about_point_one_title' => '#173F39', 'about_point_one_body' => '#788D80', 'about_point_two_title' => '#173F39', 'about_point_two_body' => '#788D80',
                'packages_eyebrow' => '#488B79', 'packages_title' => '#173F39', 'packages_description' => '#788D80',
                'quote_eyebrow' => '#BCE3D2', 'quote' => '#E7F3E8',
                'steps_eyebrow' => '#488B79', 'steps_title' => '#173F39', 'step_one_title' => '#173F39', 'step_one_body' => '#788D80', 'step_two_title' => '#173F39', 'step_two_body' => '#788D80', 'step_three_title' => '#173F39', 'step_three_body' => '#788D80',
                'booking_eyebrow' => '#BCE3D2', 'booking_title' => '#FFFFFF', 'booking_description' => '#C5DFD4', 'booking_note' => '#C3DCD0', 'booking_button' => '#FFFFFF',
                'navigation_links' => '#476A5F', 'navigation_button_text' => '#FFFFFF',
                'footer_description' => '#A9C6B8', 'footer_links' => '#B6D0C3', 'footer_headings' => '#6DA997',
            ],
            'form_fields' => [],
        ];

        $saved = static::where('key', 'landing_page')->value('value') ?? [];

        return array_replace_recursive($defaults, $saved);
    }
}
