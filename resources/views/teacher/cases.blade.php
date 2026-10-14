@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Kasus')
@section('content')
<div class="mb-4 flex items-center justify-between"><h1 class="text-2xl font-bold text-blue-900">Kasus</h1><a href="{{ route('guru.cases.create') }}" class="btn btn-primary"><i data-lucide="plus" class="h-4 w-4"></i> Kasus baru</a></div>
<div class="space-y-3">
@forelse($cases as $c)
  <div class="card flex flex-wrap items-center justify-between gap-3">
    <div class="min-w-0">
      <div class="flex items-center gap-2"><h2 class="font-semibold">{{ $c->title }}</h2><span class="badge {{ $c->isPublished() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $c->isPublished() ? 'Terpublikasi' : 'Draft' }}</span></div>
      <div class="text-sm text-slate-500">{{ $c->classroom->name }} · {{ $c->classroom->subject->name }} · {{ $c->submissions_count }} pengumpulan @if($c->deadline) · tenggat {{ $c->deadline->translatedFormat('d M Y H:i') }}@endif</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('guru.cases.edit', $c) }}" class="btn btn-ghost"><i data-lucide="pencil" class="h-4 w-4"></i> Ubah</a>
      <form method="POST" action="{{ route('guru.cases.duplicate', $c) }}">@csrf<button class="btn btn-ghost"><i data-lucide="copy" class="h-4 w-4"></i> Duplikat</button></form>
      <form method="POST" action="{{ route('guru.cases.toggle', $c) }}">@csrf<button class="btn {{ $c->isPublished() ? 'btn-ghost' : 'btn-primary' }}">{{ $c->isPublished() ? 'Tarik ke draft' : 'Publikasikan' }}</button></form>
      <form method="POST" action="{{ route('guru.cases.destroy', $c) }}" onsubmit="return confirm('Hapus kasus beserta semua jawabannya?')">@csrf @method('DELETE')<button class="btn btn-danger"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form>
    </div>
  </div>
@empty
  <div class="card text-sm text-slate-500">Belum ada kasus.</div>
@endforelse
</div>
@endsection
