@extends('layouts.app', ['mode' => 'siswa'])
@section('title', 'Materi')
@section('content')
@php
  $total = $materials->count(); $done = count($readIds); $pct = $total ? round($done / $total * 100) : 0;
@endphp
<section class="relative mb-6 overflow-hidden rounded-[28px] bg-gradient-to-br from-[#e2ecf3] via-[#ece7ee] to-[#f8dedf] p-6 sm:p-8">
  <div class="pointer-events-none absolute -bottom-28 -right-16 h-72 w-72 rounded-full bg-[#eec6c7]/60"></div>
  <i data-lucide="library" class="absolute -right-4 -top-4 h-44 w-44 text-white/70"></i>
  <i data-lucide="sparkles" class="absolute bottom-5 right-28 h-7 w-7 text-[#db88a4]"></i>
  <div class="relative max-w-xl">
    <span class="inline-block rounded-full border border-[#a8c3d4] bg-white/60 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#4d6a9c]">Perpustakaan belajar</span>
    <h1 class="mt-3 text-3xl font-extrabold text-[#33406b]">Materi Pembelajaran</h1>
    <p class="mt-1 text-[#5b5470]">{{ $classroom->name }} · {{ $classroom->subject->name }}</p>
    <div class="mt-5 rounded-2xl bg-white/70 p-4 backdrop-blur">
      <div class="mb-1.5 flex justify-between text-sm"><span class="font-semibold text-[#33406b]">Progres membaca</span><span class="text-[#5b5470]">{{ $done }}/{{ $total }} materi</span></div>
      <div class="h-3 overflow-hidden rounded-full bg-[#e9e3ec]"><div class="h-3 rounded-full bg-gradient-to-r from-[#738fbd] via-[#cc8eb1] to-[#db88a4]" style="width: {{ $pct }}%"></div></div>
    </div>
  </div>
</section>

@if($materials->isEmpty())
  <div class="card py-12 text-center">
    <i data-lucide="book-open" class="mx-auto h-12 w-12 text-slate-300"></i>
    <p class="mt-3 font-semibold text-slate-600">Belum ada materi</p>
    <p class="text-sm text-slate-400">Materi akan muncul di sini setelah gurumu mempublikasikannya.</p>
  </div>
@else
<div x-data="materiFilter()">
  <div class="mb-4 flex flex-wrap items-center gap-3">
    <div class="relative min-w-0 flex-1 sm:max-w-sm">
      <i data-lucide="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
      <input x-model="q" type="search" placeholder="Cari materi…" class="input !pl-9">
    </div>
    <div class="flex gap-1 rounded-xl bg-white p-1 shadow-sm ring-1 ring-slate-200">
      @foreach(['all' => 'Semua', 'unread' => 'Belum dibaca', 'read' => 'Sudah dibaca'] as $k => $l)
        <button type="button" @click="f='{{ $k }}'" :class="f==='{{ $k }}' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="rounded-lg px-3 py-1.5 text-sm font-semibold transition">{{ $l }}</button>
      @endforeach
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($materials as $m)
      @php $isRead = in_array($m->id, $readIds); $isNew = !$isRead && $m->created_at->gt(now()->subDays(7)); @endphp
      <a href="{{ route('siswa.materials.show', $m) }}"
         x-show="show(@js($isRead), @js(mb_strtolower($m->title.' '.$m->summary)))"
         class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="relative h-36 bg-gradient-to-br {{ $m->gradient() }}">
          <i data-lucide="{{ $m->icon }}" class="absolute -bottom-6 -right-4 h-32 w-32 text-white/20 transition duration-500 group-hover:rotate-6 group-hover:scale-110"></i>
          <span class="absolute bottom-3 left-4 grid h-12 w-12 place-items-center rounded-xl bg-white/25 backdrop-blur"><i data-lucide="{{ $m->icon }}" class="h-6 w-6 text-white"></i></span>
          <div class="absolute right-3 top-3 flex gap-1.5">
            @if($isRead)<span class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-bold text-white shadow"><i data-lucide="circle-check" class="h-3.5 w-3.5"></i>Dibaca</span>@endif
            @if($isNew)<span class="rounded-full bg-amber-400 px-2.5 py-1 text-xs font-bold text-amber-950 shadow">Baru</span>@endif
          </div>
        </div>
        <div class="p-4">
          <h3 class="line-clamp-2 text-lg font-bold leading-snug text-blue-900 group-hover:text-blue-600">{{ $m->title }}</h3>
          <p class="mt-1 line-clamp-2 min-h-[2.5rem] text-sm text-slate-500">{{ $m->summary ?: 'Ketuk untuk mulai membaca materi ini.' }}</p>
          <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3.5 w-3.5"></i>{{ $m->readingMinutes() }} menit</span>
            <span class="inline-flex items-center gap-1 font-semibold text-blue-600">Baca <i data-lucide="arrow-right" class="h-3.5 w-3.5 transition group-hover:translate-x-1"></i></span>
          </div>
        </div>
      </a>
    @endforeach
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function materiFilter() {
  return {
    q: '', f: 'all',
    show(read, text) {
      var okFilter = this.f === 'all' || (this.f === 'read' && read) || (this.f === 'unread' && !read);
      return okFilter && text.indexOf(this.q.toLowerCase()) !== -1;
    }
  };
}
</script>
@endpush