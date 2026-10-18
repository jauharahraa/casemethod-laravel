@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Dashboard Guru')
@section('content')
@php
  $tiles = [
    ['Mata pelajaran', $stats['subjects'], 'book-open', 'guru.subjects', '#DCEBE2', '#2F6772'],
    ['Kelas', $stats['classrooms'], 'users', 'guru.classrooms', '#FBD9DA', '#27153D'],
    ['Siswa', $stats['students'], 'user-round', 'guru.classrooms', '#CFE3D8', '#5E4B73'],
    ['Kasus', $stats['cases'], 'file-text', 'guru.cases', '#FFE0E1', '#27153D'],
  ];
  $tints = ['#DCEBE2', '#FBD9DA', '#CFE3D8', '#FFE0E1'];
  $inks  = ['#2F6772', '#27153D', '#5E4B73', '#27153D'];
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#FFEBED] via-[#F6B6B7] to-[#A6C9B6] p-6 sm:p-10">
  <div class="pointer-events-none absolute -bottom-32 -right-20 h-80 w-80 rounded-full bg-[#F6B6B7]/60"></div>
  <div class="relative grid items-center gap-6 md:grid-cols-5">
    <div class="md:col-span-3">
      <span class="inline-block rounded-full border border-[#A6C9B6] bg-white/60 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#2F6772]">Ruang guru</span>
      <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#27153D] sm:text-4xl">Halo, {{ auth()->user()->name }} 👋</h1>
      <p class="mt-3 max-w-md text-[#5E4B73]">Kelola kelas, materi, dan kasus dari satu tempat.
        @if($ungraded > 0) Ada <b class="text-[#3E828E]">{{ $ungraded }} jawaban</b> yang menunggu penilaianmu. @else Semua jawaban yang masuk sudah dinilai. @endif</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('guru.cases.create') }}" class="btn btn-primary"><i data-lucide="plus" class="h-4 w-4"></i> Kasus baru</a>
        <a href="{{ route('guru.materials.create') }}" class="btn btn-ghost"><i data-lucide="book-marked" class="h-4 w-4"></i> Materi baru</a>
      </div>
    </div>
    <div class="md:col-span-2">@include('partials.art_desk')</div>
  </div>
</section>

{{-- Statistik --}}
<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
  @foreach($tiles as [$l, $n, $i, $r, $bg, $ink])
    <a href="{{ route($r) }}" class="card group transition hover:-translate-y-1 hover:shadow-xl">
      <span class="grid h-12 w-12 place-items-center rounded-2xl transition group-hover:scale-110" style="background: {{ $bg }}; color: {{ $ink }}"><i data-lucide="{{ $i }}" class="h-6 w-6"></i></span>
      <div class="mt-3 font-display text-3xl font-extrabold text-[#27153D]">{{ $n }}</div>
      <div class="text-sm text-[#8B7C9B]">{{ $l }}</div>
    </a>
  @endforeach
  <a href="{{ route('guru.grading', ['filter' => 'ungraded']) }}" class="card group border-[#F6B6B7] bg-gradient-to-br from-[#FFEBED] to-[#FFE0E1] transition hover:-translate-y-1 hover:shadow-xl">
    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-[#3E828E] transition group-hover:scale-110"><i data-lucide="clock" class="h-6 w-6"></i></span>
    <div class="mt-3 font-display text-3xl font-extrabold text-[#27153D]">{{ $ungraded }}</div>
    <div class="text-sm text-[#27153D]">Belum dinilai</div>
  </a>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-5">
  {{-- Kasus terbaru --}}
  <section class="card lg:col-span-3">
    <div class="mb-4 flex items-end justify-between gap-3">
      <div><p class="text-xs font-bold uppercase tracking-widest text-[#3E828E]">Kasus</p><h2 class="font-display text-xl font-bold text-[#27153D]">Kasus terbaru</h2></div>
      <a href="{{ route('guru.cases') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#3E828E] hover:text-[#2F6772]">Semua kasus <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
    </div>
    @forelse($cases as $k => $c)
      <div class="flex items-center gap-3 border-t border-[#FBE4E5] py-3">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl font-display text-lg font-bold" style="background: {{ $tints[$k % 4] }}; color: {{ $inks[$k % 4] }}">{{ mb_strtoupper(mb_substr($c->title, 0, 1)) }}</span>
        <div class="min-w-0 flex-1">
          <a href="{{ route('guru.cases.edit', $c) }}" class="block truncate font-semibold text-[#27153D] hover:text-[#3E828E]">{{ $c->title }}</a>
          <div class="text-xs text-[#8B7C9B]">{{ $c->classroom->name }} · {{ $c->submissions_count }} pengumpulan</div>
        </div>
        <span class="badge {{ $c->isPublished() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $c->isPublished() ? 'Terpublikasi' : 'Draft' }}</span>
      </div>
    @empty
      <p class="rounded-2xl bg-[#FFF5F6] p-4 text-sm text-[#8B7C9B]">Belum ada kasus. Mulai dengan menambah mata pelajaran, lalu kelas, lalu kasus.</p>
    @endforelse
  </section>

  {{-- Aktivitas --}}
  <section class="card lg:col-span-2">
    <p class="text-xs font-bold uppercase tracking-widest text-[#3E828E]">Riwayat</p>
    <h2 class="mb-4 font-display text-xl font-bold text-[#27153D]">Aktivitas terbaru</h2>
    @forelse($recent as $a)
      <div class="relative border-l-2 border-[#F9D3D5] pb-4 pl-5 last:pb-0">
        <span class="absolute -left-[7px] top-1 h-3 w-3 rounded-full bg-gradient-to-br from-[#A6C9B6] to-[#F6B6B7] ring-4 ring-white"></span>
        <div class="text-sm text-[#5E4B73]">{{ $a->description }}</div>
        <div class="text-xs text-[#8B7C9B]">{{ $a->created_at->diffForHumans() }}</div>
      </div>
    @empty
      <p class="rounded-2xl bg-[#FFF5F6] p-4 text-sm text-[#8B7C9B]">Belum ada aktivitas.</p>
    @endforelse
  </section>
</div>
@endsection