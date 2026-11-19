<!doctype html>
<html lang="ms">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Log Masuk · YUSTERAPI</title><link rel="stylesheet" href="{{ asset('css/yusterapi.css') }}"></head>
<body class="login-page">
    <div class="login-panel">
        <div class="login-visual"><div class="login-visual-inner"><div class="brand brand-light"><span class="brand-mark">Y</span><span><strong>YUSTERAPI</strong><small>Customer Management</small></span></div><div><span class="eyebrow light">PENGURUSAN YANG LEBIH TENANG</span><h1>Setiap pelanggan,<br><em>dijaga rapi.</em></h1><p>Satu ruang untuk tempahan, sejarah kesihatan, rawatan dan bayaran — tersusun untuk pasukan anda.</p></div><div class="visual-foot">YUSTERAPI · RUANG STAF</div></div></div>
        <div class="login-form-wrap"><div class="login-form"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2>Log masuk ke akaun anda</h2><p>Masukkan butiran staf untuk meneruskan.</p>@if($errors->any())<div class="alert error" role="alert">{{ $errors->first() }}</div>@endif<form method="post" action="{{ route('login.submit') }}">@csrf<label>Alamat emel<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="nama@contoh.com"></label><label>Kata laluan<input type="password" name="password" autocomplete="current-password" required placeholder="••••••••••••"></label><button class="btn btn-primary btn-wide" type="submit">Log Masuk <span>→</span></button></form><small class="login-help">Akses untuk staf YUSTERAPI sahaja. <a href="{{ route('home') }}">Lihat laman awam →</a></small></div></div>
    </div>
</body>
</html>
