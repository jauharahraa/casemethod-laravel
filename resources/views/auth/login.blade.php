@extends('layouts.auth', ['tabs' => [['Masuk', route('guru.login'), true], ['Daftar', route('guru.register'), false]], 'switch' => ['Masuk siswa', route('siswa.login')]])
@section('title', 'Masuk Guru')
@section('content')
<form method="POST" action="{{ route('guru.login') }}" class="space-y-5">@csrf
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="user-round" class="h-4 w-4"></i></span>
    <input name="username" value="{{ old('username') }}" class="pill-input" placeholder="Username" required autofocus></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="lock" class="h-4 w-4"></i></span>
    <input type="password" name="password" class="pill-input" placeholder="Password" required></label>
  <div class="flex items-center justify-between gap-4 pt-1">
    <label class="flex items-center gap-2 text-sm text-[#5f7cae]"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded accent-[#738fbd]"> Ingat saya</label>
    <button class="pill-btn">Masuk</button>
  </div>
</form>
<p class="mt-8 text-center text-sm text-slate-500">Belum punya akun? <a class="font-semibold text-[#5f7cae] hover:underline" href="{{ route('guru.register') }}">Daftar sebagai guru</a></p>
@endsection