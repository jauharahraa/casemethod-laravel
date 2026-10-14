@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Kelas')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">Kelas</h1>
<div class="grid gap-4 lg:grid-cols-3">
  <form method="POST" action="{{ route('guru.classrooms.store') }}" class="card space-y-3">@csrf
    <h2 class="font-bold">Buat kelas</h2>
    @if($subjects->isEmpty())
      <p class="text-sm text-amber-700">Tambahkan <a class="underline" href="{{ route('guru.subjects') }}">mata pelajaran</a> terlebih dahulu.</p>
    @else
      <div><label class="label">Nama kelas</label><input name="name" class="input" placeholder="mis. XI IPS 1" required></div>
      <div><label class="label">Mata pelajaran</label><select name="subject_id" class="input" required>@foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
      <button class="btn btn-primary w-full"><i data-lucide="plus" class="h-4 w-4"></i> Buat kelas</button>
    @endif
  </form>
  <div class="grid gap-3 sm:grid-cols-2 lg:col-span-2">
    @forelse($classrooms as $c)
      <a href="{{ route('guru.classrooms.show', $c) }}" class="card hover:border-blue-400">
        <div class="font-semibold">{{ $c->name }}</div>
        <div class="text-sm text-slate-500">{{ $c->subject->name }} · {{ $c->students_count }} siswa</div>
        <div class="mt-2 inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2 py-1 font-mono text-sm font-bold text-blue-800"><i data-lucide="key-round" class="h-4 w-4"></i>{{ $c->code }}</div>
      </a>
    @empty
      <div class="card text-sm text-slate-500 sm:col-span-2">Belum ada kelas.</div>
    @endforelse
  </div>
</div>
@endsection
