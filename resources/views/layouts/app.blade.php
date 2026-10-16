<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Beranda') — Case Method AI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
/* Palet: #738fbd #a8c3d4 #dbd6df #eec6c7 #db88a4 #cc8eb1.
   Skala warna bawaan Tailwind (blue, indigo, sky, rose, violet, purple, fuchsia, pink) diganti dengan turunan palet,
   sehingga seluruh halaman guru & siswa otomatis ikut berubah warna. */
const mauve = {50:'#faf3f7',100:'#f4e5ee',200:'#ebcfdf',300:'#dfb2cc',400:'#d49dbd',500:'#cc8eb1',600:'#b3769a',700:'#955d7e',800:'#764964',900:'#5a374c',950:'#3b2331'};
tailwind.config = { theme: { extend: {
  fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'], display: ['Fraunces', 'Georgia', 'serif'] },
  colors: {
    blue:   {50:'#f3f6fb',100:'#e6ecf6',200:'#d0dbee',300:'#adc0de',400:'#8aa4cd',500:'#738fbd',600:'#5f7cae',700:'#4d6a9c',800:'#3f5584',900:'#33406b',950:'#262f4d'},
    indigo: {50:'#f7f5fa',100:'#ece8f2',200:'#ddd7e6',300:'#c6bdd4',400:'#ab9fc0',500:'#9186ab',600:'#776c92',700:'#5f5577',800:'#4a4260',900:'#383248',950:'#272233'},
    sky:    {50:'#f2f7fa',100:'#e2edf3',200:'#c9dce7',300:'#a8c3d4',400:'#90b0c6',500:'#7a9fb9',600:'#648aa5',700:'#4f7189',800:'#3f5a6e',900:'#2f4453',950:'#1f2d37'},
    rose:   {50:'#fdf3f5',100:'#fbe6ea',200:'#f6cfd8',300:'#eeb0c0',400:'#e49bb2',500:'#db88a4',600:'#c4708f',700:'#a65772',800:'#85445b',900:'#66344a',950:'#431f2f'},
    violet: mauve, purple: mauve, fuchsia: mauve, pink: mauve
  }
} } }
</script>
<style type="text/tailwindcss">
@layer components {
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.9rem;padding:.55rem 1.1rem;font-size:.875rem;font-weight:600;transition:all .2s;cursor:pointer}
  .btn:disabled{opacity:.5}
  .btn-primary{background-image:linear-gradient(90deg,#6b88b8,#c47b9f);color:#fff;box-shadow:0 8px 20px -10px rgba(115,143,189,.95)}
  .btn-primary:hover{filter:brightness(1.06);transform:translateY(-1px)}
  .btn-dark{background:#3f5584;color:#fff}
  .btn-dark:hover{background:#33406b}
  .btn-ghost{background:#fff;color:#475569;border:1px solid #e3dde6}
  .btn-ghost:hover{background:#faf6f7}
  .btn-danger{background:#d9627f;color:#fff}
  .btn-danger:hover{background:#c44e6c}
  .card{border-radius:1.5rem;background:#fff;padding:1.25rem;border:1px solid #f0e8ec;box-shadow:0 14px 34px -22px rgba(115,143,189,.55)}
  .input{width:100%;border-radius:.9rem;border:1px solid #e3dde6;background:#fff;padding:.55rem .85rem;font-size:.875rem;color:#3d2a4f}
  .input:focus{outline:none;border-color:#738fbd;box-shadow:0 0 0 3px rgba(115,143,189,.22)}
  .label{display:block;margin-bottom:.25rem;font-size:.8125rem;font-weight:600;color:#5b5470}
  .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.15rem .65rem;font-size:.75rem;font-weight:600}
  .th{padding:.6rem .75rem;text-align:left;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#8a8399}
  .td{padding:.7rem .75rem;font-size:.875rem;color:#4a4458}
}
</style>
<style>
  [x-cloak]{display:none !important}
  html{scroll-behavior:smooth}
  main h1{font-family:Fraunces,Georgia,serif;letter-spacing:-.01em}
  @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  @media (prefers-reduced-motion:no-preference){.fl{animation:floaty 6s ease-in-out infinite}.fl2{animation:floaty 7.5s ease-in-out infinite .8s}}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.453.0/dist/umd/lucide.min.js"></script>
</head>
<body class="relative min-h-screen bg-[#fdf8f7] font-sans text-[#3d2a4f] antialiased">
@php
  $mode = $mode ?? 'guest';
  $links = $mode === 'guru' ? [
    ['guru.dashboard','Dashboard','layout-dashboard'],['guru.subjects','Mapel','book-open'],['guru.classrooms','Kelas','users'],
    ['guru.materials','Materi','book-marked'],['guru.cases','Kasus','file-text'],['guru.grading','Penilaian','check-square'],['guru.recap','Rekap','table'],
  ] : ($mode === 'siswa' ? [['siswa.dashboard','Dashboard','layout-dashboard'],['siswa.materials','Materi','library']] : []);
  $who = $mode === 'guru' ? (auth()->user()->name ?? '') : ($mode === 'siswa' ? (request()->attributes->get('student')->full_name ?? '') : '');
@endphp

{{-- Hiasan latar --}}
<div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
  <div class="absolute -right-32 -top-32 h-[420px] w-[420px] rounded-full bg-[#eec6c7]/35"></div>
  <div class="absolute -left-40 top-1/3 h-[420px] w-[420px] rounded-full bg-[#a8c3d4]/25"></div>
  <div class="absolute -bottom-40 right-1/4 h-[380px] w-[380px] rounded-full bg-[#dbd6df]/40"></div>
</div>

<header class="sticky top-0 z-40 border-b border-[#f1e7eb] bg-[#fdf8f7]/90 backdrop-blur">
  <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-[#a8c3d4] to-[#cc8eb1] text-white shadow-sm"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
      <span class="leading-tight">
        <span class="block font-display text-lg font-bold text-[#33406b]">Case Method AI</span>
        <span class="block text-[11px] font-medium text-[#8a8399]">{{ $mode === 'guru' ? 'Ruang Guru' : ($mode === 'siswa' ? 'Ruang Siswa' : 'Pembelajaran') }}</span>
      </span>
    </a>

    @if($links)
    <nav class="hidden items-center gap-1 lg:flex">
      @foreach($links as [$route,$label,$icon])
        @php $active = $route === 'guru.dashboard' ? request()->routeIs('guru.dashboard') : request()->routeIs($route.'*'); @endphp
        <a href="{{ route($route) }}" class="flex items-center gap-2 border-b-2 px-3 py-2 text-sm font-semibold transition {{ $active ? 'border-[#db88a4] text-[#c4708f]' : 'border-transparent text-[#5b5470] hover:text-[#c4708f]' }}">
          <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>{{ $label }}
        </a>
      @endforeach
    </nav>
    @endif

    @if($mode !== 'guest')
    <div class="flex items-center gap-3">
      <span class="hidden items-center gap-2 rounded-full bg-white py-1 pl-1 pr-3 text-sm font-semibold text-[#5b5470] shadow-sm ring-1 ring-[#f0e8ec] xl:inline-flex">
        <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-[#a8c3d4] to-[#cc8eb1] text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($who ?: '?', 0, 1)) }}</span>
        <span class="max-w-[10rem] truncate">{{ $who }}</span>
      </span>
      <form method="POST" action="{{ route($mode === 'guru' ? 'guru.logout' : 'siswa.logout') }}">@csrf
        <button class="btn btn-ghost !py-2"><i data-lucide="log-out" class="h-4 w-4"></i> Keluar</button>
      </form>
    </div>
    @endif
  </div>

  @if($links)
  <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-3 pb-2 lg:hidden">
    @foreach($links as [$route,$label,$icon])
      @php $active = $route === 'guru.dashboard' ? request()->routeIs('guru.dashboard') : request()->routeIs($route.'*'); @endphp
      <a href="{{ route($route) }}" class="flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-semibold {{ $active ? 'bg-gradient-to-r from-[#6b88b8] to-[#c47b9f] text-white shadow' : 'bg-white text-[#5b5470] ring-1 ring-[#f0e8ec]' }}">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>{{ $label }}
      </a>
    @endforeach
  </nav>
  @endif
</header>

<main class="mx-auto max-w-6xl px-4 py-8">
  @if(session('ok'))<div class="mb-5 flex items-start gap-2 rounded-2xl border border-[#c9dce7] bg-[#eef5f9] px-4 py-3 text-sm text-[#3f5a6e]"><i data-lucide="circle-check" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ session('ok') }}</span></div>@endif
  @if(session('warn'))<div class="mb-5 flex items-start gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"><i data-lucide="triangle-alert" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ session('warn') }}</span></div>@endif
  @if($errors->any())
    <div class="mb-5 rounded-2xl border border-[#f1c4cf] bg-[#fdf1f4] px-4 py-3 text-sm text-[#a5486c]">
      <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif
  @yield('content')
</main>

<footer class="mt-16">
  <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="block h-8 w-full text-[#33406b]" aria-hidden="true"><path d="M0 30C240 70 480 -10 720 30S1200 70 1440 20V60H0Z" fill="currentColor"/></svg>
  <div class="bg-[#33406b] px-4 pb-8 pt-2 text-white">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 text-sm">
      <span class="flex items-center gap-2 font-display text-base font-bold"><i data-lucide="graduation-cap" class="h-5 w-5 text-[#a8c3d4]"></i> Case Method AI</span>
      <span class="text-white/70">Feedback AI bersifat formatif · nilai resmi ditentukan guru</span>
      <span class="text-white/60">© {{ date('Y') }}</span>
    </div>
  </div>
</footer>

<script>document.addEventListener('DOMContentLoaded',function(){lucide.createIcons();});</script>
@stack('scripts')
</body>
</html>