@extends('layouts.auth', ['tabs' => [['Siswa', route('siswa.login'), true], ['Guru', route('guru.login'), false]], 'switch' => ['Masuk guru', route('guru.login')]])
@section('title', 'Masuk Siswa')
@section('content')
<p class="-mt-3 mb-5 text-sm text-slate-500">Tanpa email atau password. Minta kode kelas dan nomor peserta ke gurumu.</p>
<form method="POST" action="{{ route('siswa.login') }}" class="space-y-4">@csrf
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="user-round" class="h-4 w-4"></i></span>
    <input name="full_name" value="{{ old('full_name') }}" class="pill-input" placeholder="Nama lengkap" required autofocus></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="school" class="h-4 w-4"></i></span>
    <input name="class_name" value="{{ old('class_name') }}" class="pill-input" placeholder="Kelas (mis. X DKV 1)" required></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="hash" class="h-4 w-4"></i></span>
    <input name="participant_number" value="{{ old('participant_number') }}" class="pill-input" placeholder="Nomor peserta" required></label>
  <label class="pill-wrap"><span class="pill-ico"><i data-lucide="key-round" class="h-4 w-4"></i></span>
    <input name="class_code" value="{{ old('class_code') }}" class="pill-input uppercase" placeholder="Kode kelas" required></label>
  <div class="flex justify-end pt-1"><button class="pill-btn">Masuk</button></div>
</form>
@endsection