<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Case Method AI — Belajar Berpikir Kritis</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: {
  fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
  colors: { lav: { DEFAULT: '#a78bea', dark: '#8f70e0', soft: '#f3ecfd', ink: '#1f1b2d' } }
} } }
</script>
<style>
  html { scroll-behavior: smooth; }
  [x-cloak] { display: none !important; }
  .lucide { stroke-width: 1.4; }
  @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
  @media (prefers-reduced-motion: no-preference) {
    .fl  { animation: floaty 6s ease-in-out infinite; }
    .fl2 { animation: floaty 7.5s ease-in-out infinite .8s; }
    .fl3 { animation: floaty 5s ease-in-out infinite 1.6s; }
  }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.453.0/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-white font-sans text-lav-ink antialiased">

{{-- ============ NAVBAR ============ --}}
<header class="mx-auto max-w-6xl px-4 pt-5" x-data="{ open: false }">
  <div class="flex items-center justify-between gap-4">
    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold tracking-tight">
      <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-[#b9a6f5] to-[#8f70e0] text-white"><i data-lucide="graduation-cap" class="h-5 w-5"></i></span>
      Case Method AI
    </a>
    <nav class="hidden items-center gap-7 text-[11px] font-semibold uppercase tracking-wide md:flex">
      <a href="#beranda" class="hover:text-lav-dark">Beranda</a>
      <a href="#fitur" class="hover:text-lav-dark">Fitur</a>
      <a href="#alur" class="hover:text-lav-dark">Alur belajar</a>
      <a href="#mulai" class="hover:text-lav-dark">Mulai</a>
    </nav>
    <div class="hidden items-center gap-5 md:flex">
      <a href="{{ route('siswa.login') }}" class="flex items-center gap-1.5 text-[12px] font-semibold uppercase tracking-wide hover:text-lav-dark"><i data-lucide="user-round" class="h-4 w-4"></i> Masuk siswa</a>
      <a href="{{ route('guru.login') }}" class="inline-flex items-center gap-2 rounded-full border border-violet-300 px-5 py-2 text-[12px] font-semibold uppercase tracking-wide transition hover:bg-violet-50">Masuk guru <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></a>
    </div>
    <button type="button" class="md:hidden" @click="open = !open" aria-label="Menu"><i data-lucide="menu" class="h-6 w-6"></i></button>
  </div>
  <div x-show="open" x-cloak x-transition class="mt-3 space-y-1 rounded-2xl border border-violet-100 bg-white p-4 text-sm font-semibold shadow-lg md:hidden">
    <a href="#fitur" @click="open = false" class="block rounded-lg px-3 py-2 hover:bg-violet-50">Fitur</a>
    <a href="#alur" @click="open = false" class="block rounded-lg px-3 py-2 hover:bg-violet-50">Alur belajar</a>
    <a href="{{ route('siswa.login') }}" class="block rounded-lg px-3 py-2 hover:bg-violet-50">Masuk siswa</a>
    <a href="{{ route('guru.login') }}" class="block rounded-lg bg-lav px-3 py-2 text-center text-white">Masuk guru</a>
  </div>
</header>

{{-- ============ HERO ============ --}}
<section id="beranda" class="mx-auto mt-4 max-w-6xl px-4">
  <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#fdf0f5] via-[#f7effb] to-[#efe4fa]">
    <div class="pointer-events-none absolute -bottom-44 right-[-6%] h-[540px] w-[540px] rounded-full bg-[#e3d3f7]/80"></div>

    <div class="relative grid items-center gap-6 px-6 pb-6 pt-12 md:grid-cols-2 md:px-12 md:pt-16">
      <div>
        <span class="inline-block rounded-full border border-violet-200 bg-white/60 px-3 py-1 text-[11px] font-medium uppercase tracking-wide">Pembelajaran Case Method</span>
        <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl md:text-[3.3rem]">Belajar Case Method<br class="hidden sm:block"> Lebih Cerdas dengan AI</h1>
        <p class="mt-5 max-w-md text-lg leading-relaxed text-slate-700">Identifikasi masalah, analisis, lalu temukan solusi. AI memberi feedback yang membantumu berpikir, sementara nilai akhir tetap dari gurumu.</p>
        <div class="mt-8 flex flex-wrap items-center gap-5">
          <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-2 rounded-full bg-lav px-7 py-3.5 text-[13px] font-semibold uppercase tracking-wide text-white shadow-[0_10px_24px_-8px_rgba(143,112,224,.8)] transition hover:-translate-y-0.5 hover:bg-lav-dark">Masuk sebagai siswa <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></a>
          <a href="{{ route('guru.register') }}" class="text-[13px] font-semibold uppercase tracking-wide hover:text-lav-dark">Daftar sebagai guru</a>
        </div>
      </div>

      {{-- Ilustrasi hero --}}
      <div class="relative">
        <svg viewBox="0 0 520 430" class="mx-auto h-auto w-full max-w-[540px]" role="img" aria-label="Ilustrasi kasus dengan feedback AI">
          <defs>
            <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#ebe4fb"/></linearGradient>
            <linearGradient id="g2" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#8f7be8"/><stop offset="1" stop-color="#b9a6f5"/></linearGradient>
            <linearGradient id="g3" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#6ea8ff"/><stop offset="1" stop-color="#3b6fe0"/></linearGradient>
            <filter id="sh1" x="-20%" y="-20%" width="150%" height="160%"><feDropShadow dx="0" dy="16" stdDeviation="14" flood-color="#6d4fd0" flood-opacity=".22"/></filter>
            <symbol id="st" viewBox="0 0 24 24"><path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.6 7-6.2-3.7-6.2 3.7 1.6-7L2 9.2l7.1-.6z" fill="#f9c846" stroke="#e7a92a" stroke-width="1" stroke-linejoin="round"/></symbol>
          </defs>

          <g fill="#fff" class="fl2"><ellipse cx="150" cy="58" rx="46" ry="20"/><ellipse cx="190" cy="50" rx="34" ry="22"/><ellipse cx="118" cy="66" rx="30" ry="14"/></g>
          <g fill="#fff" class="fl3"><ellipse cx="462" cy="150" rx="40" ry="16"/><ellipse cx="480" cy="140" rx="26" ry="18"/></g>
          <g fill="#fff" opacity=".92"><ellipse cx="260" cy="352" rx="150" ry="26"/><ellipse cx="200" cy="342" rx="60" ry="26"/><ellipse cx="330" cy="340" rx="70" ry="28"/></g>

          <g filter="url(#sh1)">
            <rect x="90" y="96" width="330" height="240" rx="26" fill="url(#g1)" stroke="#d9cff7" stroke-width="2"/>
            <circle cx="116" cy="122" r="4.5" fill="#f4a3b8"/><circle cx="132" cy="122" r="4.5" fill="#f6d27a"/><circle cx="148" cy="122" r="4.5" fill="#a6e3c4"/>
            <rect x="172" y="114" width="200" height="18" rx="9" fill="#efeafc"/>
            <circle cx="355" cy="123" r="4" fill="none" stroke="#8f7be8" stroke-width="1.5"/>
            <rect x="112" y="146" width="286" height="170" rx="16" fill="#fff" opacity=".85"/>

            <text x="128" y="177" font-size="12" font-weight="600" fill="#4c3f8a">Identifikasi</text>
            <rect x="216" y="168" width="130" height="12" rx="6" fill="#ece6fb"/><rect x="216" y="168" width="98" height="12" rx="6" fill="url(#g2)"/>
            <text x="356" y="178" font-size="11" fill="#7a6bb5">3/4</text>

            <text x="128" y="217" font-size="12" font-weight="600" fill="#4c3f8a">Analisis</text>
            <rect x="216" y="208" width="130" height="12" rx="6" fill="#ece6fb"/><rect x="216" y="208" width="130" height="12" rx="6" fill="url(#g2)"/>
            <text x="356" y="218" font-size="11" fill="#7a6bb5">4/4</text>

            <text x="128" y="257" font-size="12" font-weight="600" fill="#4c3f8a">Solusi</text>
            <rect x="216" y="248" width="130" height="12" rx="6" fill="#ece6fb"/><rect x="216" y="248" width="98" height="12" rx="6" fill="url(#g2)"/>
            <text x="356" y="258" font-size="11" fill="#7a6bb5">3/4</text>

            <rect x="128" y="284" width="92" height="20" rx="10" fill="#e9e1fb"/><text x="174" y="298" font-size="11" font-weight="600" text-anchor="middle" fill="#6b55c8">Revisi 1x</text>
            <rect x="228" y="284" width="108" height="20" rx="10" fill="#e9e1fb"/><text x="282" y="298" font-size="11" font-weight="600" text-anchor="middle" fill="#6b55c8">Draft otomatis</text>
          </g>

          <g class="fl3" filter="url(#sh1)">
            <path d="M46 176h88a14 14 0 0 1 14 14v34a14 14 0 0 1-14 14H92l-18 16v-16H46a14 14 0 0 1-14-14v-34a14 14 0 0 1 14-14z" fill="#f4577a"/>
            <rect x="52" y="210" width="10" height="14" rx="2" fill="#fff"/><rect x="68" y="202" width="10" height="22" rx="2" fill="#fff"/>
            <rect x="84" y="194" width="10" height="30" rx="2" fill="#fff"/><rect x="100" y="186" width="10" height="38" rx="2" fill="#fff"/>
          </g>

          <g class="fl">
            <circle cx="432" cy="74" r="34" fill="#f4577a"/><circle cx="432" cy="74" r="25" fill="#fff"/><circle cx="432" cy="74" r="17" fill="#f4577a"/><circle cx="432" cy="74" r="8" fill="#fff"/>
            <line x1="432" y1="74" x2="468" y2="40" stroke="#6d5a3a" stroke-width="3" stroke-linecap="round"/>
            <polygon points="468,40 482,36 472,26" fill="#f9c846"/>
          </g>

          <g class="fl2">
            <circle cx="62" cy="112" r="34" fill="#fff6d6" opacity=".7"/>
            <circle cx="62" cy="110" r="15" fill="#fcd34d"/><rect x="55" y="124" width="14" height="8" rx="2" fill="#cbd5e1"/>
          </g>

          <g class="fl3">
            <circle cx="444" cy="306" r="20" fill="#f6c453" stroke="#e0a526" stroke-width="3"/><circle cx="444" cy="306" r="11" fill="none" stroke="#e0a526" stroke-width="2"/>
            <circle cx="70" cy="330" r="14" fill="#f6c453" stroke="#e0a526" stroke-width="3"/>
          </g>

          <use href="#st" x="40" y="60" width="18" height="18" class="fl"/>
          <use href="#st" x="398" y="18" width="22" height="22" class="fl2"/>
          <use href="#st" x="470" y="222" width="20" height="20" class="fl3"/>
          <use href="#st" x="262" y="56" width="16" height="16" class="fl"/>
          <use href="#st" x="478" y="320" width="14" height="14" class="fl2"/>
          <circle cx="30" cy="260" r="5" fill="#b9a6f5"/><circle cx="500" cy="190" r="4" fill="#f4a3b8"/><circle cx="300" cy="30" r="4" fill="#b9a6f5"/>
        </svg>
      </div>
    </div>

    {{-- Strip alur --}}
    <div class="relative border-t border-white/70 px-6 pb-8 pt-6 md:px-12">
      <div class="flex flex-wrap items-center justify-between gap-x-8 gap-y-4">
        <span class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Alur belajar</span>
        <span class="font-serif text-2xl italic">Baca materi</span>
        <span class="text-xl font-light uppercase tracking-[0.2em]">Identifikasi</span>
        <span class="text-2xl font-extrabold italic tracking-tight">Analisis</span>
        <span class="text-xl font-light uppercase tracking-[0.2em]">Solusi</span>
        <span class="font-serif text-2xl">Feedback<span class="text-lav-dark"> AI</span></span>
      </div>
    </div>
  </div>
</section>

{{-- ============ FITUR ============ --}}
<section id="fitur" class="relative mx-auto mt-20 max-w-6xl px-4 text-center">
  <div class="pointer-events-none absolute left-1/2 top-10 -z-10 h-80 w-80 -translate-x-1/2 rounded-full bg-violet-50/80 blur-2xl"></div>
  <span class="absolute left-[6%] top-0 hidden h-11 w-11 rounded-full bg-gradient-to-br from-emerald-300 to-yellow-200 shadow-md md:block"></span>
  <h2 class="text-3xl font-extrabold tracking-tight">Fitur Unggulan</h2>
  <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-700">Semua yang dibutuhkan guru dan siswa untuk belajar lewat studi kasus, dari materi sampai nilai, dalam satu tempat.</p>
  <a href="#alur" class="mt-5 inline-flex items-center gap-1.5 text-[12px] font-semibold uppercase tracking-wide hover:text-lav-dark">Lihat alur belajar <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></a>

  <div class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 md:grid-cols-5">
    @foreach([
      ['book-open', 'Materi Interaktif', 'Bacaan bergaya, lengkap daftar isi'],
      ['clipboard-list', 'Kasus & Rubrik', '3 aspek, skala skor 0–4'],
      ['sparkles', 'Feedback AI', 'Skor, alasan, dan saran'],
      ['rotate-ccw', 'Revisi 1 Kali', 'Berpikir ulang, lalu kunci'],
      ['table', 'Rekap Nilai', 'Filter dan ekspor CSV'],
    ] as [$icon, $title, $desc])
      <div class="group flex flex-col items-center gap-3">
        <span class="grid h-[76px] w-[76px] place-items-center rounded-full bg-[#fbeefb] text-[#4a3d7a] ring-8 ring-[#fdf6fd] transition duration-300 group-hover:-translate-y-1.5 group-hover:bg-lav group-hover:text-white"><i data-lucide="{{ $icon }}" class="h-8 w-8"></i></span>
        <span class="text-[15px] font-semibold">{{ $title }}</span>
        <span class="max-w-[10rem] text-xs text-slate-500">{{ $desc }}</span>
      </div>
    @endforeach
  </div>
</section>

{{-- ============ TENTANG ============ --}}
<section class="mx-auto mt-24 grid max-w-6xl items-center gap-10 px-4 md:grid-cols-2">
  <svg viewBox="0 0 480 380" class="mx-auto h-auto w-full max-w-[480px]" role="img" aria-label="Ilustrasi dasbor nilai dan statistik">
    <defs>
      <linearGradient id="g4" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#cfc4f6"/><stop offset="1" stop-color="#a99aef"/></linearGradient>
      <linearGradient id="g5" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#6ea8ff"/><stop offset="1" stop-color="#3b6fe0"/></linearGradient>
    </defs>
    <g filter="url(#sh1)"><rect x="70" y="56" width="320" height="232" rx="24" fill="url(#g4)"/></g>
    <rect x="86" y="72" width="288" height="200" rx="16" fill="#ece7fc"/>
    <rect x="100" y="84" width="120" height="12" rx="6" fill="#fff"/><rect x="232" y="84" width="70" height="12" rx="6" fill="#fff"/>
    <rect x="104" y="112" width="124" height="64" rx="8" fill="url(#g5)"/>
    <path d="M96 196 C130 112,150 226,192 150 S256 118,310 164" fill="none" stroke="#f4577a" stroke-width="3" stroke-linecap="round"/>
    <rect x="252" y="130" width="10" height="36" rx="3" fill="#6ea8ff"/><rect x="270" y="116" width="10" height="50" rx="3" fill="#6ea8ff"/><rect x="288" y="140" width="10" height="26" rx="3" fill="#6ea8ff"/>
    <rect x="150" y="196" width="170" height="58" rx="10" fill="#fff"/>
    <circle cx="182" cy="222" r="10" fill="none" stroke="#f4577a" stroke-width="4"/><circle cx="235" cy="222" r="10" fill="none" stroke="#6d4fd0" stroke-width="4"/><circle cx="288" cy="222" r="10" fill="none" stroke="#f6c453" stroke-width="4"/>
    <g class="fl2"><rect x="296" y="150" width="136" height="80" rx="10" fill="#f6c453"/><rect x="296" y="166" width="136" height="14" fill="#e7a92a"/><rect x="310" y="196" width="50" height="6" rx="3" fill="#fff"/><rect x="310" y="208" width="80" height="6" rx="3" fill="#fff" opacity=".7"/></g>
    <g class="fl"><circle cx="58" cy="200" r="26" fill="#f4577a"/><path d="M58 200 L58 174 A26 26 0 0 1 84 200 Z" fill="#f6c453"/></g>
    <g class="fl3"><circle cx="54" cy="268" r="18" fill="#f6c453" stroke="#e0a526" stroke-width="3"/><circle cx="404" cy="312" r="16" fill="#f6c453" stroke="#e0a526" stroke-width="3"/></g>
    <rect x="98" y="244" width="40" height="40" rx="10" fill="#fff"/><path d="M108 264 l8 8 14-16" fill="none" stroke="#5b45d6" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    <text x="52" y="66" font-size="30" font-weight="800" fill="#5b45d6">%</text><text x="408" y="104" font-size="30" font-weight="800" fill="#5b45d6">%</text>
    <use href="#st" x="420" y="40" width="22" height="22" class="fl"/><use href="#st" x="30" y="150" width="14" height="14" class="fl2"/><use href="#st" x="440" y="250" width="16" height="16" class="fl3"/>
  </svg>

  <div>
    <h2 class="flex items-center gap-2 text-3xl font-extrabold leading-tight tracking-tight">Satu platform untuk guru dan siswa <i data-lucide="target" class="h-7 w-7 shrink-0 text-rose-500"></i></h2>
    <p class="mt-4 leading-relaxed text-slate-600">Guru menyiapkan materi, kasus, dan rubrik, lalu membagikan kode kelas. Siswa mengerjakan, menerima feedback yang mengarahkan cara berpikir, dan hanya melihat nilai setelah gurumu memublikasikannya. Semua tercatat rapi dan aman.</p>
    <div class="mt-6 flex flex-wrap gap-2.5">
      @foreach(['Kode kelas', 'Draft otomatis', 'Rubrik 0–4', 'Feedback AI', 'Revisi 1x', 'Materi Markdown', 'Publikasi nilai', 'Ekspor CSV'] as $chip)
        <span class="rounded-full border border-slate-300 px-4 py-1.5 text-[12px] font-medium uppercase tracking-wide transition hover:border-lav hover:bg-lav-soft">{{ $chip }}</span>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ALUR ============ --}}
