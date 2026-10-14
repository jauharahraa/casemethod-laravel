<div class="grid gap-3 md:grid-cols-3">
@foreach($rubric['aspects'] as $a)
  <div class="rounded-xl border border-slate-200 p-3">
    <div class="flex items-center justify-between"><h4 class="font-semibold text-blue-900">{{ $a['label'] }}</h4><span class="badge bg-blue-100 text-blue-700">{{ rtrim(rtrim(number_format($a['weight'],2),'0'),'.') }}%</span></div>
    <ul class="mt-2 space-y-1.5 text-sm">
      @foreach($a['criteria'] as $c)
        <li><span class="font-medium">{{ $c['name'] }}</span> <span class="text-xs text-slate-400">(skala 0–{{ rtrim(rtrim(number_format($c['max'],2),'0'),'.') }})</span>
          @if(!empty($c['description']))<br><span class="text-slate-500">{{ $c['description'] }}</span>@endif
          @if(!empty($c['levels']))
            <ul class="mt-1 space-y-0.5 text-xs text-slate-600">
              @foreach($c['levels'] as $n => $d)<li><b>Skor {{ $n }}:</b> {{ $d }}</li>@endforeach
            </ul>
          @endif
        </li>
      @endforeach
    </ul>
  </div>
@endforeach
</div>