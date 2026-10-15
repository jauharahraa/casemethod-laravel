@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Materi')
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
  <div><h1 class="text-2xl font-bold text-blue-900">Materi Pembelajaran</h1><p class="text-sm text-slate-500">Bagikan bacaan ke siswa sebelum mereka mengerjakan kasus.</p></div>
  <a href="{{ route('guru.materials.create') }}" class="btn btn-primary"><i data-lucide="plus" class="h-4 w-4"></i> Materi baru</a>
</div>

@if($materials->isEmpty())
  <div class="card py-12 text-center"><i data-lucide="book-marked" class="mx-auto h-12 w-12 text-slate-300"></i><p class="mt-3 font-semibold text-slate-600">Belum ada materi</p><p class="text-sm text-slate-400">Klik "Materi baru" untuk membuat materi pertama.</p></div>
@else
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
  @foreach($materials as $m)
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg">
      <div class="relative h-28 bg-gradient-to-br {{ $m->gradient() }}">
        <i data-lucide="{{ $m->icon }}" class="absolute -bottom-5 -right-3 h-28 w-28 text-white/20"></i>
        <span class="absolute bottom-3 left-4 grid h-11 w-11 place-items-center rounded-xl bg-white/25 backdrop-blur"><i data-lucide="{{ $m->icon }}" class="h-5 w-5 text-white"></i></span>
        <span class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-xs font-bold shadow {{ $m->isPublished() ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-600' }}">{{ $m->isPublished() ? 'Terpublikasi' : 'Draft' }}</span>
      </div>
      <div class="p-4">
        <h3 class="line-clamp-2 font-bold text-blue-900">{{ $m->title }}</h3>
        <p class="mt-1 text-xs text-slate-500">{{ $m->classroom->name }} · {{ $m->readingMinutes() }} menit · dibaca {{ $m->reads_count }} siswa</p>
        <div class="mt-3 flex flex-wrap gap-2">
          <a href="{{ route('guru.materials.show', $m) }}" class="btn btn-ghost !px-3 !py-1.5"><i data-lucide="eye" class="h-4 w-4"></i> Lihat</a>
          <a href="{{ route('guru.materials.edit', $m) }}" class="btn btn-ghost !px-3 !py-1.5"><i data-lucide="pencil" class="h-4 w-4"></i> Ubah</a>
          <form method="POST" action="{{ route('guru.materials.toggle', $m) }}">@csrf<button class="btn {{ $m->isPublished() ? 'btn-ghost' : 'btn-primary' }} !px-3 !py-1.5">{{ $m->isPublished() ? 'Tarik' : 'Publikasikan' }}</button></form>
          <form method="POST" action="{{ route('guru.materials.destroy', $m) }}" onsubmit="return confirm('Hapus materi ini?')">@csrf @method('DELETE')<button class="btn btn-danger !px-3 !py-1.5"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endif
@endsection