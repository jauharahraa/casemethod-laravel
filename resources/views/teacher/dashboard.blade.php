@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Dashboard Guru')
@section('content')
@php
  $tiles = [
    ['Mata pelajaran', $stats['subjects'], 'book-open', 'guru.subjects', '#d9e6ef', '#4f7189'],
    ['Kelas', $stats['classrooms'], 'users', 'guru.classrooms', '#f8dde2', '#a65772'],
    ['Siswa', $stats['students'], 'user-round', 'guru.classrooms', '#e9e4ef', '#5f5577'],
    ['Kasus', $stats['cases'], 'file-text', 'guru.cases', '#f3dfea', '#955d7e'],
  ];
  $tints = ['#d9e6ef', '#f8dde2', '#e9e4ef', '#f3dfea'];
  $inks  = ['#4f7189', '#a65772', '#5f5577', '#955d7e'];
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#e2ecf3] via-[#ece7ee] to-[#f8dedf] p-6 sm:p-10">
  <div class="pointer-events-none absolute -bottom-32 -right-20 h-80 w-80 rounded-full bg-[#eec6c7]/60"></div>
  <div class="relative grid items-center gap-6 md:grid-cols-5">
    <div class="md:col-span-3">
      <span class="inline-block rounded-full border border-[#a8c3d4] bg-white/60 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#4d6a9c]">Ruang guru</span>
      <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#33406b] sm:text-4xl">Halo, {{ auth()->user()->name }} 👋</h1>
      <p class="mt-3 max-w-md text-[#5b5470]">Kelola kelas, materi, dan kasus dari satu tempat.
        @if($ungraded > 0) Ada <b class="text-[#c4708f]">{{ $ungraded }} jawaban</b> yang menunggu penilaianmu. @else Semua jawaban yang masuk sudah dinilai. @endif</p>
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
      <div class="mt-3 font-display text-3xl font-extrabold text-[#33406b]">{{ $n }}</div>
      <div class="text-sm text-[#8a8399]">{{ $l }}</div>
    </a>
  @endforeach
  <a href="{{ route('guru.grading', ['filter' => 'ungraded']) }}" class="card group border-[#f1c4cf] bg-gradient-to-br from-[#fdf1f4] to-[#fbe3e9] transition hover:-translate-y-1 hover:shadow-xl">
    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-[#c4708f] transition group-hover:scale-110"><i data-lucide="clock" class="h-6 w-6"></i></span>
    <div class="mt-3 font-display text-3xl font-extrabold text-[#a65772]">{{ $ungraded }}</div>
    <div class="text-sm text-[#a65772]">Belum dinilai</div>
  </a>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-5">
  {{-- Kasus terbaru --}}
  <section class="card lg:col-span-3">
    <div class="mb-4 flex items-end justify-between gap-3">
      <div><p class="text-xs font-bold uppercase tracking-widest text-[#c4708f]">Kasus</p><h2 class="font-display text-xl font-bold text-[#33406b]">Kasus terbaru</h2></div>
      <a href="{{ route('guru.cases') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#5f7cae] hover:text-[#c4708f]">Semua kasus <i data-lucide="arrow-right" class="h-4 w-4"></i></a>
    </div>
    @forelse($cases as $k => $c)
      <div class="flex items-center gap-3 border-t border-[#f3ecef] py-3">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl font-display text-lg font-bold" style="background: {{ $tints[$k % 4] }}; color: {{ $inks[$k % 4] }}">{{ mb_strtoupper(mb_substr($c->title, 0, 1)) }}</span>
        <div class="min-w-0 flex-1">
          <a href="{{ route('guru.cases.edit', $c) }}" class="block truncate font-semibold text-[#33406b] hover:text-[#c4708f]">{{ $c->title }}</a>
          <div class="text-xs text-[#8a8399]">{{ $c->classroom->name }} · {{ $c->submissions_count }} pengumpulan</div>
        </div>
        <span class="badge {{ $c->isPublished() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $c->isPublished() ? 'Terpublikasi' : 'Draft' }}</span>
      </div>
    @empty
      <p class="rounded-2xl bg-[#faf6f7] p-4 text-sm text-[#8a8399]">Belum ada kasus. Mulai dengan menambah mata pelajaran, lalu kelas, lalu kasus.</p>
    @endforelse
  </section>

  {{-- Aktivitas --}}
  <section class="card lg:col-span-2">
    <p class="text-xs font-bold uppercase tracking-widest text-[#c4708f]">Riwayat</p>
    <h2 class="mb-4 font-display text-xl font-bold text-[#33406b]">Aktivitas terbaru</h2>
    @forelse($recent as $a)
      <div class="relative border-l-2 border-[#f0dde4] pb-4 pl-5 last:pb-0">
        <span class="absolute -left-[7px] top-1 h-3 w-3 rounded-full bg-gradient-to-br from-[#a8c3d4] to-[#db88a4] ring-4 ring-white"></span>
        <div class="text-sm text-[#4a4458]">{{ $a->description }}</div>
        <div class="text-xs text-[#a3a0b0]">{{ $a->created_at->diffForHumans() }}</div>
      </div>
    @empty
      <p class="rounded-2xl bg-[#faf6f7] p-4 text-sm text-[#8a8399]">Belum ada aktivitas.</p>
    @endforelse
  </section>
</div>
@endsection