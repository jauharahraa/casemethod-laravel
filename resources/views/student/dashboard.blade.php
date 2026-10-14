@extends('layouts.app', ['mode' => 'siswa'])
@section('title', 'Dashboard Siswa')
@section('content')
@php
  $done = $cases->filter(fn ($c) => ($subs[$c->id] ?? null)?->status === 'locked')->count();
  $pct = $cases->count() ? round($done / $cases->count() * 100) : 0;
@endphp
<section class="mb-6 rounded-2xl bg-gradient-to-r from-blue-900 to-blue-600 p-6 text-white shadow">
  <p class="text-blue-200">Halo,</p>
  <h1 class="text-2xl font-extrabold">{{ $student->full_name }}</h1>
  <p class="mt-1 text-sm text-blue-100">{{ $classroom->name }} · {{ $classroom->subject->name }} · No. {{ $student->participant_number }}</p>
  <div class="mt-4"><div class="mb-1 flex justify-between text-sm"><span>Progres belajar</span><span>{{ $done }}/{{ $cases->count() }} kasus selesai</span></div>
    <div class="h-3 rounded-full bg-white/20"><div class="h-3 rounded-full bg-white" style="width: {{ $pct }}%"></div></div></div>
</section>

<h2 class="mb-3 text-lg font-bold text-blue-900">Daftar kasus</h2>
<div class="grid gap-4 md:grid-cols-2">
@forelse($cases as $c)
  @php $s = $subs[$c->id] ?? null; $g = $s?->grade?->published ? $s->grade : null; [$lbl,$cls] = \App\Models\Submission::badge($s?->status, false, (bool) $g); $late = $c->deadline && $c->deadline->isPast() && (!$s || $s->status !== 'locked'); @endphp
  <a href="{{ route('siswa.case', $c) }}" class="card transition hover:border-blue-400 hover:shadow-md">
    <div class="flex items-start justify-between gap-2"><h3 class="font-bold text-blue-900">{{ $c->title }}</h3><span class="badge shrink-0 {{ $cls }}">{{ $lbl }}</span></div>
    <div class="mt-2 flex items-center gap-1 text-sm {{ $late ? 'text-red-600' : 'text-slate-500' }}"><i data-lucide="calendar-clock" class="h-4 w-4"></i>{{ $c->deadline ? 'Tenggat '.$c->deadline->translatedFormat('d M Y H:i').($late ? ' (terlewat)' : '') : 'Tanpa tenggat' }}</div>
    @if($g)<div class="mt-3 flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2"><span class="text-sm text-emerald-700">Nilai akhir</span><span class="text-2xl font-extrabold text-emerald-700">{{ $g->final_score }}</span></div>@endif
  </a>
@empty
  <div class="card text-sm text-slate-500 md:col-span-2">Belum ada kasus yang dipublikasikan untuk kelasmu.</div>
@endforelse
</div>
@endsection
