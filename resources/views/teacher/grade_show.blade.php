@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Menilai: '.$s->student->full_name)
@section('content')
<a href="{{ route('guru.grading') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Daftar penilaian</a>
<div class="mb-4"><h1 class="text-2xl font-bold text-blue-900">{{ $s->student->full_name }}</h1><p class="text-slate-500">{{ $s->case->title }} · No. {{ $s->student->participant_number }} · rubrik versi {{ $s->rubricVersion->version }}</p></div>

<div class="grid gap-4 lg:grid-cols-5">
  <div class="space-y-4 lg:col-span-3">
    @foreach($s->versions as $v)
      @continue($v->status !== 'submitted')
      <section class="card">
        <h2 class="mb-1 font-bold text-blue-900">{{ $v->version === 1 ? 'Jawaban pertama' : 'Jawaban revisi' }}</h2>
        <div class="mb-3 text-xs text-slate-400">Dikirim {{ $v->submitted_at?->translatedFormat('d M Y H:i') }}</div>
        @include('partials.answer', ['v' => $v])
        @if($v->feedback)
          <details class="mt-3"><summary class="cursor-pointer text-sm font-semibold text-blue-600">Feedback AI untuk versi ini</summary>
            <div class="mt-2">
              @if($v->feedback->status === 'ok') @include('partials.feedback', ['feedback' => $v->feedback]) @else <p class="text-sm text-red-600">Feedback AI gagal dibuat.</p> @endif
            </div></details>
        @endif
      </section>
    @endforeach
    <section class="card"><h2 class="mb-2 font-bold text-blue-900">Riwayat pengerjaan</h2>
      <ul class="space-y-1 text-sm text-slate-600">
        @foreach($s->versions as $v)
          <li>• Versi {{ $v->version }} — {{ $v->status === 'submitted' ? 'dikirim '.$v->submitted_at?->translatedFormat('d M Y H:i') : 'draft (belum dikirim)' }}</li>
        @endforeach
        <li>• Status: {{ \App\Models\Submission::badge($s->status)[0] }} @if($s->locked_at) (dikunci {{ $s->locked_at->translatedFormat('d M Y H:i') }}) @endif</li>
      </ul></section>
  </div>

  <div class="lg:col-span-2">
    @if($s->status !== 'locked')
      <div class="card text-sm text-amber-700">Siswa belum menyelesaikan jawaban (status: {{ \App\Models\Submission::badge($s->status)[0] }}). Penilaian dibuka setelah jawaban dikunci.</div>
    @else
    <form method="POST" action="{{ route('guru.grading.save', $s) }}" class="card space-y-4 lg:sticky lg:top-4">@csrf
      <h2 class="font-bold text-blue-900">Penilaian berdasarkan rubrik</h2>
      @foreach($rubric['aspects'] as $k => $a)
        <div class="rounded-xl border border-slate-200 p-3">
          <div class="mb-2 flex items-center justify-between"><h3 class="font-semibold">{{ $a['label'] }}</h3><span class="badge bg-blue-100 text-blue-700">{{ rtrim(rtrim(number_format($a['weight'],2),'0'),'.') }}%</span></div>
          @foreach($a['criteria'] as $i => $c)
            <div class="mb-2"><label class="text-sm font-medium">{{ $c['name'] }}</label>
              @if(!empty($c['description']))<p class="text-xs text-slate-500">{{ $c['description'] }}</p>@endif
              @if(!empty($c['levels']))
                <ul class="my-1 space-y-0.5 text-xs text-slate-600">@foreach($c['levels'] as $n => $d)<li><b>Skor {{ $n }}:</b> {{ $d }}</li>@endforeach</ul>
              @endif
              <div class="mt-1 flex items-center gap-2"><input type="number" step="{{ $c['max'] <= 4 ? 1 : 0.5 }}" min="0" max="{{ $c['max'] }}" name="scores[{{ $k }}][{{ $i }}]" value="{{ old("scores.$k.$i", $s->grade?->criterion_scores[$k][$i] ?? '') }}" class="input !w-24" required><span class="text-xs text-slate-400">/ {{ rtrim(rtrim(number_format($c['max'],2),'0'),'.') }}</span></div></div>
          @endforeach
          @if($s->grade)<div class="text-xs text-slate-500">Skor aspek: <b>{{ $s->grade->aspect_scores[$k] ?? 0 }}</b>/100</div>@endif
        </div>
      @endforeach
      @if($s->grade)<div class="rounded-xl bg-blue-50 p-3 text-center"><div class="text-xs text-blue-700">Nilai akhir tersimpan</div><div class="text-3xl font-extrabold text-blue-900">{{ $s->grade->final_score }}</div></div>@endif
      <div><label class="label">Komentar untuk siswa</label><textarea name="comment" rows="4" class="input">{{ old('comment', $s->grade?->comment) }}</textarea></div>
      <p class="text-xs text-slate-500">Nilai akhir dihitung otomatis dari skor kriteria × bobot rubrik. Siswa hanya melihat nilai setelah dipublikasikan.</p>
      <div class="flex flex-wrap gap-2">
        <button class="btn btn-ghost" name="publish" value="0">Simpan</button>
        <button class="btn btn-primary" name="publish" value="1">Simpan & publikasikan</button>
      </div>
    </form>
    @if($s->grade)
      <form method="POST" action="{{ route('guru.grading.toggle', $s) }}" class="mt-3">@csrf
        <button class="btn btn-ghost w-full">{{ $s->grade->published ? 'Tarik publikasi nilai' : 'Publikasikan nilai tersimpan' }}</button></form>
    @endif
    @endif
  </div>
</div>
@endsection