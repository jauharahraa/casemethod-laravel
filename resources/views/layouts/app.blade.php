<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Beranda') — Case Method AI</title>
<script src="https://cdn.tailwindcss.com"></script>
<style type="text/tailwindcss">
@layer components {
  .btn{@apply inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition disabled:opacity-50 cursor-pointer}
  .btn-primary{@apply bg-blue-600 text-white hover:bg-blue-700}
  .btn-dark{@apply bg-blue-900 text-white hover:bg-blue-800}
  .btn-ghost{@apply border border-slate-300 bg-white text-slate-700 hover:bg-slate-50}
  .btn-danger{@apply bg-red-600 text-white hover:bg-red-700}
  .card{@apply rounded-2xl border border-slate-200 bg-white p-5 shadow-sm}
  .input{@apply w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200}
  .label{@apply mb-1 block text-sm font-medium text-slate-700}
  .badge{@apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold}
  .th{@apply px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500}
  .td{@apply px-3 py-2.5 text-sm text-slate-700}
}
</style>
<style>[x-cloak]{display:none !important}</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.453.0/dist/umd/lucide.min.js"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
@php
  $mode = $mode ?? 'guest';
  $links = $mode === 'guru' ? [
    ['guru.dashboard','Dashboard','layout-dashboard'],['guru.subjects','Mapel','book-open'],['guru.classrooms','Kelas','users'],
    ['guru.cases','Kasus','file-text'],['guru.grading','Penilaian','check-square'],['guru.recap','Rekap','table'],
  ] : ($mode === 'siswa' ? [['siswa.dashboard','Dashboard','layout-dashboard']] : []);
@endphp
<header class="bg-blue-900 text-white shadow">
  <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
    <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold"><i data-lucide="graduation-cap" class="h-6 w-6 text-blue-300"></i> Case Method AI</a>
    @if($mode !== 'guest')
      <form method="POST" action="{{ route($mode === 'guru' ? 'guru.logout' : 'siswa.logout') }}">@csrf
        <button class="btn bg-white/10 text-white hover:bg-white/20"><i data-lucide="log-out" class="h-4 w-4"></i> Keluar</button>
      </form>
    @endif
  </div>
  @if($links)
  <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-3 pb-2">
    @foreach($links as [$route,$label,$icon])
      @php $active = $route === 'guru.dashboard' ? request()->routeIs('guru.dashboard') : request()->routeIs($route.'*'); @endphp
      <a href="{{ route($route) }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-1.5 text-sm {{ $active ? 'bg-blue-600 font-semibold' : 'text-blue-100 hover:bg-white/10' }}">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>{{ $label }}
      </a>
    @endforeach
  </nav>
  @endif
</header>

<main class="mx-auto max-w-6xl px-4 py-6">
  @if(session('ok'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('ok') }}</div>@endif
  @if(session('warn'))<div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('warn') }}</div>@endif
  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
      <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif
  @yield('content')
</main>
<script>document.addEventListener('DOMContentLoaded',function(){lucide.createIcons();});</script>
@stack('scripts')
</body>
</html>
