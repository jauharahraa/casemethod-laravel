@extends('layouts.app', ['mode' => 'siswa'])
@section('title', $case->title)
@section('content')
@php
  $versions = $sub->versions; $v1 = $versions->firstWhere('version', 1); $v2 = $versions->firstWhere('version', 2);
  $fb1 = $v1?->feedback; $status = $sub->status;
  $grade = $sub->grade?->published ? $sub->grade : null;
  $editing = in_array($status, ['draft','revising']);
  $current = $editing ? $versions->last() : null;
  $late = $case->deadline && $case->deadline->isPast() && $status !== 'locked';
@endphp
<a href="{{ route('siswa.dashboard') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Dashboard</a>
<div class="mb-4 flex flex-wrap items-start justify-between gap-2">
  <h1 class="text-2xl font-bold text-blue-900">{{ $case->title }}</h1>
  @php [$lbl,$cls] = \App\Models\Submission::badge($status, false, (bool)$grade); @endphp<span class="badge {{ $cls }}">{{ $lbl }}</span>
</div>
@if($case->deadline)<p class="mb-3 text-sm {{ $late ? 'font-semibold text-red-600' : 'text-slate-500' }}">Tenggat: {{ $case->deadline->translatedFormat('d M Y H:i') }}{{ $late ? ' — sudah terlewat' : '' }}</p>@endif

<section class="card mb-4">
  <h2 class="mb-2 font-bold text-blue-900">Kasus</h2>
  <div class="whitespace-pre-line text-sm leading-relaxed">{{ $case->description }}</div>
  <div class="mt-4 grid gap-3 md:grid-cols-2">
    <div class="rounded-xl bg-blue-50 p-3"><h3 class="mb-1 text-sm font-bold text-blue-900">Instruksi</h3><p class="whitespace-pre-line text-sm">{{ $case->instructions }}</p></div>
    <div class="rounded-xl bg-indigo-50 p-3"><h3 class="mb-1 text-sm font-bold text-indigo-900">Tujuan pembelajaran</h3><p class="whitespace-pre-line text-sm">{{ $case->objectives }}</p></div>
  </div>
  <details class="mt-3"><summary class="cursor-pointer text-sm font-semibold text-blue-600">Lihat rubrik penilaian</summary><div class="mt-2">@include('partials.rubric_summary', ['rubric' => $rubric])</div></details>
</section>

@if($grade)
<section class="card mb-4 border-emerald-300 bg-emerald-50">
  <h2 class="mb-2 font-bold text-emerald-800">Nilai dari guru</h2>
  <div class="flex flex-wrap items-center gap-6">
    <div class="text-5xl font-extrabold text-emerald-700">{{ $grade->final_score }}</div>
    <div class="space-y-1 text-sm">@foreach($rubric['aspects'] as $k => $a)<div>{{ $a['label'] }}: <b>{{ $grade->aspect_scores[$k] ?? 0 }}</b>/100 <span class="text-slate-400">(bobot {{ rtrim(rtrim(number_format($a['weight'],2),'0'),'.') }}%)</span></div>@endforeach</div>
  </div>
  @if($grade->comment)<p class="mt-3 whitespace-pre-line rounded-lg bg-white p-3 text-sm"><b>Komentar guru:</b> {{ $grade->comment }}</p>@endif
</section>
@endif

{{-- Form pengerjaan (jawaban pertama atau revisi) --}}
@if($editing)
  @php $isRev = $status === 'revising'; @endphp
  @if($isRev && $fb1)
    <section class="card mb-4"><h2 class="mb-2 font-bold text-blue-900">Feedback AI atas jawaban pertama</h2>@include('partials.feedback', ['feedback' => $fb1])</section>
  @endif
  <form id="answerForm" method="POST" action="{{ route($isRev ? 'siswa.case.revise.submit' : 'siswa.case.submit', $case) }}" class="card space-y-4">@csrf
    <div class="flex flex-wrap items-center justify-between gap-2"><h2 class="font-bold text-blue-900">{{ $isRev ? 'Revisi jawaban (kesempatan satu kali)' : 'Jawabanmu' }}</h2><span id="saveState" class="text-xs text-slate-400">Draft tersimpan otomatis</span></div>
    @foreach(['identification'=>['1. Identifikasi Masalah','Apa masalah utama dalam kasus ini?'],'analysis'=>['2. Analisis Masalah','Mengapa masalah terjadi? Apa dampak dan konsep yang relevan?'],'solution'=>['3. Solusi','Apa solusi yang kamu usulkan, beserta langkah dan alasannya?']] as $f => [$t,$ph])
      <div><label class="label">{{ $t }}</label><textarea name="{{ $f }}" rows="7" class="input autosave" placeholder="{{ $ph }}">{{ old($f, $current->$f) }}</textarea></div>
    @endforeach
    <p class="text-xs text-slate-500">Setiap bagian minimal 20 karakter untuk dikirim.
      @if(!$isRev) Setelah dikirim, AI memberi feedback dan kamu bisa merevisi satu kali. @else Setelah revisi dikirim, jawaban dikunci. @endif</p>
    <div class="flex flex-wrap gap-2">
      <button type="submit" formaction="{{ route('siswa.case.draft', $case) }}" class="btn btn-ghost"><i data-lucide="save" class="h-4 w-4"></i> Simpan draft</button>
      <button type="submit" class="btn btn-primary" onclick="return confirm('{{ $isRev ? 'Kirim revisi? Jawaban akan dikunci.' : 'Kirim jawaban? Kamu tidak dapat mengubah jawaban pertama setelah ini.' }}')"><i data-lucide="send" class="h-4 w-4"></i> {{ $isRev ? 'Kirim revisi' : 'Kirim jawaban' }}</button>
    </div>
  </form>
