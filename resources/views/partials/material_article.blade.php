{{-- Tampilan baca materi (dipakai siswa & pratinjau guru). Variabel: $material --}}
<div id="readBar" class="fixed left-0 top-0 z-50 h-1 w-0 bg-gradient-to-r from-sky-300 to-blue-600"></div>

<section class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br {{ $material->gradient() }} p-6 text-white shadow-lg sm:p-10">
  <i data-lucide="{{ $material->icon }}" class="absolute -bottom-8 -right-6 h-56 w-56 text-white/15"></i>
  <div class="relative max-w-3xl">
    <div class="mb-3 flex flex-wrap gap-2 text-xs font-semibold">
      <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 backdrop-blur"><i data-lucide="users" class="h-3.5 w-3.5"></i>{{ $material->classroom->name }}</span>
      <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 backdrop-blur"><i data-lucide="clock" class="h-3.5 w-3.5"></i>{{ $material->readingMinutes() }} menit baca</span>
      <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 backdrop-blur"><i data-lucide="calendar" class="h-3.5 w-3.5"></i>{{ $material->created_at->translatedFormat('d M Y') }}</span>
    </div>
    <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl">{{ $material->title }}</h1>
    @if($material->summary)<p class="mt-3 text-base text-white/90 sm:text-lg">{{ $material->summary }}</p>@endif
  </div>
</section>

<div class="grid gap-6 lg:grid-cols-4">
  <aside id="tocWrap" class="hidden lg:block">
    <div class="sticky top-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-blue-900"><i data-lucide="list" class="h-4 w-4"></i>Daftar isi</h3>
      <ul id="toc" class="space-y-0.5"></ul>
    </div>
  </aside>

  <article class="card p-6 sm:p-8 lg:col-span-3">
    <div id="materiBody" class="materi-body">{!! $material->html() !!}</div>
    @if($material->link_url)
      <div class="mt-8 flex items-center gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-blue-600 text-white"><i data-lucide="external-link" class="h-5 w-5"></i></span>
        <div class="min-w-0 flex-1"><div class="text-sm font-bold text-blue-900">Sumber tambahan</div><div class="truncate text-xs text-slate-500">{{ $material->link_url }}</div></div>
        <a href="{{ $material->link_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary shrink-0">{{ $material->link_label ?: 'Buka tautan' }}</a>
      </div>
    @endif
  </article>
</div>

<style>
.materi-body{font-size:1.02rem;line-height:1.8;color:#334155;overflow-wrap:anywhere}
.materi-body h1,.materi-body h2,.materi-body h3{color:#0b2a5b;font-weight:800;line-height:1.3;scroll-margin-top:1rem}
.materi-body h1{font-size:1.75rem;margin:1.6em 0 .6em}
.materi-body h2{font-size:1.4rem;margin:1.8em 0 .6em;padding-left:.75rem;border-left:5px solid #2563eb}
.materi-body h3{font-size:1.15rem;margin:1.4em 0 .5em}
.materi-body h1:first-child,.materi-body h2:first-child,.materi-body h3:first-child{margin-top:0}
.materi-body p{margin:.9em 0}
.materi-body a{color:#2563eb;text-decoration:underline}
.materi-body ul,.materi-body ol{margin:.9em 0;padding-left:1.5rem}
.materi-body ul{list-style:disc}.materi-body ol{list-style:decimal}
.materi-body li{margin:.3em 0}
.materi-body li::marker{color:#2563eb;font-weight:700}
.materi-body blockquote{margin:1.2em 0;padding:.9rem 1.1rem;border-left:5px solid #f59e0b;background:#fffbeb;border-radius:0 .75rem .75rem 0;color:#78350f}
.materi-body blockquote p{margin:.3em 0}
.materi-body code{background:#eff6ff;color:#1d4ed8;padding:.15em .4em;border-radius:.4rem;font-size:.9em}
.materi-body pre{background:#0f172a;color:#e2e8f0;padding:1rem;border-radius:.9rem;overflow-x:auto;margin:1.2em 0}
.materi-body pre code{background:none;color:inherit;padding:0}
.materi-body img{max-width:100%;height:auto;border-radius:1rem;margin:1.2em auto;box-shadow:0 4px 14px rgba(15,23,42,.12)}
.materi-body hr{border:0;height:2px;background:linear-gradient(90deg,#2563eb,transparent);margin:2em 0}
.materi-body table{display:block;overflow-x:auto;width:100%;border-collapse:collapse;margin:1.2em 0;font-size:.95rem}
.materi-body th{background:#0b2a5b;color:#fff;text-align:left;padding:.6rem .8rem}
.materi-body td{padding:.6rem .8rem;border-bottom:1px solid #e2e8f0}
.materi-body tr:nth-child(even) td{background:#f8fafc}
</style>
<script>
(function () {
  var body = document.getElementById('materiBody'), toc = document.getElementById('toc'),
      wrap = document.getElementById('tocWrap'), bar = document.getElementById('readBar');
  var heads = body.querySelectorAll('h1, h2, h3');
  if (!heads.length) { wrap.style.display = 'none'; }
  heads.forEach(function (h, i) {
    h.id = 'bagian-' + (i + 1);
    var li = document.createElement('li'), a = document.createElement('a');
    a.href = '#' + h.id; a.textContent = h.textContent;
    a.className = 'block rounded-lg px-2 py-1 text-slate-600 hover:bg-blue-50 hover:text-blue-700 ' + (h.tagName === 'H3' ? 'ml-3 text-xs' : 'text-sm font-medium');
    li.appendChild(a); toc.appendChild(li);
  });
  window.addEventListener('scroll', function () {
    var r = body.getBoundingClientRect(), total = Math.max(body.offsetHeight - window.innerHeight * 0.5, 1);
    bar.style.width = (Math.min(1, Math.max(0, (window.innerHeight * 0.3 - r.top) / total)) * 100) + '%';
  }, { passive: true });
})();
</script>