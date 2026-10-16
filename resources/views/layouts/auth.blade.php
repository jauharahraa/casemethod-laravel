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
  .pill-ico{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:1.75rem;height:1.75rem;border-radius:9999px;border:1.5px solid #a8c3d4;color:#5f7cae;background:#fff}
  .pill-input{width:100%;border:0;border-radius:9999px;background:#fff;padding:.9rem 1rem .9rem 3.4rem;font-size:.875rem;color:#3d2a4f;box-shadow:0 10px 26px -12px rgba(115,143,189,.65),0 0 0 1px #e6ecf3;outline:none;transition:box-shadow .2s}
  .pill-input::placeholder{color:#a3acbd}
  .pill-input:focus{box-shadow:0 10px 26px -12px rgba(115,143,189,.7),0 0 0 2px #738fbd}
  .pill-btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border:0;border-radius:9999px;background:linear-gradient(90deg,#6b88b8,#c47b9f);color:#fff;font-weight:600;font-size:.875rem;padding:.8rem 2rem;box-shadow:0 12px 26px -10px rgba(115,143,189,.95);cursor:pointer;transition:transform .2s,filter .2s}
  .pill-btn:hover{transform:translateY(-2px);filter:brightness(1.05)}
  @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
  @media (prefers-reduced-motion:no-preference){.fl{animation:floaty 6s ease-in-out infinite}.fl2{animation:floaty 7.5s ease-in-out infinite .8s}}
</style>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.453.0/dist/umd/lucide.min.js"></script>
</head>
<body class="relative min-h-screen overflow-x-hidden bg-[#f8f6f9] font-sans text-[#3d2a4f] antialiased">

{{-- Hiasan lingkaran latar --}}
<div class="pointer-events-none absolute -left-60 -top-60 h-[640px] w-[640px] rounded-full bg-[#dbd6df]/45"></div>
<div class="pointer-events-none absolute -left-40 -top-40 h-[480px] w-[480px] rounded-full bg-[#a8c3d4]/45"></div>
<div class="pointer-events-none absolute -bottom-72 -right-48 h-[620px] w-[620px] rounded-full bg-[#eec6c7]/45"></div>
<div class="pointer-events-none absolute -bottom-52 -right-24 h-[420px] w-[420px] rounded-full bg-[#cc8eb1]/25"></div>

<div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-8">
  <div class="relative w-full overflow-hidden rounded-3xl bg-white shadow-[0_30px_80px_-30px_rgba(115,143,189,.55)]">

    {{-- Panel kanan: lingkaran konsentris + ilustrasi --}}
    <div class="absolute inset-y-0 right-0 hidden w-[52%] overflow-hidden lg:block">
      <div class="absolute -right-[18%] top-1/2 aspect-square w-[135%] -translate-y-1/2 rounded-full bg-[#f6e3e5]"></div>
      <div class="absolute -right-[14%] top-1/2 aspect-square w-[118%] -translate-y-1/2 rounded-full bg-[#e3e3ec]"></div>
      <div class="absolute -right-[10%] top-1/2 aspect-square w-full -translate-y-1/2 rounded-full bg-gradient-to-br from-[#a8c3d4] via-[#c4b6cf] to-[#db88a4]"></div>

      <svg viewBox="0 0 460 400" class="absolute left-[10%] top-1/2 w-[76%] -translate-y-1/2" role="img" aria-label="Ilustrasi laptop, tanaman, dan cangkir">
        <defs>
          <path id="leaf" d="M372 262 C360 226 358 190 372 148 C386 190 384 226 372 262Z"/>
        </defs>
        <ellipse cx="235" cy="322" rx="205" ry="30" fill="#5f7cae" opacity=".14"/>

        {{-- cangkir --}}
        <path d="M41 214v26a17 7 0 0 0 34 0v-26z" fill="#f8f5fa"/>
        <path d="M75 222c11 0 11 15 0 15" fill="none" stroke="#cfc8da" stroke-width="3" stroke-linecap="round"/>
        <ellipse cx="58" cy="214" rx="17" ry="7" fill="#fff" stroke="#e6e1ec"/>
        <ellipse cx="58" cy="216" rx="13" ry="5" fill="#db88a4"/>

        {{-- sisi tebal laptop --}}
        <polygon points="93,250 214,320 214,328 93,258" fill="#cfc8da"/>
        <polygon points="301,270 214,320 214,328 301,278" fill="#b9b1c8"/>

        {{-- dek keyboard --}}
        <g transform="matrix(0.866 0.5 -0.866 0.5 180 200)">
          <rect x="0" y="0" width="140" height="100" rx="3" fill="#f3eff6"/>
          @for($r = 0; $r < 4; $r++)
            @for($c = 0; $c < 10; $c++)
              <rect x="{{ 12 + $c * 11.5 }}" y="{{ 10 + $r * 11 }}" width="8" height="8" rx="1.5" fill="#738fbd" opacity=".9"/>
            @endfor
          @endfor
          <rect x="48" y="66" width="44" height="22" rx="3" fill="#cc8eb1"/>
        </g>

        {{-- layar --}}
        <polygon points="187,66 308,136 308,266 187,196" fill="#46608f"/>
        <g transform="matrix(0.866 0.5 0 1 180 70)">
          <rect x="0" y="0" width="140" height="130" rx="4" fill="#5f7cae"/>
          <rect x="10" y="12" width="120" height="106" rx="2" fill="#fff"/>
          <rect x="18" y="20" width="52" height="7" rx="3" fill="#dbd6df"/>
          <rect x="18" y="36" width="104" height="9" rx="4" fill="#eec6c7"/>
          <rect x="18" y="50" width="82" height="9" rx="4" fill="#a8c3d4"/>
          <rect x="18" y="64" width="94" height="9" rx="4" fill="#cc8eb1"/>
          <rect x="18" y="82" width="44" height="28" rx="4" fill="#738fbd" opacity=".85"/>
          <rect x="70" y="82" width="52" height="28" rx="4" fill="#dbd6df"/>
        </g>

        {{-- tanaman --}}
        <use href="#leaf" fill="#8aa8c8" transform="rotate(-38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
        <use href="#leaf" fill="#8aa8c8" transform="rotate(38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
        <use href="#leaf" fill="#738fbd" transform="rotate(-18 372 262)"/>
        <use href="#leaf" fill="#a8c3d4" transform="rotate(18 372 262)"/>
        <use href="#leaf" fill="#5f7cae" transform="translate(372 262) scale(1.08) translate(-372 -262)"/>
        <path d="M346 262v44a26 12 0 0 0 52 0v-44z" fill="#fff" stroke="#e6e1ec"/>
        <ellipse cx="372" cy="262" rx="26" ry="12" fill="#f4f0f6" stroke="#e6e1ec"/>
        <ellipse cx="372" cy="262" rx="20" ry="8" fill="#b07a97"/>

        {{-- hiasan --}}
        <g class="fl"><circle cx="70" cy="92" r="6" fill="#fff" opacity=".9"/><circle cx="410" cy="80" r="5" fill="#fff" opacity=".8"/></g>
        <g class="fl2"><path d="M120 60l3 9 9 3-9 3-3 9-3-9-9-3 9-3z" fill="#fff" opacity=".9"/><path d="M420 150l2.5 7 7 2.5-7 2.5-2.5 7-2.5-7-7-2.5 7-2.5z" fill="#fff" opacity=".85"/></g>
      </svg>
    </div>

    {{-- Navbar --}}
    <header class="relative z-10 flex items-center justify-between gap-4 px-6 py-5 md:px-12">
      <div class="flex items-center gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-base font-extrabold tracking-tight">
          <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-[#a8c3d4] to-[#cc8eb1] text-white"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
          <span class="hidden sm:inline">Case Method AI</span>
        </a>
        <nav class="hidden items-center gap-6 text-[13px] font-medium text-[#5f7cae] md:flex">
          <a href="{{ route('home') }}" class="hover:text-[#c4708f]">Beranda</a>
          <a href="{{ route('home') }}#fitur" class="hover:text-[#c4708f]">Fitur</a>
          <a href="{{ route('home') }}#alur" class="hover:text-[#c4708f]">Alur belajar</a>
        </nav>
      </div>
      <a href="{{ $switch[1] }}" class="inline-flex items-center gap-2 rounded-full bg-[#5f7cae] px-5 py-2 text-[13px] font-semibold text-white shadow-md transition hover:bg-[#4d6a9c]">{{ $switch[0] }}</a>
    </header>

    {{-- Konten --}}
    <div class="relative z-10 grid lg:grid-cols-2">
      <section class="flex items-center px-6 pb-14 pt-6 md:px-12 lg:min-h-[540px] lg:pt-0">
        <div class="mx-auto w-full max-w-sm">
          <div class="mb-8 flex gap-6">
            @foreach($tabs as [$label, $url, $active])
              <a href="{{ $url }}" class="flex-1 border-b-2 pb-2 text-center text-xl font-bold transition {{ $active ? 'border-[#738fbd] text-[#5f7cae]' : 'border-slate-200 text-slate-300 hover:text-[#5f7cae]' }}">{{ $label }}</a>
            @endforeach
          </div>

          @if(session('ok'))<div class="mb-5 rounded-2xl border border-[#a8c3d4] bg-[#eef3f7] px-4 py-3 text-sm text-[#4d6a9c]">{{ session('ok') }}</div>@endif
          @if($errors->any())
            <div class="mb-5 rounded-2xl border border-[#eec6c7] bg-[#fbeff0] px-4 py-3 text-sm text-[#a5486c]">
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