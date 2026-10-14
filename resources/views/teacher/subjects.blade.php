@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Mata Pelajaran')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">Mata Pelajaran</h1>
<div class="grid gap-4 lg:grid-cols-3">
  <form method="POST" action="{{ route('guru.subjects.store') }}" class="card space-y-3 lg:col-span-1">@csrf
    <h2 class="font-bold">Tambah mata pelajaran</h2>
    <div><label class="label">Nama</label><input name="name" class="input" required></div>
    <div><label class="label">Deskripsi (opsional)</label><textarea name="description" rows="2" class="input"></textarea></div>
    <button class="btn btn-primary w-full"><i data-lucide="plus" class="h-4 w-4"></i> Tambah</button>
  </form>
  <div class="space-y-3 lg:col-span-2">
    @forelse($subjects as $s)
      <div class="card" x-data="{edit:false}">
        <div x-show="!edit" class="flex items-start justify-between gap-3">
          <div><div class="font-semibold">{{ $s->name }}</div><div class="text-sm text-slate-500">{{ $s->description }}</div><div class="mt-1 text-xs text-slate-400">{{ $s->classrooms_count }} kelas</div></div>
          <div class="flex gap-2">
            <button type="button" @click="edit=true" class="btn btn-ghost"><i data-lucide="pencil" class="h-4 w-4"></i></button>
            <form method="POST" action="{{ route('guru.subjects.destroy', $s) }}" onsubmit="return confirm('Hapus mata pelajaran ini beserta SEMUA kelas, kasus, dan jawaban di dalamnya?')">@csrf @method('DELETE')<button class="btn btn-danger"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form>
          </div>
        </div>
        <form x-show="edit" x-cloak method="POST" action="{{ route('guru.subjects.update', $s) }}" class="space-y-2">@csrf @method('PUT')
          <input name="name" value="{{ $s->name }}" class="input" required>
          <textarea name="description" rows="2" class="input">{{ $s->description }}</textarea>
          <div class="flex gap-2"><button class="btn btn-primary">Simpan</button><button type="button" @click="edit=false" class="btn btn-ghost">Batal</button></div>
        </form>
      </div>
    @empty
      <div class="card text-sm text-slate-500">Belum ada mata pelajaran.</div>
    @endforelse
  </div>
</div>
@endsection
