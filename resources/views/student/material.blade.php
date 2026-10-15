@extends('layouts.app', ['mode' => 'siswa'])
@section('title', $material->title)
@section('content')
<a href="{{ route('siswa.materials') }}" class="mb-3 inline-flex items-center gap-1 text-sm font-semibold text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Semua materi</a>

@include('partials.material_article', ['material' => $material])

<div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl {{ $isRead ? 'border border-emerald-200 bg-emerald-50' : 'border border-blue-200 bg-white shadow-sm' }} p-5">
  @if($isRead)
    <div class="flex items-center gap-3 text-emerald-700"><i data-lucide="circle-check" class="h-8 w-8"></i><div><div class="font-bold">Sudah kamu baca</div><div class="text-sm">Kamu bisa membacanya lagi kapan saja.</div></div></div>
  @else
    <div class="flex items-center gap-3 text-blue-900"><i data-lucide="sparkles" class="h-8 w-8 text-blue-500"></i><div><div class="font-bold">Selesai membaca?</div><div class="text-sm text-slate-500">Tandai agar progres belajarmu tercatat.</div></div></div>
    <form method="POST" action="{{ route('siswa.materials.read', $material) }}">@csrf
      <button class="btn btn-primary"><i data-lucide="check" class="h-4 w-4"></i> Tandai sudah dibaca</button></form>
  @endif
</div>
@endsection