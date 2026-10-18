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
/* Palet: Lavender Blush #FFEBED · Powder Blush #F6B6B7 · Muted Teal #A6C9B6 · Pacific Cyan #3E828E · Dark Amethyst #27153D.
   Skala warna bawaan Tailwind diganti turunan palet, sehingga seluruh halaman guru & siswa ikut berubah otomatis. */
const amethyst = {50:'#F6F2F9',100:'#EBE3F1',200:'#D7C9E3',300:'#BBA5CF',400:'#977BB4',500:'#745595',600:'#5A3F7A',700:'#46305F',800:'#35244A',900:'#27153D',950:'#190D29'};
const blush = {50:'#FFF5F6',100:'#FFEBED',200:'#FCD3D4',300:'#F6B6B7',400:'#EE9798',500:'#E27A7D',600:'#CC6065',700:'#9E4249',800:'#7E343A',900:'#632A2F',950:'#3D1A1D'};
tailwind.config = { theme: { extend: {
  fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'], display: ['Fraunces', 'Georgia', 'serif'] },
  colors: {
    blue:    {50:'#EAF4F5',100:'#D3E7EA',200:'#A9D0D6',300:'#7DB5BE',400:'#559BA7',500:'#4C94A0',600:'#3E828E',700:'#2F6772',800:'#1F4E58',900:'#27153D',950:'#1A0E2B'},
    sky:     {50:'#F3F9F6',100:'#E4F1EA',200:'#CFE3D8',300:'#A6C9B6',400:'#8BB8A2',500:'#6FA28C',600:'#58867A',700:'#466B61',800:'#39554E',900:'#2D423D',950:'#1B2926'},
    emerald: {50:'#F1F8F4',100:'#DFEFE6',200:'#C4DFD0',300:'#A6C9B6',400:'#7FB09A',500:'#5C9A83',600:'#3E8A72',700:'#2C6A57',800:'#25574A',900:'#1E453B',950:'#102821'},
    indigo: amethyst, violet: amethyst, purple: amethyst, fuchsia: amethyst,
    rose: blush, pink: blush, amber: blush
  }
} } }
</script>
<style type="text/tailwindcss">
@layer components {
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.9rem;padding:.55rem 1.1rem;font-size:.875rem;font-weight:600;transition:all .2s;cursor:pointer}
  .btn:disabled{opacity:.5}
  .btn-primary{background:#3E828E;color:#fff;box-shadow:0 8px 20px -10px rgba(62,130,142,.9)}
  .btn-primary:hover{background:#2F6772;transform:translateY(-1px)}
  .btn-dark{background:#27153D;color:#fff}
  .btn-dark:hover{background:#3B2554}
  .btn-blush{background:#F6B6B7;color:#27153D}
  .btn-blush:hover{background:#FFEBED}
  .btn-ghost{background:#fff;color:#5E4B73;border:1px solid #F0CFD1}
  .btn-ghost:hover{background:#FFF5F6}
  .btn-danger{background:#D9626F;color:#fff}
  .btn-danger:hover{background:#C4565F}
  .card{border-radius:1.5rem;background:#fff;padding:1.25rem;border:1px solid #F9DCDD;box-shadow:0 14px 34px -22px rgba(62,130,142,.55)}
  .input{width:100%;border-radius:.9rem;border:1px solid #F0CFD1;background:#fff;padding:.55rem .85rem;font-size:.875rem;color:#27153D}
  .input:focus{outline:none;border-color:#3E828E;box-shadow:0 0 0 3px rgba(62,130,142,.22)}
  .label{display:block;margin-bottom:.25rem;font-size:.8125rem;font-weight:600;color:#5E4B73}
  .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.15rem .65rem;font-size:.75rem;font-weight:600}
  .th{padding:.6rem .75rem;text-align:left;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#8B7C9B}
  .td{padding:.7rem .75rem;font-size:.875rem;color:#5E4B73}
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
<body class="relative flex min-h-screen flex-col bg-[#FFF6F7] font-sans text-[#27153D] antialiased">
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
  <div class="absolute -right-32 -top-32 h-[420px] w-[420px] rounded-full bg-[#F6B6B7]/35"></div>
  <div class="absolute -left-40 top-1/3 h-[420px] w-[420px] rounded-full bg-[#A6C9B6]/30"></div>
  <div class="absolute -bottom-40 right-1/4 h-[380px] w-[380px] rounded-full bg-[#FFEBED]"></div>
</div>

<header class="sticky top-0 z-40 shadow-[0_10px_28px_-18px_rgba(39,21,61,.8)]" style="background-image: linear-gradient(90deg,#27153D 0%,#3E828E 100%)">
  <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
    <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
      <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-[#A6C9B6] to-[#F6B6B7] text-[#27153D] shadow-sm"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
      <span class="whitespace-nowrap leading-tight">
        <span class="block font-display text-lg font-bold text-[#FFEBED]">Case Method AI</span>
        <span class="block text-[11px] font-semibold text-[#A6C9B6]">{{ $mode === 'guru' ? 'Ruang Guru' : ($mode === 'siswa' ? 'Ruang Siswa' : 'Pembelajaran') }}</span>
      </span>
    </a>

    @if($links)
    <nav class="hidden items-center gap-1 xl:flex">
      @foreach($links as [$route,$label,$icon])
        @php $active = $route === 'guru.dashboard' ? request()->routeIs('guru.dashboard') : request()->routeIs($route.'*'); @endphp
        <a href="{{ route($route) }}" class="flex items-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold transition {{ $active ? 'bg-[#F6B6B7] text-[#27153D] shadow' : 'text-[#FFEBED] hover:bg-white/10' }}">
          <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>{{ $label }}
        </a>
      @endforeach
    </nav>
    @endif

    @if($mode !== 'guest')
    <div class="flex shrink-0 items-center gap-3">
      <span class="hidden items-center gap-2 rounded-full bg-[#FFEBED] py-1 pl-1 pr-3 text-sm font-semibold text-[#27153D] shadow-sm 2xl:inline-flex">
        <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-[#3E828E] to-[#A6C9B6] text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($who ?: '?', 0, 1)) }}</span>
        <span class="max-w-[10rem] truncate">{{ $who }}</span>
      </span>
      <form method="POST" action="{{ route($mode === 'guru' ? 'guru.logout' : 'siswa.logout') }}">@csrf
        <button class="btn btn-blush !py-2"><i data-lucide="log-out" class="h-4 w-4"></i> Keluar</button>
      </form>
    </div>
    @endif
  </div>

  @if($links)
  <nav class="mx-auto flex max-w-6xl gap-1.5 overflow-x-auto px-3 pb-2.5 xl:hidden">
    @foreach($links as [$route,$label,$icon])
      @php $active = $route === 'guru.dashboard' ? request()->routeIs('guru.dashboard') : request()->routeIs($route.'*'); @endphp
      <a href="{{ route($route) }}" class="flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-semibold {{ $active ? 'bg-[#F6B6B7] text-[#27153D] shadow' : 'bg-white/10 text-[#FFEBED]' }}">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>{{ $label }}
      </a>
    @endforeach
  </nav>
  @endif
</header>

<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
  @if(session('ok'))<div class="mb-5 flex items-start gap-2 rounded-2xl border border-[#CFE3D8] bg-[#EAF4EE] px-4 py-3 text-sm text-[#245A64]"><i data-lucide="circle-check" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ session('ok') }}</span></div>@endif
  @if(session('warn'))<div class="mb-5 flex items-start gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"><i data-lucide="triangle-alert" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ session('warn') }}</span></div>@endif
  @if($errors->any())
    <div class="mb-5 rounded-2xl border border-[#F6B6B7] bg-[#FFEBED] px-4 py-3 text-sm text-[#9B3B49]">
      <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif
  @yield('content')
</main>

<footer class="relative z-10 mt-12">
  <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="block h-8 w-full text-[#27153D]" aria-hidden="true"><path d="M0 30C240 70 480 -10 720 30S1200 70 1440 20V60H0Z" fill="currentColor"/></svg>
  <div class="bg-[#27153D] px-4 pb-7 pt-2 text-[#FFEBED]">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 text-sm">
      <span class="flex items-center gap-2 font-display text-base font-bold"><span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-[#A6C9B6] to-[#F6B6B7] text-[#27153D]"><i data-lucide="graduation-cap" class="h-4 w-4"></i></span> Case Method AI</span>
      <span class="font-medium text-[#FFEBED]/80">Feedback AI bersifat formatif · nilai resmi ditentukan guru</span>
      <span class="font-medium text-[#A6C9B6]">© {{ date('Y') }}</span>
    </div>
  </div>
</footer>

<script>document.addEventListener('DOMContentLoaded',function(){lucide.createIcons();});</script>
@stack('scripts')
</body>
</html>