@extends('layouts.app', ['mode' => 'guru'])
@section('title', $material->exists ? 'Ubah Materi' : 'Materi Baru')
@section('content')
<h1 class="mb-4 text-2xl font-bold text-blue-900">{{ $material->exists ? 'Ubah Materi' : 'Materi Baru' }}</h1>
@if($classrooms->isEmpty())
  <div class="card text-sm text-amber-700">Buat <a class="underline" href="{{ route('guru.classrooms') }}">kelas</a> terlebih dahulu.</div>
@else
@php
  $theme = old('theme', $material->theme ?: 'blue');
  $icon = old('icon', $material->icon ?: 'book-open');
@endphp
<form method="POST" action="{{ $material->exists ? route('guru.materials.update', $material) : route('guru.materials.store') }}" class="space-y-5"
      x-data="{ theme: @js($theme), icon: @js($icon), title: @js(old('title', $material->title)), themes: @js(\App\Models\Material::THEMES) }">
  @csrf @if($material->exists) @method('PUT') @endif

  <section class="card space-y-4">
    <h2 class="font-bold text-blue-900">Informasi materi</h2>
    <div class="grid gap-4 md:grid-cols-3">
      <div class="md:col-span-2"><label class="label">Judul materi</label><input name="title" x-model="title" class="input" required></div>
      <div><label class="label">Kelas</label><select name="classroom_id" class="input" required>@foreach($classrooms as $c)<option value="{{ $c->id }}" @selected(old('classroom_id', $material->classroom_id) == $c->id)>{{ $c->name }} — {{ $c->subject->name }}</option>@endforeach</select></div>
    </div>
    <div><label class="label">Ringkasan singkat (tampil di kartu)</label><input name="summary" value="{{ old('summary', $material->summary) }}" maxlength="300" class="input" placeholder="Satu-dua kalimat tentang isi materi"></div>
  </section>

  <section class="card">
    <h2 class="mb-3 font-bold text-blue-900">Tampilan kartu</h2>
    <div class="grid gap-5 lg:grid-cols-2">
      <div class="space-y-4">
        <div><span class="label">Warna sampul</span>
          <div class="flex flex-wrap gap-3">
            @foreach(\App\Models\Material::THEMES as $key => $grad)
              <label class="cursor-pointer"><input type="radio" name="theme" value="{{ $key }}" x-model="theme" class="peer sr-only" @checked($theme === $key)>
                <span class="block h-11 w-16 rounded-xl bg-gradient-to-br {{ $grad }} ring-offset-2 transition peer-checked:ring-2 peer-checked:ring-blue-600"></span></label>
            @endforeach
          </div></div>
        <div><span class="label">Ikon</span>
          <div class="flex flex-wrap gap-2">
            @foreach(\App\Models\Material::ICONS as $ic)
              <label class="cursor-pointer"><input type="radio" name="icon" value="{{ $ic }}" x-model="icon" class="peer sr-only" @checked($icon === $ic)>
                <span class="grid h-11 w-11 place-items-center rounded-xl border border-slate-300 text-slate-500 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700"><i data-lucide="{{ $ic }}" class="h-5 w-5"></i></span></label>
            @endforeach
          </div></div>
      </div>
      <div>
        <span class="label">Pratinjau sampul</span>
        <div class="relative h-36 overflow-hidden rounded-2xl bg-gradient-to-br p-4 text-white shadow" :class="themes[theme]">
          <div class="absolute bottom-3 left-4 right-4"><div class="line-clamp-2 text-xl font-extrabold leading-tight" x-text="title || 'Judul materi'"></div></div>
          <div class="absolute right-3 top-3 rounded-full bg-white/25 px-2.5 py-1 text-xs font-bold backdrop-blur">Baru</div>
        </div>
      </div>
    </div>
  </section>

  <section class="card space-y-3">
    <div class="flex flex-wrap items-center justify-between gap-2"><h2 class="font-bold text-blue-900">Isi materi</h2><span class="text-xs text-slate-500">Mendukung Markdown</span></div>
    <textarea name="content" rows="18" class="input font-mono" required placeholder="## Judul bagian&#10;Tulis isi materi di sini…">{{ old('content', $material->content) }}</textarea>
    <details class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
      <summary class="cursor-pointer font-semibold text-blue-700">Contoh penulisan (Markdown)</summary>
      <pre class="mt-2 overflow-x-auto text-xs leading-relaxed">## Judul bagian
### Sub-judul
Teks biasa dengan **tebal**, *miring*, dan `kode`.

- Poin pertama
- Poin kedua

1. Langkah satu
2. Langkah dua

&gt; Catatan penting atau tips.

| Istilah | Arti |
|---|---|
| Biaya | Pengorbanan sumber daya |

[Teks tautan](https://contoh.com)
![Keterangan gambar](https://contoh.com/gambar.jpg)</pre>
    </details>
  </section>

  <section class="card grid gap-4 md:grid-cols-3">
    <div class="md:col-span-2"><label class="label">Tautan sumber tambahan (opsional)</label><input type="url" name="link_url" value="{{ old('link_url', $material->link_url) }}" class="input" placeholder="https://…"></div>
    <div><label class="label">Teks tombol (opsional)</label><input name="link_label" value="{{ old('link_label', $material->link_label) }}" maxlength="60" class="input" placeholder="Tonton video"></div>
  </section>

  <div class="flex flex-wrap gap-2">
    <button name="action" value="draft" class="btn btn-ghost"><i data-lucide="save" class="h-4 w-4"></i> Simpan {{ $material->isPublished() ? '(tetap terpublikasi)' : 'sebagai draft' }}</button>
    <button name="action" value="publish" class="btn btn-primary"><i data-lucide="send" class="h-4 w-4"></i> {{ $material->isPublished() ? 'Simpan' : 'Simpan & publikasikan' }}</button>
    <a href="{{ route('guru.materials') }}" class="btn btn-ghost">Batal</a>
  </div>
</form>
@endif
@endsection