@extends('layouts.app', ['mode' => 'siswa'])
@section('title', 'Dashboard Siswa')
@section('content')
@php
  $total = $cases->count();
  $done = $cases->filter(fn ($c) => ($subs[$c->id] ?? null)?->status === 'locked')->count();
  $pct = $total ? round($done / $total * 100) : 0;
  $working = $subs->filter(fn ($s) => in_array($s->status, ['draft', 'feedback', 'revising']))->count();
  $graded = $subs->filter(fn ($s) => $s->grade?->published)->count();
  $tints = ['#d9e6ef', '#f8dde2', '#e9e4ef', '#f3dfea'];
  $inks  = ['#4f7189', '#a65772', '#5f5577', '#955d7e'];
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#e2ecf3] via-[#ece7ee] to-[#f8dedf] p-6 sm:p-10">
  <div class="pointer-events-none absolute -bottom-32 -right-20 h-80 w-80 rounded-full bg-[#eec6c7]/60"></div>
  <div class="relative grid items-center gap-6 md:grid-cols-5">
    <div class="md:col-span-3">
      <span class="inline-block rounded-full border border-[#a8c3d4] bg-white/60 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#4d6a9c]">Ruang siswa</span>
      <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#33406b] sm:text-4xl">Halo, {{ $student->full_name }} 👋</h1>
      <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/70 px-3 py-1 text-[#5f7cae]"><i data-lucide="school" class="h-3.5 w-3.5"></i>{{ $classroom->name }}</span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/70 px-3 py-1 text-[#955d7e]"><i data-lucide="book-open" class="h-3.5 w-3.5"></i>{{ $classroom->subject->name }}</span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/70 px-3 py-1 text-[#5b5470]"><i data-lucide="hash" class="h-3.5 w-3.5"></i>{{ $student->participant_number }}</span>
      </div>

      <div class="mt-5 max-w-md rounded-2xl bg-white/70 p-4 backdrop-blur">
        <div class="mb-1.5 flex justify-between text-sm"><span class="font-semibold text-[#33406b]">Progres belajar</span><span class="text-[#5b5470]">{{ $done }}/{{ $total }} kasus selesai</span></div>
        <div class="h-3 overflow-hidden rounded-full bg-[#e9e3ec]"><div class="h-3 rounded-full bg-gradient-to-r from-[#738fbd] via-[#cc8eb1] to-[#db88a4]" style="width: {{ $pct }}%"></div></div>
      </div>

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="#kasus" class="btn btn-primary"><i data-lucide="pencil-line" class="h-4 w-4"></i> Kerjakan kasus</a>
        <a href="{{ route('siswa.materials') }}" class="btn btn-ghost"><i data-lucide="library" class="h-4 w-4"></i> Baca materi</a>
      </div>
    </div>
    <div class="md:col-span-2">@include('partials.art_desk')</div>
  </div>
</section>

{{-- Ringkasan --}}
<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
  @foreach([['Total kasus', $total, 'file-text'], ['Sedang dikerjakan', $working, 'pencil-line'], ['Selesai', $done, 'circle-check'], ['Nilai tersedia', $graded, 'award']] as $k => [$l, $n, $i])
    <div class="card">
      <span class="grid h-12 w-12 place-items-center rounded-2xl" style="background: {{ $tints[$k] }}; color: {{ $inks[$k] }}"><i data-lucide="{{ $i }}" class="h-6 w-6"></i></span>
      <div class="mt-3 font-display text-3xl font-extrabold text-[#33406b]">{{ $n }}</div>
      <div class="text-sm text-[#8a8399]">{{ $l }}</div>
    </div>
  @endforeach
</div>

{{-- Daftar kasus --}}
<div id="kasus" class="mb-4 mt-10 scroll-mt-24">
  <p class="text-xs font-bold uppercase tracking-widest text-[#c4708f]">Tugas belajar</p>
  <h2 class="font-display text-2xl font-bold text-[#33406b]">Daftar kasus</h2>
</div>
<div class="grid gap-5 md:grid-cols-2">
@forelse($cases as $k => $c)
  @php
    $s = $subs[$c->id] ?? null; $g = $s?->grade?->published ? $s->grade : null;
    [$lbl, $cls] = \App\Models\Submission::badge($s?->status, false, (bool) $g);
    $late = $c->deadline && $c->deadline->isPast() && (!$s || $s->status !== 'locked');
  @endphp
  <a href="{{ route('siswa.case', $c) }}" class="card group flex gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl transition group-hover:scale-110" style="background: {{ $tints[$k % 4] }}; color: {{ $inks[$k % 4] }}"><i data-lucide="notebook-pen" class="h-7 w-7"></i></span>
    <div class="min-w-0 flex-1">
      <div class="flex items-start justify-between gap-2"><h3 class="font-display text-lg font-bold leading-snug text-[#33406b] group-hover:text-[#c4708f]">{{ $c->title }}</h3><span class="badge shrink-0 {{ $cls }}">{{ $lbl }}</span></div>
      <div class="mt-2 flex items-center gap-1.5 text-sm {{ $late ? 'font-semibold text-rose-600' : 'text-[#8a8399]' }}"><i data-lucide="calendar-clock" class="h-4 w-4"></i>{{ $c->deadline ? 'Tenggat '.$c->deadline->translatedFormat('d M Y H:i').($late ? ' (terlewat)' : '') : 'Tanpa tenggat' }}</div>
      @if($g)<div class="mt-3 flex items-center justify-between rounded-xl bg-gradient-to-r from-[#eef5f9] to-[#fbeaee] px-3 py-2"><span class="text-sm text-[#5b5470]">Nilai akhir</span><span class="font-display text-2xl font-extrabold text-[#33406b]">{{ $g->final_score }}</span></div>@endif
      <div class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-[#5f7cae] group-hover:text-[#c4708f]">Buka kasus <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1"></i></div>
    </div>
  </a>
@empty
  <div class="card py-12 text-center md:col-span-2"><i data-lucide="file-text" class="mx-auto h-12 w-12 text-slate-300"></i><p class="mt-3 font-semibold text-[#5b5470]">Belum ada kasus</p><p class="text-sm text-[#8a8399]">Kasus akan muncul di sini setelah gurumu mempublikasikannya.</p></div>
@endforelse
</div>
@endsection