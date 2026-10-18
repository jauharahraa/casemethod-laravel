<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Masuk') — Case Method AI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } } }</script>
<style>
  .pill-wrap{position:relative;display:block}
  .pill-ico{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:1.75rem;height:1.75rem;border-radius:9999px;border:1.5px solid #A6C9B6;color:#3E828E;background:#fff}
  .pill-input{width:100%;border:0;border-radius:9999px;background:#fff;padding:.9rem 1rem .9rem 3.4rem;font-size:.875rem;color:#27153D;box-shadow:0 10px 26px -12px rgba(62,130,142,.65),0 0 0 1px #F0D9DB;outline:none;transition:box-shadow .2s}
  .pill-input::placeholder{color:#A89BB5}
  .pill-input:focus{box-shadow:0 10px 26px -12px rgba(62,130,142,.7),0 0 0 2px #3E828E}
  .pill-btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border:0;border-radius:9999px;background:#3E828E;color:#fff;font-weight:600;font-size:.875rem;padding:.8rem 2rem;box-shadow:0 12px 26px -10px rgba(62,130,142,.9);cursor:pointer;transition:transform .2s,background .2s}
  .pill-btn:hover{transform:translateY(-2px);background:#2F6772}
  @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  @media (prefers-reduced-motion:no-preference){.fl{animation:floaty 6s ease-in-out infinite}.fl2{animation:floaty 7.5s ease-in-out infinite .8s}}
</style>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.453.0/dist/umd/lucide.min.js"></script>
</head>
<body class="relative min-h-screen overflow-x-hidden bg-[#FFF6F7] font-sans text-[#27153D] antialiased">

{{-- Hiasan lingkaran latar --}}
<div class="pointer-events-none absolute -left-60 -top-60 h-[640px] w-[640px] rounded-full bg-[#FFEBED]"></div>
<div class="pointer-events-none absolute -left-40 -top-40 h-[480px] w-[480px] rounded-full bg-[#A6C9B6]/45"></div>
<div class="pointer-events-none absolute -bottom-72 -right-48 h-[620px] w-[620px] rounded-full bg-[#F6B6B7]/45"></div>
<div class="pointer-events-none absolute -bottom-52 -right-24 h-[420px] w-[420px] rounded-full bg-[#3E828E]/15"></div>

<div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-8">
  <div class="relative w-full overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_-30px_rgba(62,130,142,.55)]">

    {{-- Panel kanan: lingkaran konsentris + ilustrasi --}}
    <div class="absolute inset-y-0 right-0 hidden w-[52%] overflow-hidden lg:block">
      <div class="absolute -right-[18%] top-1/2 aspect-square w-[135%] -translate-y-1/2 rounded-full bg-[#FFEBED]"></div>
      <div class="absolute -right-[14%] top-1/2 aspect-square w-[118%] -translate-y-1/2 rounded-full bg-[#F6B6B7]"></div>
      <div class="absolute -right-[10%] top-1/2 aspect-square w-full -translate-y-1/2 rounded-full bg-gradient-to-br from-[#A6C9B6] to-[#3E828E]"></div>

      <svg viewBox="0 0 460 400" class="absolute left-[10%] top-1/2 w-[76%] -translate-y-1/2" role="img" aria-label="Ilustrasi laptop, tanaman, dan cangkir">
        <defs>
          <path id="leaf" d="M372 262 C360 226 358 190 372 148 C386 190 384 226 372 262Z"/>
        </defs>
        <ellipse cx="235" cy="322" rx="205" ry="30" fill="#3E828E" opacity=".14"/>

        {{-- cangkir --}}
        <path d="M41 214v26a17 7 0 0 0 34 0v-26z" fill="#FFF5F6"/>
        <path d="M75 222c11 0 11 15 0 15" fill="none" stroke="#E8CFD1" stroke-width="3" stroke-linecap="round"/>
        <ellipse cx="58" cy="214" rx="17" ry="7" fill="#fff" stroke="#F3D6D8"/>
        <ellipse cx="58" cy="216" rx="13" ry="5" fill="#F6B6B7"/>

        {{-- sisi tebal laptop --}}
        <polygon points="93,250 214,320 214,328 93,258" fill="#E8CFD1"/>
        <polygon points="301,270 214,320 214,328 301,278" fill="#D3AEB1"/>

        {{-- dek keyboard --}}
        <g transform="matrix(0.866 0.5 -0.866 0.5 180 200)">
          <rect x="0" y="0" width="140" height="100" rx="3" fill="#FFF5F6"/>
          @for($r = 0; $r < 4; $r++)
            @for($c = 0; $c < 10; $c++)
              <rect x="{{ 12 + $c * 11.5 }}" y="{{ 10 + $r * 11 }}" width="8" height="8" rx="1.5" fill="#3E828E" opacity=".9"/>
            @endfor
          @endfor
          <rect x="48" y="66" width="44" height="22" rx="3" fill="#A6C9B6"/>
        </g>

        {{-- layar --}}
        <polygon points="187,66 308,136 308,266 187,196" fill="#3B2554"/>
        <g transform="matrix(0.866 0.5 0 1 180 70)">
          <rect x="0" y="0" width="140" height="130" rx="4" fill="#27153D"/>
          <rect x="10" y="12" width="120" height="106" rx="2" fill="#fff"/>
          <rect x="18" y="20" width="52" height="7" rx="3" fill="#FFEBED"/>
          <rect x="18" y="36" width="104" height="9" rx="4" fill="#F6B6B7"/>
          <rect x="18" y="50" width="82" height="9" rx="4" fill="#A6C9B6"/>
          <rect x="18" y="64" width="94" height="9" rx="4" fill="#A6C9B6"/>
          <rect x="18" y="82" width="44" height="28" rx="4" fill="#3E828E" opacity=".85"/>
          <rect x="70" y="82" width="52" height="28" rx="4" fill="#FFEBED"/>
        </g>

        {{-- tanaman --}}
        <use href="#leaf" fill="#A6C9B6" transform="rotate(-38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
        <use href="#leaf" fill="#A6C9B6" transform="rotate(38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
        <use href="#leaf" fill="#27153D" transform="rotate(-18 372 262)"/>
        <use href="#leaf" fill="#A6C9B6" transform="rotate(18 372 262)"/>
        <use href="#leaf" fill="#FFEBED" transform="translate(372 262) scale(1.08) translate(-372 -262)"/>
        <path d="M346 262v44a26 12 0 0 0 52 0v-44z" fill="#fff" stroke="#F3D6D8"/>
        <ellipse cx="372" cy="262" rx="26" ry="12" fill="#FFF5F6" stroke="#F3D6D8"/>
        <ellipse cx="372" cy="262" rx="20" ry="8" fill="#3B2554"/>

        {{-- hiasan --}}
        <g class="fl"><circle cx="70" cy="92" r="6" fill="#fff" opacity=".9"/><circle cx="410" cy="80" r="5" fill="#fff" opacity=".8"/></g>
        <g class="fl2"><path d="M120 60l3 9 9 3-9 3-3 9-3-9-9-3 9-3z" fill="#fff" opacity=".9"/><path d="M420 150l2.5 7 7 2.5-7 2.5-2.5 7-2.5-7-7-2.5 7-2.5z" fill="#fff" opacity=".85"/></g>
      </svg>
    </div>

    {{-- Navbar --}}
    <header class="relative z-10 flex items-center justify-between gap-4 px-6 py-5 md:px-12">
      <div class="flex items-center gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-base font-extrabold tracking-tight">
          <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-[#A6C9B6] to-[#F6B6B7] text-white"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
          <span class="hidden sm:inline">Case Method AI</span>
        </a>
        <nav class="hidden items-center gap-6 text-[13px] font-medium text-[#3E828E] md:flex">
          <a href="{{ route('home') }}" class="hover:text-[#27153D]">Beranda</a>
          <a href="{{ route('home') }}#fitur" class="hover:text-[#27153D]">Fitur</a>
          <a href="{{ route('home') }}#alur" class="hover:text-[#27153D]">Alur belajar</a>
        </nav>
      </div>
      <a href="{{ $switch[1] }}" class="inline-flex items-center gap-2 rounded-full bg-[#3E828E] px-5 py-2 text-[13px] font-semibold text-white shadow-md transition hover:bg-[#2F6772]">{{ $switch[0] }}</a>
    </header>

    {{-- Konten --}}
    <div class="relative z-10 grid lg:grid-cols-2">
      <section class="flex items-center px-6 pb-14 pt-6 md:px-12 lg:min-h-[540px] lg:pt-0">
        <div class="mx-auto w-full max-w-sm">
          <div class="mb-8 flex gap-6">
            @foreach($tabs as [$label, $url, $active])
              <a href="{{ $url }}" class="flex-1 border-b-2 pb-2 text-center text-xl font-bold transition {{ $active ? 'border-[#3E828E] text-[#3E828E]' : 'border-slate-200 text-slate-300 hover:text-[#3E828E]' }}">{{ $label }}</a>
            @endforeach
          </div>

          @if(session('ok'))<div class="mb-5 rounded-2xl border border-[#A6C9B6] bg-[#EAF4EE] px-4 py-3 text-sm text-[#2F6772]">{{ session('ok') }}</div>@endif
          @if($errors->any())
            <div class="mb-5 rounded-2xl border border-[#F6B6B7] bg-[#FFEBED] px-4 py-3 text-sm text-[#9B3B49]">
              <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif

          @yield('content')
        </div>
      </section>
      <div class="hidden lg:block"></div>
    </div>
  </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });</script>
</body>
</html>