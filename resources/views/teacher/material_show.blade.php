@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Pratinjau: '.$material->title)
@section('content')
<div class="mb-3 flex flex-wrap items-center justify-between gap-2">
  <a href="{{ route('guru.materials') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Semua materi</a>
  <div class="flex items-center gap-2">
    <span class="badge {{ $material->isPublished() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">Pratinjau · {{ $material->isPublished() ? 'Terpublikasi' : 'Draft' }}</span>
    <a href="{{ route('guru.materials.edit', $material) }}" class="btn btn-ghost"><i data-lucide="pencil" class="h-4 w-4"></i> Ubah</a>
  </div>
</div>
@include('partials.material_article', ['material' => $material])
@endsection