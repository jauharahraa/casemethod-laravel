@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<section class="mx-auto max-w-3xl py-8 text-center">
  <h1 class="text-3xl font-extrabold text-blue-900 sm:text-4xl">Belajar Case Method dengan Feedback AI</h1>
  <p class="mx-auto mt-3 max-w-xl text-slate-600">Identifikasi masalah, analisis, dan temukan solusi. AI membantumu berpikir lewat feedback formatif; nilai akhir tetap dari gurumu.</p>
  <div class="mt-8 grid gap-4 sm:grid-cols-2">
    <a href="{{ route('siswa.login') }}" class="card text-left transition hover:border-blue-400 hover:shadow-md">
      <i data-lucide="user-round" class="h-8 w-8 text-blue-600"></i>
      <h2 class="mt-3 text-lg font-bold">Masuk sebagai Siswa</h2>
      <p class="text-sm text-slate-500">Nama lengkap, kelas, nomor peserta, dan kode kelas.</p>
    </a>
    <a href="{{ route('guru.login') }}" class="card text-left transition hover:border-blue-400 hover:shadow-md">
      <i data-lucide="presentation" class="h-8 w-8 text-blue-900"></i>
      <h2 class="mt-3 text-lg font-bold">Masuk sebagai Guru</h2>
      <p class="text-sm text-slate-500">Kelola kelas, kasus, rubrik, dan penilaian.</p>
    </a>
  </div>
</section>
@endsection