@endif

{{-- Setelah jawaban pertama dikirim --}}
@if($status === 'feedback')
  <section class="card mb-4"><h2 class="mb-2 font-bold text-blue-900">Jawaban pertamamu</h2>@include('partials.answer', ['v' => $v1])</section>
  <section class="card mb-4"><h2 class="mb-3 font-bold text-blue-900">Feedback AI</h2>
    @if($fb1 && $fb1->status === 'ok')
      @include('partials.feedback', ['feedback' => $fb1])
      <div class="mt-4 rounded-xl bg-blue-50 p-3 text-sm text-blue-900">Kamu punya <b>satu kali</b> kesempatan merevisi. Revisi bersifat opsional.</div>
      <div class="mt-3 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('siswa.case.revise', $case) }}">@csrf<button class="btn btn-primary" onclick="return confirm('Mulai revisi? Kesempatan revisi hanya satu kali.')"><i data-lucide="pencil" class="h-4 w-4"></i> Revisi jawaban</button></form>
        <form method="POST" action="{{ route('siswa.case.finish', $case) }}">@csrf<button class="btn btn-ghost" onclick="return confirm('Selesai tanpa revisi? Jawaban akan dikunci.')"><i data-lucide="check" class="h-4 w-4"></i> Selesai tanpa revisi</button></form>
      </div>
    @else
      <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">Feedback AI belum berhasil dibuat. Jawabanmu aman dan kesempatan revisimu belum terpakai.
        @if($fb1?->error)<div class="mt-1 text-xs text-red-500">{{ $fb1->error }}</div>@endif</div>
      <div class="mt-3 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('siswa.case.retry', $case) }}">@csrf<button class="btn btn-primary"><i data-lucide="refresh-cw" class="h-4 w-4"></i> Coba lagi</button></form>
        <form method="POST" action="{{ route('siswa.case.finish', $case) }}">@csrf<button class="btn btn-ghost" onclick="return confirm('Selesai tanpa feedback dan tanpa revisi?')">Selesai tanpa revisi</button></form>
      </div>
    @endif
  </section>
@endif

{{-- Terkunci --}}
@if($status === 'locked')
  <section class="card mb-4"><h2 class="mb-2 font-bold text-blue-900">Jawaban pertama</h2>@include('partials.answer', ['v' => $v1])</section>
  @if($fb1) <section class="card mb-4"><h2 class="mb-3 font-bold text-blue-900">Feedback AI</h2>@include('partials.feedback', ['feedback' => $fb1])</section> @endif
  @if($v2) <section class="card mb-4"><h2 class="mb-2 font-bold text-blue-900">Jawaban revisi (final)</h2>@include('partials.answer', ['v' => $v2])</section> @endif
  @if(!$grade)<div class="card text-sm text-slate-600">Jawabanmu sudah dikunci dan menunggu penilaian guru. Nilai akan muncul di sini setelah dipublikasikan.</div>@endif
@endif

@if($editing)
@push('scripts')
<script>
(function(){
  var form = document.getElementById('answerForm'), state = document.getElementById('saveState'), t = null, busy = false;
  function save(){
    if(busy) return; busy = true; state.textContent = 'Menyimpan…';
    fetch(@js(route('siswa.case.draft', $case)), {method:'POST', body:new FormData(form), headers:{'Accept':'application/json'}})
      .then(function(r){ return r.json().then(function(j){ if(!r.ok) throw new Error(j.message||'gagal'); return j; }); })
      .then(function(j){ state.textContent = 'Draft tersimpan '+j.saved_at; })
      .catch(function(){ state.textContent = 'Gagal menyimpan otomatis — gunakan tombol Simpan draft'; })
      .finally(function(){ busy = false; });
  }
  form.querySelectorAll('.autosave').forEach(function(el){
    el.addEventListener('input', function(){ state.textContent = 'Mengetik…'; clearTimeout(t); t = setTimeout(save, 2000); });
  });
})();
</script>
@endpush
@endif
@endsection
