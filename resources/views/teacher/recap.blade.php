@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Rekap Nilai')
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-2"><h1 class="text-2xl font-bold text-blue-900">Rekap Nilai</h1>
  <a href="{{ route('guru.recap.export', request()->query()) }}" class="btn btn-dark"><i data-lucide="download" class="h-4 w-4"></i> Ekspor CSV (Excel)</a></div>
<form method="GET" class="card mb-4 flex flex-wrap items-end gap-3">
  <div><label class="label">Cari siswa</label><input name="q" value="{{ request('q') }}" class="input" placeholder="Nama / nomor peserta"></div>
  <div><label class="label">Kelas</label><select name="classroom_id" class="input"><option value="">Semua</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" @selected(request('classroom_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
  <button class="btn btn-dark">Terapkan</button>
</form>
<div class="card overflow-x-auto p-0">
<table class="w-full"><thead class="bg-slate-50"><tr><th class="th">Siswa</th><th class="th">Kelas</th><th class="th">Kasus</th><th class="th">Progres siswa</th><th class="th">Status</th><th class="th">Nilai</th></tr></thead><tbody>
@forelse($rows as $x)
  @php [$lbl,$cls] = \App\Models\Submission::badge($x['status'], $x['graded'], false); $p = $progress[$x['student_id']]; $pct = $p['total'] ? round($p['done']/$p['total']*100) : 0; @endphp
  <tr class="border-t border-slate-100">
    <td class="td"><div class="font-medium">{{ $x['student'] }}</div><div class="text-xs text-slate-400">{{ $x['number'] }}</div></td>
    <td class="td">{{ $x['class'] }}</td><td class="td">{{ $x['case'] }}</td>
    <td class="td"><div class="h-2 w-24 rounded-full bg-slate-200"><div class="h-2 rounded-full bg-blue-600" style="width: {{ $pct }}%"></div></div><div class="text-xs text-slate-400">{{ $p['done'] }}/{{ $p['total'] }} kasus</div></td>
    <td class="td">@if($x['sub_id'])<a href="{{ route('guru.grading.show', $x['sub_id']) }}"><span class="badge {{ $cls }}">{{ $lbl }}</span></a>@else<span class="badge {{ $cls }}">{{ $lbl }}</span>@endif @if($x['published'])<span class="text-xs text-emerald-600">· dipublikasikan</span>@endif</td>
    <td class="td font-semibold">{{ $x['score'] ?? '—' }}</td></tr>
@empty
  <tr><td class="td text-slate-500" colspan="6">Belum ada data. Rekap hanya memuat kasus yang sudah dipublikasikan.</td></tr>
@endforelse
</tbody></table></div>
@endsection
