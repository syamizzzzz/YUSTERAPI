<div class="form-grid">
    <label>Nama pakej <input name="name" value="{{ old('name', $package->name ?? '') }}" required placeholder="Contoh: Rawatan Bekam Asas"></label>
    <label>Harga (RM) <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $package->price ?? '') }}" required placeholder="0.00"></label>
    <label>Tempoh (minit) <input type="number" name="duration_minutes" min="1" max="1440" value="{{ old('duration_minutes', $package->duration_minutes ?? '') }}" placeholder="Contoh: 60"></label>
    <label class="check-label"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true))> Pakej aktif</label>
    <label class="span-2">Penerangan <textarea name="description" rows="3" placeholder="Butiran rawatan yang ditawarkan">{{ old('description', $package->description ?? '') }}</textarea></label>
    <section class="span-2 package-text-colors" aria-label="Warna teks pakej">
        <strong>Warna teks kad ini</strong>
        <small>Pilih warna berasingan untuk nama, penerangan, tempoh dan harga.</small>
        <div class="package-color-grid">
            <label>Label PAKEJ 01 <input type="color" name="package_label_color" value="{{ old('package_label_color', $package->package_label_color ?? '#17685F') }}"></label>
            <label>Nama pakej <input type="color" name="name_color" value="{{ old('name_color', $package->name_color ?? '#173F39') }}"></label>
            <label>Penerangan <input type="color" name="description_color" value="{{ old('description_color', $package->description_color ?? '#788D80') }}"></label>
            <label>Tempoh <input type="color" name="duration_color" value="{{ old('duration_color', $package->duration_color ?? '#89A093') }}"></label>
            <label>Harga <input type="color" name="price_color" value="{{ old('price_color', $package->price_color ?? '#17685F') }}"></label>
        </div>
    </section>
    <label class="span-2 package-image-field">Gambar latar kad <small>PNG, JPG atau WebP · maksimum 2 MB</small><input type="file" accept="image/png,image/jpeg,image/webp" data-supabase-image-upload data-context="package" data-target="background_image_path" data-status="package-image-status">
        <input type="hidden" name="background_image_path" value="{{ old('background_image_path', $package->background_image ?? '') }}">
        <span id="package-image-status" class="upload-status" role="status">Pilih gambar untuk upload ke Supabase; tunggu mesej berjaya sebelum menyimpan.</span>
        @if($package && $package->background_image)
            <span class="package-current-image"><img src="{{ \App\Models\SupabaseStorage::publicUrl($package->background_image) }}" alt="Latar pakej semasa"><label class="check-label"><input type="checkbox" name="remove_background_image" value="1"> Buang gambar latar</label></span>
        @endif
    </label>
</div>
