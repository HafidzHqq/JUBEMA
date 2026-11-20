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
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; }
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

    $navItems = $isAdmin
        ? [
            ['route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'label' => 'Rekap Kelas'],
            ['route' => 'admin.konsultasi-masuk', 'active' => ['admin.konsultasi-masuk', 'admin.konsultasi.form.*'], 'label' => 'Konsultasi Masuk'],
            ['route' => 'admin.rpl-admin', 'active' => ['admin.rpl-admin'], 'label' => 'Dokumen RPL'],
            ['route' => 'admin.integrasi', 'active' => ['admin.integrasi'], 'label' => 'Tautan Sekolah'],
            ['route' => 'admin.admins.index', 'active' => ['admin.admins.*'], 'label' => 'Akun Guru BK'],
        ]
        : [
            ['route' => 'siswa.beranda', 'active' => ['siswa.beranda'], 'label' => 'Beranda'],
            ['route' => 'siswa.perencana', 'active' => ['siswa.perencana'], 'label' => 'Perencana'],
            ['route' => 'siswa.ceklis', 'active' => ['siswa.ceklis'], 'label' => 'Ceklis'],
            ['route' => 'siswa.refleksi', 'active' => ['siswa.refleksi'], 'label' => 'Refleksi'],
            ['route' => 'siswa.materi', 'active' => ['siswa.materi'], 'label' => 'Materi'],
            ['route' => 'siswa.rpl', 'active' => ['siswa.rpl'], 'label' => 'RPL'],
            ['route' => 'siswa.konsultasi', 'active' => ['siswa.konsultasi'], 'label' => 'Konsultasi'],
        ];
@endphp

{{-- Notifikasi Toast --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"></div>

{{-- Navbar Sticky Penuh (ringkas & tidak meluap di layar laptop) --}}
<header class="sticky top-0 z-40 w-full bg-paper/95 backdrop-blur-sm border-b border-rule no-print">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-14 h-[71px] md:h-[79px] flex items-center justify-between gap-4">
        {{-- Kiri: Logo & Nama --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('siswa.beranda') }}" class="flex items-center gap-3 group">
                <x-logo class="w-9 h-9 md:w-10 md:h-10 shrink-0 shadow-sm rounded-[10px]" />
                <span class="font-serif text-[1.4rem] md:text-[1.55rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
            </a>
            <span class="hidden 2xl:inline-flex items-center gap-1.5 ml-1 px-2.5 py-1 rounded-md text-xs font-medium {{ $isAdmin ? 'bg-[#F9DD7A]/60 text-ink border border-[#DFC056]' : 'bg-sheet text-ink-soft border border-rule' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isAdmin ? 'bg-ink' : 'bg-accent' }}" aria-hidden="true"></span>
                <span>{{ $isAdmin ? 'Ruang Guru BK' : 'Jurnal Siswa' }}</span>
            </span>
        </div>

        {{-- Tengah: Menu Navigasi Lengkap (Desktop, pas tanpa overflow) --}}
        <nav class="hidden lg:flex items-center gap-4 xl:gap-6 text-[13.5px] xl:text-[15px]" aria-label="Navigasi utama">
            @foreach ($navItems as $item)
                @php $active = request()->routeIs(...$item['active']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="py-1 transition-colors whitespace-nowrap {{ $active ? 'text-ink font-semibold underline underline-offset-[8px] decoration-accent decoration-2' : 'text-ink-soft hover:text-ink' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Kanan: Profil Pengguna & Tombol Keluar --}}
        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
            <div class="hidden md:flex items-center gap-2 pl-1 pr-3 py-1 rounded-full bg-sheet border border-rule">
                <span class="w-7 h-7 rounded-full bg-ink text-sheet font-serif text-xs font-semibold flex items-center justify-center shrink-0">
                    {{ $initial }}
                </span>
                <div class="text-xs leading-tight whitespace-nowrap">
                    <span class="font-semibold text-ink">{{ $displayName }}</span>
                    <span class="hidden xl:inline text-ink-faint"> · {{ $displaySub }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                @csrf
                <button type="submit"
                        class="px-3.5 py-2 rounded-md border border-rule bg-sheet hover:bg-ink hover:text-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium transition-colors whitespace-nowrap">
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

    {{-- Drawer Navigasi Mobile --}}
    <div id="app-mobile-nav" class="hidden lg:hidden border-t border-rule bg-sheet">
        <div class="px-6 py-4 space-y-1 text-sm">
            <div class="pb-3 mb-2 border-b border-rule flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-full bg-ink text-sheet font-serif text-sm font-semibold flex items-center justify-center">
                        {{ $initial }}
                    </span>
                    <div>
                        <p class="font-semibold text-ink leading-tight">{{ $displayName }}</p>
                        <p class="text-xs text-ink-faint">{{ $displaySub }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-md border border-rule text-xs font-medium text-red-700 hover:bg-red-50">
                        Keluar
                    </button>
                </form>
            </div>
            @foreach ($navItems as $item)
                @php $active = request()->routeIs(...$item['active']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="block py-2.5 {{ $active ? 'text-ink font-semibold underline underline-offset-4 decoration-accent decoration-2' : 'text-ink-soft hover:text-ink font-medium' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <div class="pt-3 mt-2 border-t border-rule">
                <a href="{{ route('landing') }}" class="block py-2 text-xs text-ink-faint hover:text-ink">
                    ← Halaman Depan JUBEMA
                </a>
            </div>
        </div>
    </div>
</header>

{{-- Konten Utama --}}
<main class="flex-1 w-full max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-14 py-10 sm:py-14">
    @yield('content')
</main>

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
        const btn = document.getElementById('app-mobile-nav-btn');
        const menu = document.getElementById('app-mobile-nav');
        const iconOpen = document.getElementById('app-nav-icon-open');
        const iconClose = document.getElementById('app-nav-icon-close');

        btn?.addEventListener('click', () => {
            const open = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !open);
            btn.setAttribute('aria-expanded', String(open));
            iconOpen?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
        });

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
