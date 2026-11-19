<div class="cms-text-color-settings">
    <strong>Warna tulisan bahagian ini</strong>
    <div class="cms-fields cms-colors">
        @foreach($textColorLabels as $label => $key)
            <label>{{ $label }}<span class="color-input"><input type="color" name="text_colors[{{ $key }}]" value="{{ old('text_colors.'.$key, $landingPage['text_colors'][$key]) }}"><input class="hex-value" value="{{ old('text_colors.'.$key, $landingPage['text_colors'][$key]) }}" readonly></span></label>
        @endforeach
    </div>
</div>
