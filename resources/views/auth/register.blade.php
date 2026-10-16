@extends('layouts.auth', ['tabs' => [['Masuk', route('guru.login'), false], ['Daftar', route('guru.register'), true]], 'switch' => ['Masuk siswa', route('siswa.login')]])
@section('title', 'Daftar Guru')
@section('content')
<form method="POST" action="{{ route('guru.register') }}" class="space-y-4">@csrf
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="user-round" class="h-4 w-4"></i></span>
    <input name="name" value="{{ old('name') }}" class="pill-input" placeholder="Nama lengkap" required autofocus></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="at-sign" class="h-4 w-4"></i></span>
    <input name="username" value="{{ old('username') }}" class="pill-input" placeholder="Username (min. 4 karakter)" required></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="lock" class="h-4 w-4"></i></span>
    <input type="password" name="password" class="pill-input" placeholder="Password (min. 8 karakter)" required></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="shield-check" class="h-4 w-4"></i></span>
    <input type="password" name="password_confirmation" class="pill-input" placeholder="Ulangi password" required></label>
  <div class="flex justify-end pt-1"><button class="pill-btn">Daftar</button></div>
</form>
<p class="mt-8 text-center text-sm text-slate-500">Sudah punya akun? <a class="font-semibold text-[#5f7cae] hover:underline" href="{{ route('guru.login') }}">Masuk</a></p>
@endsection