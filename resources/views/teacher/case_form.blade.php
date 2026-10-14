@extends('layouts.app', ['mode' => 'guru'])
@section('title', $case->exists ? 'Ubah Kasus' : 'Kasus Baru')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">{{ $case->exists ? 'Ubah Kasus' : 'Kasus Baru' }}</h1>
@if($classrooms->isEmpty())
  <div class="card text-sm text-amber-700">Buat <a class="underline" href="{{ route('guru.classrooms') }}">kelas</a> terlebih dahulu.</div>
@else
<form method="POST" action="{{ $case->exists ? route('guru.cases.update', $case) : route('guru.cases.store') }}" class="space-y-5">
  @csrf @if($case->exists) @method('PUT') @endif
  <section class="card space-y-4">
    <div class="grid gap-4 md:grid-cols-3">
      <div class="md:col-span-2"><label class="label">Judul kasus</label><input name="title" value="{{ old('title', $case->title) }}" class="input" required></div>
      <div><label class="label">Kelas</label><select name="classroom_id" class="input" required>@foreach($classrooms as $c)<option value="{{ $c->id }}" @selected(old('classroom_id', $case->classroom_id) == $c->id)>{{ $c->name }} — {{ $c->subject->name }}</option>@endforeach</select></div>
    </div>
    <div><label class="label">Deskripsi kasus</label><textarea name="description" rows="8" class="input" required>{{ old('description', $case->description) }}</textarea></div>
    <div class="grid gap-4 md:grid-cols-2">
      <div><label class="label">Instruksi pengerjaan</label><textarea name="instructions" rows="4" class="input" required>{{ old('instructions', $case->instructions) }}</textarea></div>
      <div><label class="label">Tujuan pembelajaran</label><textarea name="objectives" rows="4" class="input" required>{{ old('objectives', $case->objectives) }}</textarea></div>
    </div>
    <div class="max-w-xs"><label class="label">Tenggat (opsional)</label><input type="datetime-local" name="deadline" value="{{ old('deadline', $case->deadline?->format('Y-m-d\TH:i')) }}" class="input"></div>
  </section>

  @php
    $weights = [];
    foreach ($rubric['aspects'] as $k => $a) { $weights[$k] = old("rubric.$k.weight", $a['weight']); }
  @endphp
  <section class="card" x-data="{ w: @js($weights), total(){ return Math.round(Object.values(this.w).reduce((s,v)=>s+(parseFloat(v)||0),0)*100)/100; } }">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
      <div>
        <h2 class="font-bold text-blue-900">Rubrik Penilaian</h2>
        <p class="text-xs text-slate-500">Kriteria sederhana berbasis aspek utama dan bobot penilaian.</p>
      </div>
      <span class="badge" :class="Math.abs(total()-100)<0.001 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">Total bobot: <span x-text="total()" class="mx-1"></span>%</span>
    </div>
    @if($lockWeights)<p class="mb-3 rounded-lg bg-amber-50 p-2 text-sm text-amber-700">Kasus sudah dipublikasikan: bobot dikunci.</p>@endif
    <div class="grid gap-4 lg:grid-cols-3">
      @foreach($rubric['aspects'] as $k => $a)
        @php $c = $a['criteria'][0]; @endphp
        <div class="space-y-3 rounded-xl border border-slate-200 p-4 bg-white">
          <h3 class="font-semibold text-blue-900">{{ $a['label'] }}</h3>
          <div>
            <label class="label">Bobot (%)</label>
            <input type="number" step="0.01" min="0" max="100" class="input" name="rubric[{{ $k }}][weight]" x-model.number="w.{{ $k }}" @if($lockWeights) readonly @endif required>
          </div>
          <div>
            <label class="label">Kriteria</label>
            <input class="input" name="rubric[{{ $k }}][criteria][0][name]" value="{{ old("rubric.$k.criteria.0.name", $c['name']) }}" required>
          </div>

          {{-- Hidden fields agar validasi backend tetap berjalan tanpa perlu mengisi skor 0-4 secara manual --}}
          <input type="hidden" name="rubric[{{ $k }}][criteria][0][description]" value="">
          @foreach([4,3,2,1,0] as $n)
            <input type="hidden" name="rubric[{{ $k }}][criteria][0][levels][{{ $n }}]" value="Sesuai standar">
          @endforeach
        </div>
      @endforeach
    </div>
  </section>

  <div class="flex flex-wrap gap-2">
    <button name="action" value="draft" class="btn btn-ghost"><i data-lucide="save" class="h-4 w-4"></i> Simpan {{ $case->isPublished() ? '(tetap terpublikasi)' : 'sebagai draft' }}</button>
    <button name="action" value="publish" class="btn btn-primary"><i data-lucide="send" class="h-4 w-4"></i> {{ $case->isPublished() ? 'Simpan' : 'Simpan & publikasikan' }}</button>
    <a href="{{ route('guru.cases') }}" class="btn btn-ghost">Batal</a>
  </div>
</form>
@endif
@endsection