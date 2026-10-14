<div class="space-y-3">
@foreach(['identification'=>'Identifikasi Masalah','analysis'=>'Analisis Masalah','solution'=>'Solusi'] as $f => $l)
  <div><h5 class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $l }}</h5><p class="whitespace-pre-line rounded-lg bg-slate-50 p-3 text-sm">{{ $v->$f }}</p></div>
@endforeach
</div>
