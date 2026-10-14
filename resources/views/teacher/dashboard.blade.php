@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Dashboard Guru')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">Dashboard Guru</h1>
<div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
  @foreach([['Mata pelajaran',$stats['subjects'],'book-open','guru.subjects'],['Kelas',$stats['classrooms'],'users','guru.classrooms'],['Siswa',$stats['students'],'user-round','guru.classrooms'],['Kasus',$stats['cases'],'file-text','guru.cases']] as [$l,$n,$i,$r])
    <a href="{{ route($r) }}" class="card hover:border-blue-400"><i data-lucide="{{ $i }}" class="h-5 w-5 text-blue-600"></i><div class="mt-2 text-3xl font-extrabold text-blue-900">{{ $n }}</div><div class="text-sm text-slate-500">{{ $l }}</div></a>
  @endforeach
  <a href="{{ route('guru.grading', ['filter' => 'ungraded']) }}" class="card border-amber-300 bg-amber-50 hover:border-amber-400"><i data-lucide="clock" class="h-5 w-5 text-amber-600"></i><div class="mt-2 text-3xl font-extrabold text-amber-700">{{ $ungraded }}</div><div class="text-sm text-amber-700">Belum dinilai</div></a>
</div>

<div class="mt-6 grid gap-4 lg:grid-cols-2">
  <section class="card">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-blue-900">Kasus terbaru</h2><a href="{{ route('guru.cases.create') }}" class="btn btn-primary"><i data-lucide="plus" class="h-4 w-4"></i> Kasus baru</a></div>
    @forelse($cases as $c)
      <div class="flex items-center justify-between border-t border-slate-100 py-2 text-sm">
        <div><a href="{{ route('guru.cases.edit', $c) }}" class="font-semibold hover:text-blue-600">{{ $c->title }}</a><div class="text-xs text-slate-500">{{ $c->classroom->name }} · {{ $c->submissions_count }} pengumpulan</div></div>
        <span class="badge {{ $c->isPublished() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $c->isPublished() ? 'Terpublikasi' : 'Draft' }}</span>
      </div>
    @empty
      <p class="text-sm text-slate-500">Belum ada kasus. Mulai dengan menambah mata pelajaran → kelas → kasus.</p>
    @endforelse
  </section>
  <section class="card">
    <h2 class="mb-3 font-bold text-blue-900">Aktivitas terbaru</h2>
    @forelse($recent as $a)
      <div class="border-t border-slate-100 py-2 text-sm"><div>{{ $a->description }}</div><div class="text-xs text-slate-400">{{ $a->created_at->diffForHumans() }}</div></div>
    @empty
      <p class="text-sm text-slate-500">Belum ada aktivitas.</p>
    @endforelse
  </section>
</div>
@endsection