<section id="alur" class="mx-auto mt-24 max-w-6xl px-4">
  <div class="text-center">
    <h2 class="text-3xl font-extrabold tracking-tight">Alur Belajar</h2>
    <p class="mx-auto mt-3 max-w-xl text-slate-600">Langkah sederhana untuk guru dan siswa.</p>
  </div>
  <div class="mt-10 grid gap-6 md:grid-cols-2">
    @foreach([
      ['Untuk guru', 'presentation', 'from-[#fdf0f5] to-[#f1e7fb]', ['Buat mata pelajaran dan kelas', 'Tambah siswa, bagikan kode kelas', 'Buat materi dan kasus dengan rubrik', 'Nilai jawaban, lalu publikasikan nilai']],
      ['Untuk siswa', 'user-round', 'from-[#f1e7fb] to-[#e8defa]', ['Masuk dengan kode kelas', 'Baca materi pembelajaran', 'Kerjakan kasus, kirim jawaban', 'Baca feedback AI, revisi sekali, lihat nilai']],
    ] as [$who, $icon, $grad, $steps])
      <div class="rounded-3xl bg-gradient-to-br {{ $grad }} p-7">
        <div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-full bg-white text-lav-dark shadow-sm"><i data-lucide="{{ $icon }}" class="h-5 w-5"></i></span><h3 class="text-xl font-bold">{{ $who }}</h3></div>
        <ol class="mt-5 space-y-3">
          @foreach($steps as $i => $s)
            <li class="flex items-start gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-lav text-xs font-bold text-white">{{ $i + 1 }}</span><span class="pt-0.5 text-[15px]">{{ $s }}</span></li>
          @endforeach
        </ol>
      </div>
    @endforeach
  </div>
