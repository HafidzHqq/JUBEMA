<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JUBEMA') · Jurnal Manajemen Waktu Belajar</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400;1,6..72,500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'system-ui', 'sans-serif'],
                        serif: ['Newsreader', 'Georgia', 'serif'],
                        display: ['Newsreader', 'Georgia', 'serif'],
                    },
                    colors: {
                        paper: '#F5F0E6',
                        sheet: '#FFFDF8',
                        ink: { DEFAULT: '#18283B', soft: '#4A5868', faint: '#7D8794' },
                        rule: '#DED5C4',
                        accent: { DEFAULT: '#0F766E', dark: '#0B5A54' },
                    }
                }
            }
        }
    </script>
    <script>
        try {
            if (localStorage.getItem('jubema_sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>
    <style>
        @media print {
            @page { margin: 1.5cm; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: #111827 !important; }
            main { max-width: none !important; padding: 0 !important; margin: 0 !important; }
        }
        @media (min-width: 1024px) {
            html.sidebar-collapsed #user-sidebar {
                width: 76px !important;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-text,
            html.sidebar-collapsed #user-sidebar .sidebar-group-heading {
                display: none !important;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-group-divider {
                display: block !important;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-brand-row {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-nav {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            html.sidebar-collapsed #user-sidebar .user-sidebar-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-footer {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-profile-row {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            html.sidebar-collapsed #user-sidebar .sidebar-logout-btn {
                padding-left: 0;
                padding-right: 0;
            }
            html.sidebar-collapsed #sidebar-icon-collapse {
                display: none;
            }
            html.sidebar-collapsed #sidebar-icon-expand {
                display: block;
            }
        }
        .marker {
            background: linear-gradient(transparent 58%, #F9DD7A 58%, #F9DD7A 92%, transparent 92%);
            padding: 0 .1em;
        }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid #0F766E;
            outline-offset: 2px;
            border-radius: 4px;
        }
        footer a:focus-visible, footer button:focus-visible {
            outline-color: #F9DD7A;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body id="top" class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col selection:bg-[#F9DD7A]">

@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $currentStudent = $student ?? null;
    $displayName = $isAdmin ? auth()->user()->name : ($currentStudent->name ?? 'Siswa');
    $displaySub = $isAdmin ? 'Guru BK' : ($currentStudent->kelas ?? 'Kelas VII');
    $initial = strtoupper(substr($displayName ?: 'J', 0, 1));

    $adminNavItems = [
        ['route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'label' => 'Rekap Kelas'],
        ['route' => 'admin.konsultasi-masuk', 'active' => ['admin.konsultasi-masuk', 'admin.konsultasi.form.*'], 'label' => 'Konsultasi Masuk'],
        ['route' => 'admin.rpl-admin', 'active' => ['admin.rpl-admin'], 'label' => 'Dokumen RPL'],
        ['route' => 'admin.integrasi', 'active' => ['admin.integrasi'], 'label' => 'Upload Materi'],
        ['route' => 'admin.admins.index', 'active' => ['admin.admins.*'], 'label' => 'Akun Guru BK'],
    ];

    $studentNavGroups = [
        [
            'heading' => 'Jurnal Utama',
            'items' => [
                ['route' => 'siswa.beranda', 'active' => ['siswa.beranda'], 'label' => 'Beranda', 'icon' => 'home'],
                ['route' => 'siswa.perencana', 'active' => ['siswa.perencana'], 'label' => 'Perencana', 'icon' => 'calendar'],
                ['route' => 'siswa.ceklis', 'active' => ['siswa.ceklis'], 'label' => 'Ceklis', 'icon' => 'check'],
                ['route' => 'siswa.refleksi', 'active' => ['siswa.refleksi'], 'label' => 'Refleksi', 'icon' => 'book'],
            ],
        ],
        [
            'heading' => 'Bahan & Pendampingan',
            'items' => [
                ['route' => 'siswa.materi', 'active' => ['siswa.materi'], 'label' => 'Materi', 'icon' => 'bookmark'],
                ['route' => 'siswa.rpl', 'active' => ['siswa.rpl'], 'label' => 'RPL', 'icon' => 'file'],
                ['route' => 'siswa.konsultasi', 'active' => ['siswa.konsultasi'], 'label' => 'Konsultasi', 'icon' => 'chat'],
            ],
        ],
    ];
@endphp

{{-- Notifikasi Toast (penuh & rapi di layar HP, melayang di kanan atas pada layar besar) --}}
<div id="toast-container" class="fixed top-4 inset-x-4 sm:left-auto sm:right-5 sm:top-5 z-50 flex flex-col gap-2 sm:max-w-sm sm:w-full pointer-events-none"></div>

@if ($isAdmin)
    {{-- Kepala halaman sticky penuh sepanjang scroll (selaras dengan landing page) --}}
    <header class="sticky top-0 z-40 w-full bg-paper/95 backdrop-blur-sm border-b border-rule no-print">
        <div class="w-full max-w-none px-6 sm:px-10 lg:px-16 xl:px-24 h-[71px] md:h-[79px] xl:h-[87px] flex items-center justify-between gap-6">
            {{-- Kiri: Logo & Nama Aplikasi --}}
            <a href="{{ route('landing') }}" class="flex items-center gap-3.5 group shrink-0">
                <x-logo class="w-10 h-10 xl:w-11 xl:h-11 shrink-0 shadow-sm rounded-[10px] xl:rounded-[11px]" />
                <span class="font-serif text-[1.45rem] md:text-[1.6rem] xl:text-[1.75rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
            </a>

            {{-- Kanan: Navigasi, Aksi Keluar & Tombol Menu Mobile --}}
            <div class="flex items-center gap-5 xl:gap-7">
                <nav class="hidden lg:flex items-center gap-7 xl:gap-9 text-[15px] xl:text-base" aria-label="Navigasi utama">
                    @foreach ($adminNavItems as $item)
                        @php $active = request()->routeIs(...$item['active']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="py-1 transition-colors whitespace-nowrap {{ $active ? 'text-ink font-semibold underline underline-offset-[6px] decoration-accent decoration-2' : 'text-ink-soft hover:text-ink' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <span class="hidden lg:block h-5 w-px bg-rule" aria-hidden="true"></span>

                <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 xl:px-6 xl:py-3 rounded-md bg-ink text-sheet text-sm md:text-[15px] font-medium hover:bg-black transition-colors whitespace-nowrap">
                        Keluar
                    </button>
                </form>

                {{-- Tombol Menu Mobile --}}
                <button type="button" id="app-mobile-nav-btn" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="app-mobile-nav"
                        class="lg:hidden w-10 h-10 rounded-md border border-rule bg-sheet text-ink flex items-center justify-center hover:bg-paper transition-colors">
                    <svg id="app-nav-icon-open" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    <svg id="app-nav-icon-close" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Drawer Navigasi Mobile Admin --}}
        <div id="app-mobile-nav" class="hidden lg:hidden border-t border-rule bg-sheet shadow-lg">
            <div class="px-5 py-4 space-y-1.5 text-sm">
                <div class="pb-3 mb-2 border-b border-rule flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-full bg-ink text-sheet font-serif text-xs font-semibold flex items-center justify-center shrink-0">
                            {{ $initial }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-ink truncate leading-tight">{{ $displayName }}</p>
                            <p class="text-[11px] text-ink-faint">Ruang Guru BK</p>
                        </div>
                    </div>
                </div>
                @foreach ($adminNavItems as $item)
                    @php $active = request()->routeIs(...$item['active']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="app-mobile-nav-link flex items-center justify-between px-3.5 py-3 rounded-md {{ $active ? 'bg-ink text-sheet font-semibold' : 'text-ink-soft hover:bg-paper hover:text-ink font-medium' }}">
                        <span>{{ $item['label'] }}</span>
                        @if ($active)
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F9DD7A]" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach
                <div class="pt-3 mt-2 border-t border-rule flex flex-col gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full py-3 px-4 rounded-md bg-ink text-sheet text-center font-medium hover:bg-black transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Konten Utama Admin --}}
    <main class="flex-1 w-full max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-14 py-7 sm:py-12">
        @yield('content')
    </main>
@else
    {{-- Layout Sidebar untuk Halaman Siswa --}}
    <div class="min-h-screen flex flex-col lg:flex-row">
        {{-- Backdrop Drawer Mobile --}}
        <div id="user-sidebar-backdrop" class="fixed inset-0 z-40 bg-ink/40 backdrop-blur-[1px] hidden lg:hidden no-print" aria-hidden="true"></div>

        {{-- Sidebar Kiri --}}
        <aside id="user-sidebar"
               aria-label="Navigasi Jurnal Siswa"
               class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full transition-[transform,width] duration-200 ease-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:w-64 xl:w-72 shrink-0 bg-sheet border-r border-rule flex flex-col justify-between no-print">

            {{-- Atas: Brand JUBEMA --}}
            <div class="sidebar-brand-row px-6 h-[72px] lg:h-[79px] border-b border-rule flex items-center justify-between gap-3 shrink-0">
                <a href="{{ route('landing') }}" title="JUBEMA · Halaman Depan" class="flex items-center gap-3 group min-w-0">
                    <x-logo class="w-9 h-9 shrink-0 shadow-sm rounded-[10px]" />
                    <div class="sidebar-text min-w-0">
                        <span class="block font-serif text-[1.4rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
                        <span class="block text-[11px] text-ink-faint mt-1 truncate">Jurnal Belajar Siswa</span>
                    </div>
                </a>

                <button type="button" id="user-sidebar-close" aria-label="Tutup menu navigasi"
                        class="lg:hidden w-9 h-9 rounded-md border border-rule bg-paper/60 text-ink flex items-center justify-center hover:bg-paper transition-colors shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>

            {{-- Tengah: Daftar Menu Sidebar --}}
            <nav class="sidebar-nav flex-1 overflow-y-auto px-4 py-6 space-y-7" aria-label="Menu utama siswa">
                @foreach ($studentNavGroups as $groupIndex => $group)
                    <div>
                        @if ($groupIndex > 0)
                            <div class="sidebar-group-divider hidden border-t border-rule/80 mb-5" aria-hidden="true"></div>
                        @endif
                        <p class="sidebar-group-heading px-3 mb-2.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-faint">
                            {{ $group['heading'] }}
                        </p>
                        <ul class="space-y-1">
                            @foreach ($group['items'] as $item)
                                @php $active = request()->routeIs(...$item['active']); @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                       title="{{ $item['label'] }}"
                                       class="user-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-[14.5px] transition-colors {{ $active ? 'bg-ink text-sheet font-medium shadow-sm' : 'text-ink-soft hover:bg-paper hover:text-ink font-medium' }}">
                                        @switch($item['icon'])
                                            @case('home')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 10.5L12 3l9 7.5"/><path d="M5 9.5V20h14V9.5"/><path d="M9.5 20v-6h5v6"/>
                                                </svg>
                                                @break
                                            @case('calendar')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                                                </svg>
                                                @break
                                            @case('check')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                                </svg>
                                                @break
                                            @case('book')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                </svg>
                                                @break
                                            @case('bookmark')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                                </svg>
                                                @break
                                            @case('file')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
                                                </svg>
                                                @break
                                            @case('chat')
                                                <svg class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-[#F9DD7A]' : 'text-ink-faint' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                </svg>
                                                @break
                                        @endswitch
                                        <span class="sidebar-text truncate">{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            {{-- Bawah: Profil Siswa & Tombol Keluar --}}
            <div class="sidebar-footer p-4 border-t border-rule bg-paper/45 space-y-3 shrink-0">
                <div class="sidebar-profile-row flex items-center gap-3 px-1" title="{{ $displayName }} · {{ $displaySub }}">
                    <span class="w-9 h-9 rounded-full bg-accent text-sheet font-serif text-sm font-semibold flex items-center justify-center shrink-0">
                        {{ $initial }}
                    </span>
                    <div class="sidebar-text min-w-0">
                        <p class="text-sm font-semibold text-ink truncate leading-tight">{{ $displayName }}</p>
                        <p class="text-xs text-ink-faint truncate mt-0.5">{{ $displaySub }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Keluar"
                            class="sidebar-logout-btn w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-md border border-rule bg-sheet hover:bg-ink hover:text-sheet hover:border-ink text-ink text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span class="sidebar-text">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Sisi Kanan: Topbar + Konten Utama Siswa + Bottom Nav Mobile --}}
        <div class="flex-1 min-w-0 flex flex-col">
            <header class="sticky top-0 z-30 w-full bg-paper/95 backdrop-blur-sm border-b border-rule px-4 sm:px-8 lg:px-10 h-[60px] lg:h-[79px] flex items-center justify-between gap-3 no-print">
                {{-- Mobile: Logo JUBEMA + Judul Halaman Aktif --}}
                <div class="flex lg:hidden items-center gap-2.5 min-w-0">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 shrink-0">
                        <x-logo class="w-8 h-8 shrink-0 shadow-sm rounded-[8px]" />
                        <span class="font-serif text-[1.25rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
                    </a>
                    <span class="text-rule" aria-hidden="true">/</span>
                    <span class="text-xs font-semibold text-ink-soft truncate">@yield('title', 'Beranda')</span>
                </div>

                {{-- Desktop: Tombol Minimize Sidebar + Konteks Halaman Aktif --}}
                <div class="hidden lg:flex items-center gap-3.5 text-sm min-w-0">
                    <button type="button" id="user-sidebar-collapse"
                            aria-label="Perkecil atau perluas sidebar"
                            title="Perkecil / perluas sidebar"
                            class="w-9 h-9 rounded-md border border-rule bg-sheet hover:border-ink text-ink-soft hover:text-ink flex items-center justify-center transition-colors shrink-0">
                        <svg id="sidebar-icon-collapse" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="16" rx="2"/><line x1="9" y1="4" x2="9" y2="20"/><path d="M15 9l-3 3 3 3"/>
                        </svg>
                        <svg id="sidebar-icon-expand" class="w-[18px] h-[18px] hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="16" rx="2"/><line x1="9" y1="4" x2="9" y2="20"/><path d="M13 9l3 3-3 3"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 min-w-0">
                        <a href="{{ route('siswa.beranda') }}" class="font-medium text-ink-faint hover:text-ink transition-colors">JUBEMA</a>
                        <span class="text-ink-faint/60" aria-hidden="true">/</span>
                        <span class="font-semibold text-ink truncate">@yield('title', 'Beranda')</span>
                    </div>
                </div>

                {{-- Kanan Topbar --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    {{-- Indikator Timer Fokus Aktif (tampil lintas halaman saat berjalan / dijeda) --}}
                    <div id="global-timer-pill"
                         class="hidden items-center gap-1.5 sm:gap-2 pl-2.5 pr-1.5 py-1 rounded-md bg-ink text-sheet text-xs font-medium shadow-sm">
                        <a href="{{ route('siswa.ceklis') }}" class="flex items-center gap-1.5 hover:opacity-90 transition-opacity" title="Buka halaman Ceklis & Timer Fokus">
                            <span id="global-timer-dot" class="w-2 h-2 rounded-full bg-[#F9DD7A] animate-pulse shrink-0" aria-hidden="true"></span>
                            <span id="global-timer-display" class="font-mono font-semibold tracking-tight tabular-nums">25:00</span>
                        </a>
                        <button type="button" id="global-timer-toggle" onclick="window.JubemaTimer?.toggle()"
                                class="px-2 py-0.5 rounded bg-sheet/15 hover:bg-sheet/25 text-[11px] font-semibold text-sheet transition-colors">
                            Jeda
                        </button>
                    </div>

                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-sheet border border-rule text-xs font-medium text-ink-soft">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent" aria-hidden="true"></span>
                        <span>{{ $displayName }} · {{ $displaySub }}</span>
                    </span>

                    @unless (request()->routeIs('siswa.perencana'))
                        <a href="{{ route('siswa.perencana') }}"
                           class="hidden lg:inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-accent hover:bg-accent-dark text-sheet text-xs font-semibold transition-colors">
                            <span>+ Rencana Baru</span>
                        </a>
                    @endunless

                    <button type="button" id="user-sidebar-open" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="user-sidebar"
                            class="lg:hidden w-9 h-9 rounded-md border border-rule bg-sheet text-ink flex items-center justify-center hover:bg-paper transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                </div>
            </header>

            <main class="flex-1 w-full max-w-[1180px] mx-auto px-4 sm:px-8 lg:px-10 pt-6 pb-24 sm:py-9 lg:pb-9">
                @yield('content')
            </main>

            {{-- Bottom Navigation Bar Khusus Mobile Siswa (< lg) --}}
            @php
                $mobileBottomTabs = [
                    ['route' => 'siswa.beranda', 'active' => ['siswa.beranda'], 'label' => 'Beranda', 'icon' => 'home'],
                    ['route' => 'siswa.perencana', 'active' => ['siswa.perencana'], 'label' => 'Perencana', 'icon' => 'calendar'],
                    ['route' => 'siswa.ceklis', 'active' => ['siswa.ceklis'], 'label' => 'Ceklis', 'icon' => 'check'],
                    ['route' => 'siswa.refleksi', 'active' => ['siswa.refleksi'], 'label' => 'Refleksi', 'icon' => 'book'],
                    ['route' => 'siswa.konsultasi', 'active' => ['siswa.konsultasi'], 'label' => 'Konsultasi', 'icon' => 'chat'],
                ];
            @endphp
            <nav aria-label="Navigasi bawah mobile"
                 class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-sheet/95 backdrop-blur-md border-t border-rule no-print">
                <div class="grid grid-cols-5 h-[62px] max-w-lg mx-auto px-1">
                    @foreach ($mobileBottomTabs as $tab)
                        @php $active = request()->routeIs(...$tab['active']); @endphp
                        <a href="{{ route($tab['route']) }}"
                           class="flex flex-col items-center justify-center gap-1 text-[11px] transition-colors relative {{ $active ? 'text-ink font-semibold' : 'text-ink-faint hover:text-ink' }}">
                            @if ($active)
                                <span class="absolute top-0 inset-x-4 h-0.5 bg-accent rounded-full" aria-hidden="true"></span>
                            @endif
                            @switch($tab['icon'])
                                @case('home')
                                    <svg class="w-[18px] h-[18px] {{ $active ? 'text-accent' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2.1' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 10.5L12 3l9 7.5"/><path d="M5 9.5V20h14V9.5"/><path d="M9.5 20v-6h5v6"/>
                                    </svg>
                                    @break
                                @case('calendar')
                                    <svg class="w-[18px] h-[18px] {{ $active ? 'text-accent' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2.1' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                                    </svg>
                                    @break
                                @case('check')
                                    <svg class="w-[18px] h-[18px] {{ $active ? 'text-accent' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2.1' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                    </svg>
                                    @break
                                @case('book')
                                    <svg class="w-[18px] h-[18px] {{ $active ? 'text-accent' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2.1' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                    @break
                                @case('chat')
                                    <svg class="w-[18px] h-[18px] {{ $active ? 'text-accent' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $active ? '2.1' : '1.75' }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    @break
                            @endswitch
                            <span class="truncate">{{ $tab['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>
        </div>
    </div>
@endif

<script>
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, ch => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[ch]));
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const colors = {
            success: 'bg-accent text-sheet',
            error: 'bg-red-700 text-sheet',
            info: 'bg-ink text-sheet'
        };
        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.info} px-4 py-3 rounded-md shadow-md text-xs sm:text-sm font-medium pointer-events-auto transition-all duration-300`;
        toast.innerHTML = `<span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Drawer mobile untuk navbar Admin
        const btn = document.getElementById('app-mobile-nav-btn');
        const menu = document.getElementById('app-mobile-nav');
        const iconOpen = document.getElementById('app-nav-icon-open');
        const iconClose = document.getElementById('app-nav-icon-close');

        function setMenuOpen(open) {
            if (!btn || !menu) return;
            menu.classList.toggle('hidden', !open);
            btn.setAttribute('aria-expanded', String(open));
            iconOpen?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
        }

        btn?.addEventListener('click', () => {
            setMenuOpen(menu.classList.contains('hidden'));
        });

        document.querySelectorAll('.app-mobile-nav-link').forEach((link) => {
            link.addEventListener('click', () => setMenuOpen(false));
        });

        // Minimize / expand sidebar desktop untuk halaman Siswa
        const sidebarCollapseBtn = document.getElementById('user-sidebar-collapse');

        function syncCollapseButtonState() {
            if (!sidebarCollapseBtn) return;
            const collapsed = document.documentElement.classList.contains('sidebar-collapsed');
            sidebarCollapseBtn.setAttribute('aria-pressed', String(collapsed));
            sidebarCollapseBtn.setAttribute('title', collapsed ? 'Perluas sidebar' : 'Perkecil sidebar');
        }

        syncCollapseButtonState();

        sidebarCollapseBtn?.addEventListener('click', () => {
            const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem('jubema_sidebar_collapsed', collapsed ? '1' : '0');
            } catch (e) {}
            syncCollapseButtonState();
        });

        // Drawer sidebar mobile untuk halaman Siswa
        const sidebar = document.getElementById('user-sidebar');
        const sidebarBackdrop = document.getElementById('user-sidebar-backdrop');
        const sidebarOpenBtn = document.getElementById('user-sidebar-open');
        const sidebarCloseBtn = document.getElementById('user-sidebar-close');

        function setSidebarOpen(open) {
            if (!sidebar) return;
            sidebar.classList.toggle('-translate-x-full', !open);
            sidebar.classList.toggle('translate-x-0', open);
            sidebarBackdrop?.classList.toggle('hidden', !open);
            sidebarOpenBtn?.setAttribute('aria-expanded', String(open));
        }

        sidebarOpenBtn?.addEventListener('click', () => setSidebarOpen(true));
        sidebarCloseBtn?.addEventListener('click', () => setSidebarOpen(false));
        sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));

        document.querySelectorAll('.user-sidebar-link').forEach((link) => {
            link.addEventListener('click', () => setSidebarOpen(false));
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (menu && !menu.classList.contains('hidden')) {
                    setMenuOpen(false);
                    btn?.focus();
                }
                if (sidebar && sidebar.classList.contains('translate-x-0')) {
                    setSidebarOpen(false);
                    sidebarOpenBtn?.focus();
                }
            }
        });

        // Bersihkan timer fokus saat siswa keluar
        document.querySelectorAll('form[action="{{ route('logout') }}"]').forEach((form) => {
            form.addEventListener('submit', () => {
                try {
                    localStorage.removeItem('jubema_focus_timer_v1');
                } catch (e) {}
            });
        });

        @if (! $isAdmin)
            // Timer Fokus 25 Menit persisten lintas halaman (berbasis timestamp)
            const TIMER_KEY = 'jubema_focus_timer_v1';
            const TIMER_DURATION = 25 * 60;
            const baseTitle = document.title;

            function readTimerState() {
                try {
                    const raw = localStorage.getItem(TIMER_KEY);
                    if (!raw) return { status: 'idle', remaining: TIMER_DURATION, endAt: null };
                    const parsed = JSON.parse(raw);
                    if (!parsed || typeof parsed !== 'object') {
                        return { status: 'idle', remaining: TIMER_DURATION, endAt: null };
                    }
                    return {
                        status: ['idle', 'running', 'paused'].includes(parsed.status) ? parsed.status : 'idle',
                        remaining: Number.isFinite(parsed.remaining) ? Math.max(0, Math.min(TIMER_DURATION, Math.round(parsed.remaining))) : TIMER_DURATION,
                        endAt: Number.isFinite(parsed.endAt) ? parsed.endAt : null,
                    };
                } catch (e) {
                    return { status: 'idle', remaining: TIMER_DURATION, endAt: null };
                }
            }

            function writeTimerState(state) {
                try {
                    localStorage.setItem(TIMER_KEY, JSON.stringify(state));
                } catch (e) {}
            }

            function formatClock(totalSeconds) {
                const safe = Math.max(0, Math.floor(totalSeconds));
                const m = String(Math.floor(safe / 60)).padStart(2, '0');
                const s = String(safe % 60).padStart(2, '0');
                return `${m}:${s}`;
            }

            function syncTimerUI() {
                let state = readTimerState();
                let remaining = state.remaining;

                if (state.status === 'running' && state.endAt) {
                    remaining = Math.ceil((state.endAt - Date.now()) / 1000);
                    if (remaining <= 0) {
                        state = { status: 'idle', remaining: TIMER_DURATION, endAt: null };
                        remaining = TIMER_DURATION;
                        writeTimerState(state);
                        showToast('Sesi fokus 25 menit selesai!', 'success');
                    }
                }

                const formatted = formatClock(remaining);

                // Sinkron tampilan besar di halaman Ceklis (jika sedang dibuka)
                const pageDisplay = document.getElementById('pomodoro-display');
                const pageBtn = document.getElementById('pomodoro-btn');
                if (pageDisplay) pageDisplay.textContent = formatted;
                if (pageBtn) {
                    pageBtn.textContent = state.status === 'running'
                        ? 'Jeda'
                        : (state.status === 'paused' ? 'Lanjut' : 'Mulai');
                }

                // Sinkron indikator mini di topbar (tampil di semua halaman siswa saat aktif/jeda)
                const pill = document.getElementById('global-timer-pill');
                const pillDisplay = document.getElementById('global-timer-display');
                const pillDot = document.getElementById('global-timer-dot');
                const pillBtn = document.getElementById('global-timer-toggle');

                if (pill) {
                    const showPill = state.status === 'running' || state.status === 'paused';
                    pill.classList.toggle('hidden', !showPill);
                    pill.classList.toggle('inline-flex', showPill);
                }
                if (pillDisplay) pillDisplay.textContent = formatted;
                if (pillDot) {
                    pillDot.classList.toggle('animate-pulse', state.status === 'running');
                    pillDot.classList.toggle('bg-[#F9DD7A]', state.status === 'running');
                    pillDot.classList.toggle('bg-sheet/50', state.status !== 'running');
                }
                if (pillBtn) {
                    pillBtn.textContent = state.status === 'running' ? 'Jeda' : 'Lanjut';
                }

                document.title = state.status === 'running' ? `${formatted} · ${baseTitle}` : baseTitle;
            }

            window.JubemaTimer = {
                toggle() {
                    const state = readTimerState();
                    if (state.status === 'running' && state.endAt) {
                        const rem = Math.max(1, Math.ceil((state.endAt - Date.now()) / 1000));
                        writeTimerState({ status: 'paused', remaining: rem, endAt: null });
                    } else {
                        const rem = state.remaining > 0 ? state.remaining : TIMER_DURATION;
                        writeTimerState({ status: 'running', remaining: rem, endAt: Date.now() + rem * 1000 });
                    }
                    syncTimerUI();
                },
                reset() {
                    writeTimerState({ status: 'idle', remaining: TIMER_DURATION, endAt: null });
                    syncTimerUI();
                },
                sync: syncTimerUI,
            };

            syncTimerUI();
            setInterval(syncTimerUI, 500);
            window.addEventListener('storage', (e) => {
                if (e.key === TIMER_KEY) syncTimerUI();
            });
        @endif

        @foreach (['success', 'error', 'info'] as $type)
            @if (session($type))
                showToast(@json(session($type)), '{{ $type }}');
            @endif
        @endforeach
    });
</script>
@stack('scripts')
</body>
</html>
