@extends('layouts.app')
@section('title', 'Masuk Guru')
@section('content')
<div class="card mx-auto max-w-md">
  <h1 class="mb-4 text-xl font-bold text-blue-900">Masuk Guru</h1>
  <form method="POST" action="{{ route('guru.login') }}" class="space-y-4">@csrf
    <div><label class="label">Username</label><input name="username" value="{{ old('username') }}" class="input" required autofocus></div>
    <div><label class="label">Password</label><input type="password" name="password" class="input" required></div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
    <button class="btn btn-primary w-full">Masuk</button>
  </form>
  <p class="mt-4 text-center text-sm text-slate-500">Belum punya akun? <a class="font-semibold text-blue-600" href="{{ route('guru.register') }}">Daftar</a></p>
</div>
@endsection
