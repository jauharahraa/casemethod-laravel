@php
  // Hanya kelas & mapel milik guru yang sedang login.
  $exClassrooms = \App\Models\Classroom::where('user_id', auth()->id())->with('subject:id,name')->orderBy('name')->get();
  $exSubjects = $exClassrooms->pluck('subject')->filter()->unique('id')->sortBy('name')->values();
  $exData = $exClassrooms->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'subject_id' => $c->subject_id])->values();
@endphp
<section class="card mt-6"
         x-data="{ subject: '', classroom: '', all: @js($exData), get options() { return this.all.filter(c => String(c.subject_id) === String(this.subject)); } }">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-[#3E828E]">Ekspor</p>
      <h2 class="font-display text-xl font-bold text-[#27153D]">Export Nilai Case Method</h2>
      <p class="mt-1 max-w-2xl text-sm text-[#8B7C9B]">Unduh rekap nilai akhir dari guru per kelas dalam format Excel. Skor dan feedback AI tidak disertakan, dan kasus yang belum dinilai tidak dihitung sebagai nol.</p>
    </div>
    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[#DCEBE2] text-[#2F6772]"><i data-lucide="file-spreadsheet" class="h-6 w-6"></i></span>
  </div>

  @if($exClassrooms->isEmpty())
    <p class="mt-4 rounded-2xl bg-[#FFF5F6] p-4 text-sm text-[#8B7C9B]">Belum ada kelas. Buat materi dan kelas terlebih dahulu.</p>
  @else
    <form method="GET" action="{{ route('guru.export.grades') }}" class="mt-4 grid gap-4 md:grid-cols-4 md:items-end">
      <div>
        <label class="label">Materi</label>
        <select name="subject_id" x-model="subject" @change="classroom = ''" class="input" required>
          <option value="">Pilih materi</option>
          @foreach($exSubjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label">Kelas</label>
        <select name="classroom_id" x-model="classroom" :disabled="!subject" class="input" required>
          <option value="" x-text="subject ? 'Pilih kelas' : 'Pilih materi dulu'"></option>
          <template x-for="c in options" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
        </select>
      </div>
      <label class="flex items-center gap-2 pb-2 text-sm text-[#5E4B73]">
        <input type="checkbox" name="only_published" value="1" class="h-4 w-4 rounded accent-[#3E828E]"> Hanya nilai yang sudah dipublikasikan
      </label>
      <button class="btn btn-primary" :disabled="!subject || !classroom"><i data-lucide="download" class="h-4 w-4"></i> Unduh Excel (.xlsx)</button>
    </form>
  @endif
</section>