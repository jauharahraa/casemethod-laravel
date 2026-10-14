@extends('layouts.app', ['mode' => 'guru'])
@section('title', $classroom->name)
@section('content')
<a href="{{ route('guru.classrooms') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Semua kelas</a>
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
  <div><h1 class="text-2xl font-bold text-blue-900">{{ $classroom->name }}</h1><p class="text-slate-500">{{ $classroom->subject->name }}</p></div>
  <div class="rounded-xl bg-blue-900 px-4 py-2 text-white"><div class="text-xs text-blue-200">Kode kelas (dibagikan ke siswa)</div><div class="font-mono text-2xl font-bold tracking-widest">{{ $classroom->code }}</div></div>
</div>
<div class="grid gap-4 lg:grid-cols-3">
  <div class="space-y-4">
    <form method="POST" action="{{ route('guru.students.store', $classroom) }}" class="card space-y-3">@csrf
      <h2 class="font-bold">Tambah siswa</h2>
      <div><label class="label">Nama lengkap</label><input name="full_name" value="{{ old('full_name') }}" class="input" required></div>
      <div><label class="label">Nomor peserta (unik)</label><input name="participant_number" value="{{ old('participant_number') }}" class="input" required></div>
      <button class="btn btn-primary w-full">Tambah</button>
    </form>
    <form method="POST" action="{{ route('guru.students.bulk', $classroom) }}" class="card space-y-3">@csrf
      <h2 class="font-bold">Tambah banyak sekaligus</h2>
      <p class="text-xs text-slate-500">Satu siswa per baris: <code>Nama Lengkap, Nomor Peserta</code></p>
      <textarea name="bulk" rows="5" class="input font-mono" placeholder="Budi Santoso, 2024001&#10;Siti Aminah, 2024002"></textarea>
      <button class="btn btn-dark w-full">Impor</button>
    </form>
  </div>
  <div class="card lg:col-span-2">
    <h2 class="mb-3 font-bold">Daftar siswa ({{ $classroom->students->count() }})</h2>
    <div class="overflow-x-auto"><table class="w-full"><thead><tr><th class="th">Nama</th><th class="th">No. peserta</th><th class="th"></th></tr></thead><tbody>
      @forelse($classroom->students as $s)
        <tr class="border-t border-slate-100"><td class="td">{{ $s->full_name }}</td><td class="td font-mono">{{ $s->participant_number }}</td>
          <td class="td text-right"><form method="POST" action="{{ route('guru.students.remove', [$classroom, $s]) }}" onsubmit="return confirm('Keluarkan siswa dari kelas?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Keluarkan</button></form></td></tr>
      @empty
        <tr><td class="td text-slate-500" colspan="3">Belum ada siswa.</td></tr>
      @endforelse
    </tbody></table></div>
    <form method="POST" action="{{ route('guru.classrooms.destroy', $classroom) }}" class="mt-4 border-t pt-3" onsubmit="return confirm('Hapus kelas ini beserta kasus dan jawabannya?')">@csrf @method('DELETE')<button class="text-sm text-red-600 hover:underline">Hapus kelas</button></form>
  </div>
</div>
@endsection
