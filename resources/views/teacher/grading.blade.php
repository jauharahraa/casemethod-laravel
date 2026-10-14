@extends('layouts.app', ['mode' => 'guru'])
@section('title', 'Penilaian')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">Penilaian</h1>
<form method="GET" class="card mb-4 flex flex-wrap items-end gap-3">
  <div><label class="label">Kasus</label><select name="case_id" class="input"><option value="">Semua</option>@foreach($cases as $c)<option value="{{ $c->id }}" @selected(request('case_id') == $c->id)>{{ $c->title }}</option>@endforeach</select></div>
  <div><label class="label">Tampilkan</label><select name="filter" class="input"><option value="">Semua pengumpulan</option><option value="ungraded" @selected(request('filter')==='ungraded')>Belum dinilai</option></select></div>
  <button class="btn btn-dark">Terapkan</button>
</form>
<div class="card overflow-x-auto p-0">
<table class="w-full"><thead class="bg-slate-50"><tr><th class="th">Siswa</th><th class="th">Kasus</th><th class="th">Status</th><th class="th">Nilai</th><th class="th"></th></tr></thead><tbody>
@forelse($subs as $s)
  @php [$lbl,$cls] = \App\Models\Submission::badge($s->status, (bool)$s->grade, (bool)$s->grade?->published); @endphp
  <tr class="border-t border-slate-100"><td class="td"><div class="font-medium">{{ $s->student->full_name }}</div><div class="text-xs text-slate-400">{{ $s->student->participant_number }}</div></td>
    <td class="td">{{ $s->case->title }}</td><td class="td"><span class="badge {{ $cls }}">{{ $lbl }}</span></td>
    <td class="td font-semibold">{{ $s->grade?->final_score ?? '—' }}</td>
    <td class="td text-right"><a href="{{ route('guru.grading.show', $s) }}" class="btn btn-ghost">{{ $s->status === 'locked' ? ($s->grade ? 'Lihat/ubah' : 'Nilai') : 'Lihat' }}</a></td></tr>
@empty
  <tr><td class="td text-slate-500" colspan="5">Belum ada pengumpulan.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-4">{{ $subs->links() }}</div>
@endsection
