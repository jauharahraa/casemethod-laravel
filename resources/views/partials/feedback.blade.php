@php
  $c = $feedback?->content ?? [];
  $labels = \App\Services\RubricService::ASPECTS;
  // Format lama (sebelum rubrik disederhanakan) tetap bisa ditampilkan.
  $legacy = [
    ['key' => 'strengths', 'title' => 'Kelebihan jawaban', 'icon' => 'thumbs-up', 'color' => 'text-emerald-700'],
    ['key' => 'weaknesses', 'title' => 'Kekurangan jawaban', 'icon' => 'alert-triangle', 'color' => 'text-amber-700'],
    ['key' => 'unmet_criteria', 'title' => 'Kriteria rubrik yang belum terpenuhi', 'icon' => 'list-checks', 'color' => 'text-red-700'],
    ['key' => 'suggestions', 'title' => 'Saran perbaikan', 'icon' => 'lightbulb', 'color' => 'text-blue-700'],
    ['key' => 'guiding_questions', 'title' => 'Pertanyaan pemantik', 'icon' => 'help-circle', 'color' => 'text-indigo-700'],
  ];
  $badge = fn ($score, $max) => ($score >= $max ? 'bg-emerald-100 text-emerald-700' : ($score >= ($max * 0.6) ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'));
@endphp
@if($feedback && $feedback->status === 'ok')
  <div class="space-y-3">
    @if($feedback->mode === 'simulasi')
      <div class="rounded-xl border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800"><b>MODE SIMULASI</b> — ini bukan AI sungguhan (API key Gemini belum diatur). Skor hanya perkiraan dari panjang jawaban.</div>
    @else
      <div class="text-xs text-slate-500">Dihasilkan oleh Gemini · bersifat formatif, bukan nilai resmi guru.</div>
    @endif

    @if(isset($c['aspects']))
      @foreach($labels as $key => $label)
        @php 
          $a = $c['aspects'][$key] ?? null; 
          $max = $a['max_score'] ?? ($key === 'solution' ? 4 : 3);
          $score = $a['score'] ?? 0;
        @endphp
        @if($a)
          <div class="rounded-xl border border-slate-200 p-3">
            <div class="mb-1 flex items-center justify-between gap-2">
              <h5 class="text-sm font-bold text-blue-900">{{ $label }}
                @if(!empty($a['status']))<span class="ml-1 text-xs font-semibold text-slate-500">· {{ $a['status'] }}</span>@endif
              </h5>
              <span class="badge {{ $badge($score, $max) }}">Skor {{ $score }}/{{ $max }}</span>
            </div>
            @if(!empty($a['reason']))<p class="text-sm text-slate-700"><b>Alasan:</b> {{ $a['reason'] }}</p>@endif
            @if(!empty($a['feedback']))<p class="mt-1 text-sm text-slate-700"><b>Feedback:</b> {{ $a['feedback'] }}</p>@endif
          </div>
        @endif
      @endforeach
      @isset($c['final_score'])
        <div class="rounded-xl bg-blue-50 p-3 text-center">
          <div class="text-xs text-blue-700">Perkiraan nilai (dihitung otomatis dari skor aspek × bobot)</div>
          <div class="text-3xl font-extrabold text-blue-900">{{ rtrim(rtrim(number_format($c['final_score'], 2), '0'), '.') }}<span class="text-base font-semibold text-blue-700"> / 100</span></div>
          <div class="text-xs text-slate-500">Hanya gambaran awal. Nilai resmi ditentukan guru.</div>
        </div>
      @endisset
      @if(!empty($c['conclusion']))
        <div class="rounded-xl border border-slate-200 p-3"><h5 class="mb-1 flex items-center gap-2 text-sm font-bold text-blue-900"><i data-lucide="message-square-text" class="h-4 w-4"></i>Kesimpulan</h5><p class="text-sm text-slate-700">{{ $c['conclusion'] }}</p></div>
      @endif
    @else
      @foreach($legacy as $itemInfo)
        @php $key = $itemInfo['key']; @endphp
        @if(!empty($c[$key]))
          <div class="rounded-xl border border-slate-200 p-3">
            <h5 class="mb-1 flex items-center gap-2 text-sm font-bold {{ $itemInfo['color'] }}"><i data-lucide="{{ $itemInfo['icon'] }}" class="h-4 w-4"></i>{{ $itemInfo['title'] }}</h5>
            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-700">@foreach($c[$key] as $item)<li>{{ $item }}</li>@endforeach</ul>
          </div>
        @endif
      @endforeach
    @endif
  </div>
@endif