</section>

{{-- ============ CTA ============ --}}
<section id="mulai" class="mx-auto mt-24 max-w-6xl px-4">
  <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#b9a6f5] via-[#a78bea] to-[#8f70e0] px-6 py-14 text-center text-white md:px-12">
    <span class="pointer-events-none absolute -left-10 -top-10 h-44 w-44 rounded-full bg-white/10"></span>
    <span class="pointer-events-none absolute -bottom-16 -right-10 h-60 w-60 rounded-full bg-white/10"></span>
    <h2 class="relative text-3xl font-extrabold tracking-tight md:text-4xl">Siap mulai belajar dengan studi kasus?</h2>
    <p class="relative mx-auto mt-3 max-w-lg text-white/90">Masuk sebagai siswa dengan kode kelas, atau daftar sebagai guru untuk membuat kelas pertamamu.</p>
    <div class="relative mt-8 flex flex-wrap items-center justify-center gap-4">
      <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-[13px] font-semibold uppercase tracking-wide text-lav-dark transition hover:-translate-y-0.5 hover:shadow-lg">Masuk siswa <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></a>
      <a href="{{ route('guru.login') }}" class="inline-flex items-center gap-2 rounded-full border border-white/70 px-7 py-3.5 text-[13px] font-semibold uppercase tracking-wide transition hover:bg-white/15">Masuk guru</a>
      <a href="{{ route('guru.register') }}" class="text-[13px] font-semibold uppercase tracking-wide underline-offset-4 hover:underline">Daftar guru</a>
    </div>
  </div>
</section>

<footer class="mx-auto mt-16 max-w-6xl px-4 pb-10 text-center text-xs text-slate-500">
  © {{ date('Y') }} Case Method AI · Feedback AI bersifat formatif; nilai resmi ditentukan guru.
</footer>

<script>document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });</script>
</body>
</html>