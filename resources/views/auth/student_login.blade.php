@extends('layouts.app')
@section('title', 'Masuk Siswa')
@section('content')
<div class="card mx-auto max-w-md">
  <h1 class="mb-1 text-xl font-bold text-blue-900">Masuk Siswa</h1>
  <p class="mb-4 text-sm text-slate-500">Tidak perlu email atau password. Minta kode kelas dan nomor peserta ke gurumu.</p>
  <form method="POST" action="{{ route('siswa.login') }}" class="space-y-4">@csrf
    <div><label class="label">Nama lengkap</label><input name="full_name" value="{{ old('full_name') }}" class="input" required autofocus></div>
    <div><label class="label">Kelas</label><input name="class_name" value="{{ old('class_name') }}" class="input" placeholder="mis. XI IPS 1" required></div>
    <div class="grid grid-cols-2 gap-3">
      <div><label class="label">Nomor peserta</label><input name="participant_number" value="{{ old('participant_number') }}" class="input" required></div>
      <div><label class="label">Kode kelas</label><input name="class_code" value="{{ old('class_code') }}" class="input uppercase" required></div>
    </div>
    <button class="btn btn-primary w-full">Masuk</button>
  </form>
</div>
@endsection
