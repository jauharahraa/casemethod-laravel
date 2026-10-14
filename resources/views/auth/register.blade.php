@extends('layouts.app')
@section('title', 'Daftar Guru')
@section('content')
<div class="card mx-auto max-w-md">
  <h1 class="mb-4 text-xl font-bold text-blue-900">Daftar Akun Guru</h1>
  <form method="POST" action="{{ route('guru.register') }}" class="space-y-4">@csrf
    <div><label class="label">Nama lengkap</label><input name="name" value="{{ old('name') }}" class="input" required></div>
    <div><label class="label">Username</label><input name="username" value="{{ old('username') }}" class="input" required><p class="mt-1 text-xs text-slate-500">Huruf, angka, strip, underscore. Minimal 4 karakter.</p></div>
    <div><label class="label">Password (min. 8 karakter)</label><input type="password" name="password" class="input" required></div>
    <div><label class="label">Ulangi password</label><input type="password" name="password_confirmation" class="input" required></div>
    <button class="btn btn-primary w-full">Daftar</button>
  </form>
  <p class="mt-4 text-center text-sm text-slate-500">Sudah punya akun? <a class="font-semibold text-blue-600" href="{{ route('guru.login') }}">Masuk</a></p>
</div>
@endsection
