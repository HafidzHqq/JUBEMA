<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JUBEMA · Jurnal Manajemen Waktu Belajar, Layanan BK SMP</title>
    <meta name="description" content="Jurnal digital dari Layanan Bimbingan dan Konseling untuk siswa SMP kelas VII–IX: pecah tugas jadi langkah kecil, kerjakan 25 menit sekali duduk, refleksi tiap minggu, dan ngobrol dengan Guru BK.">
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
                    },
                    colors: {
                        paper: '#F5F0E6',
                        sheet: '#FFFDF8',
                        ink: { DEFAULT: '#18283B', soft: '#4A5868', faint: '#7D8794' },
                        rule: '#DED5C4',
                        accent: { DEFAULT: '#0F766E', dark: '#0B5A54' },
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --nav-h: 72px;
        }
        @media (min-width: 768px) {
            :root { --nav-h: 80px; }
        }
        @media (min-width: 1280px) {
            :root { --nav-h: 88px; }
        }
        html, body {
            min-height: 100%;
            margin: 0;
        }
        .hero-fold {
            position: relative;
            overflow: hidden;
            min-height: calc(100vh - var(--nav-h));
            min-height: calc(100dvh - var(--nav-h));
            display: flex;
            flex-direction: column;
        }
        .hero-fold--with-banner {
            min-height: calc(100vh - var(--nav-h) - 40px);
            min-height: calc(100dvh - var(--nav-h) - 40px);
        }
        .hero-art-blend {
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            filter: brightness(1.09) contrast(1.06);
            mix-blend-mode: multiply;
            -webkit-mask-image: radial-gradient(ellipse 74% 74% at 50% 50%, #000 62%, transparent 100%);
            mask-image: radial-gradient(ellipse 74% 74% at 50% 50%, #000 62%, transparent 100%);
            pointer-events: none;
        }
        .marker {
            background: linear-gradient(transparent 58%, #F9DD7A 58%, #F9DD7A 92%, transparent 92%);
            padding: 0 .1em;
        }
        a:focus-visible, button:focus-visible { outline: 2px solid #0F766E; outline-offset: 3px; border-radius: 4px; }
        footer a:focus-visible, footer button:focus-visible { outline-color: #F9DD7A; }
        .marker-dark {
            background: linear-gradient(transparent 62%, rgba(249,221,122,.28) 62%, rgba(249,221,122,.28) 94%, transparent 94%);
        }
    </style>
</head>
<body id="top" class="bg-paper text-ink font-sans antialiased selection:bg-[#F9DD7A]">

{{-- Status sesi (hanya muncul kalau sudah masuk) --}}
@if ($adminUser)
    <div class="w-full bg-ink text-sheet text-sm">
        <div class="w-full max-w-none px-6 sm:px-10 lg:px-16 xl:px-24 py-2.5 flex flex-wrap items-center justify-between gap-x-6 gap-y-1">
            <p>Anda masuk sebagai Guru BK, <span class="opacity-70">{{ $adminUser->email }}</span></p>
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold underline underline-offset-4 decoration-1">Kembali ke Ruang Guru BK</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="opacity-70 hover:opacity-100">Keluar</button>
                </form>
            </div>
        </div>
    </div>
@elseif ($currentStudent)
    <div class="w-full bg-accent text-sheet text-sm">
        <div class="w-full max-w-none px-6 sm:px-10 lg:px-16 xl:px-24 py-2.5 flex flex-wrap items-center justify-between gap-x-6 gap-y-1">
            <p>Halo, {{ $currentStudent->name }} ({{ $currentStudent->kelas }}). Jurnalmu masih terbuka.</p>
            <a href="{{ route('siswa.beranda') }}" class="font-semibold underline underline-offset-4 decoration-1">Buka Jurnal Belajar</a>
        </div>
    </div>
@endif

{{-- Kepala halaman sticky penuh sepanjang scroll --}}
<header class="sticky top-0 z-40 w-full bg-paper/95 backdrop-blur-sm border-b border-rule">
    <div class="w-full max-w-none px-6 sm:px-10 lg:px-16 xl:px-24 h-[71px] md:h-[79px] xl:h-[87px] flex items-center justify-between gap-6">
        {{-- Kiri: Logo & Nama Aplikasi --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-3.5 group shrink-0">
            <x-logo class="w-10 h-10 xl:w-11 xl:h-11 shrink-0 shadow-sm rounded-[10px] xl:rounded-[11px]" />
            <span class="font-serif text-[1.45rem] md:text-[1.6rem] xl:text-[1.75rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
        </a>

        {{-- Kanan: Navigasi, Aksi Masuk & Tombol Menu Mobile --}}
        <div class="flex items-center gap-5 xl:gap-7">
            <nav class="hidden md:flex items-center gap-7 xl:gap-9 text-[15px] xl:text-base" aria-label="Navigasi utama">
                <a href="#cara-kerja" data-section="cara-kerja" class="nav-link text-ink-soft hover:text-ink transition-colors py-1">Cara kerja</a>
                <a href="#privasi" data-section="privasi" class="nav-link text-ink-soft hover:text-ink transition-colors py-1">Privasi</a>
            </nav>

            <span class="hidden md:block h-5 w-px bg-rule" aria-hidden="true"></span>

            @if ($adminUser)
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 xl:px-6 xl:py-3 rounded-md bg-ink text-sheet text-sm md:text-[15px] font-medium hover:bg-black transition-colors">Ruang Guru BK</a>
            @elseif ($currentStudent)
                <a href="{{ route('siswa.beranda') }}" class="px-5 py-2.5 xl:px-6 xl:py-3 rounded-md bg-ink text-sheet text-sm md:text-[15px] font-medium hover:bg-black transition-colors">Jurnalku</a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 xl:px-6 xl:py-3 rounded-md bg-ink text-sheet text-sm md:text-[15px] font-medium hover:bg-black transition-colors">Masuk</a>
            @endif

            <button type="button" id="mobile-nav-btn" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-nav"
                    class="md:hidden w-10 h-10 rounded-md border border-rule bg-sheet text-ink flex items-center justify-center hover:bg-paper transition-colors">
                <svg id="mobile-nav-icon-open" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <svg id="mobile-nav-icon-close" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Drawer Navigasi Mobile --}}
    <div id="mobile-nav" class="hidden md:hidden border-t border-rule bg-sheet">
        <div class="px-6 py-4 space-y-1 text-sm">
            <a href="#cara-kerja" class="mobile-nav-link block py-2.5 text-ink-soft hover:text-ink font-medium">Cara kerja</a>
            <a href="#privasi" class="mobile-nav-link block py-2.5 text-ink-soft hover:text-ink font-medium">Privasi</a>
            <div class="pt-3 mt-2 border-t border-rule flex flex-col gap-2">
                @if ($adminUser)
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link block w-full py-2.5 px-4 rounded-md bg-ink text-sheet text-center font-medium">Kembali ke Ruang Guru BK</a>
                @elseif ($currentStudent)
                    <a href="{{ route('siswa.beranda') }}" class="mobile-nav-link block w-full py-2.5 px-4 rounded-md bg-accent text-sheet text-center font-medium">Buka Jurnal Belajar</a>
                @else
                    <a href="{{ route('login') }}" class="mobile-nav-link block w-full py-2.5 px-4 rounded-md bg-accent text-sheet text-center font-medium">Mulai Jurnal Belajar</a>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- Layar pertama: tepat memenuhi sisa viewport di bawah header --}}
<div class="hero-fold {{ ($adminUser || $currentStudent) ? 'hero-fold--with-banner' : '' }} w-full">
    <div class="hero-art-blend hidden lg:block absolute right-[2%] xl:right-[5%] top-1/2 -translate-y-1/2 w-[min(46vw,640px)] aspect-square"
         style="background-image: url('{{ asset('_.jpeg') }}');"
         aria-hidden="true"></div>

    {{-- ===================== Pembuka ===================== --}}
    <section class="relative z-10 flex-1 flex items-center w-full">
      <div class="w-full max-w-[1440px] mx-auto px-5 sm:px-10 lg:px-14 py-7 sm:py-10 grid lg:grid-cols-12 gap-7 lg:gap-12 items-center">
        <div class="lg:col-span-6">
            <p class="text-xs sm:text-sm text-ink-faint mb-3.5 sm:mb-5">Untuk siswa SMP kelas VII, VIII, dan IX</p>

            <h1 class="font-serif text-[2.2rem] sm:text-[3.5rem] xl:text-[4.25rem] leading-[1.06] sm:leading-[1.04] tracking-tight font-medium">
                Tugas menumpuk <span class="marker italic">bukan berarti kamu malas.</span>
            </h1>

            <p class="mt-4 sm:mt-6 text-[15px] sm:text-lg text-ink-soft leading-relaxed max-w-[36rem]">
                Sering kali tugasnya saja yang terasa terlalu besar. JUBEMA membantumu memecahnya jadi
                langkah kecil, mengerjakannya 25 menit sekali duduk, lalu melihat lagi apa yang berhasil
                minggu ini. Kalau tetap macet, Guru BK bisa kamu ajak ngobrol.
            </p>

            <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 sm:gap-x-7 sm:gap-y-4">
                @if ($adminUser)
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3.5 rounded-md bg-ink text-sheet font-semibold text-center hover:bg-black">
                        Kembali ke Ruang Guru BK
                    </a>
                @elseif ($currentStudent)
                    <a href="{{ route('siswa.beranda') }}" class="px-6 py-3.5 rounded-md bg-accent text-sheet font-semibold text-center hover:bg-accent-dark">
                        Buka Jurnal Belajar
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-md bg-accent text-sheet font-semibold text-center hover:bg-accent-dark">
                        Mulai Jurnal Belajar
                    </a>
                @endif
            </div>

            <p class="mt-4 sm:mt-5 text-xs sm:text-sm text-ink-faint">Cukup masuk pakai akun Google, tanpa perlu buat kata sandi baru.</p>
        </div>

        {{-- Ilustrasi untuk layar kecil/tablet agar proporsional dan tidak terlalu tinggi --}}
        <div class="lg:hidden w-full max-w-[230px] sm:max-w-[320px] mx-auto">
            <div class="hero-art-blend w-full aspect-square"
                 style="background-image: url('{{ asset('_.jpeg') }}');"
                 aria-hidden="true"></div>
        </div>
      </div>
    </section>

    {{-- Garis penutup bawah layar pertama --}}
    <div class="relative z-10 w-full border-t border-rule/70">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-14 py-3.5 flex items-center justify-between gap-4 text-xs text-ink-faint">
            <span>Bimbingan Klasikal &amp; Pendampingan Mandiri · Fase D</span>
            <a href="#cara-kerja" class="text-ink-soft hover:text-ink font-medium">Lihat cara kerja ↓</a>
        </div>
    </div>
</div>

<main>

    {{-- ===================== Cara kerja ===================== --}}
    <section id="cara-kerja" class="scroll-mt-24 bg-sheet">
        <div class="max-w-[1440px] mx-auto px-5 sm:px-10 lg:px-14 py-12 sm:py-20">
            <div class="grid lg:grid-cols-12 gap-5 sm:gap-8 mb-10 sm:mb-14">
                <h2 class="lg:col-span-5 font-serif text-[1.75rem] sm:text-4xl font-medium leading-tight tracking-tight">
                    Satu minggu di JUBEMA
                </h2>
                <p class="lg:col-span-6 lg:col-start-7 text-[15px] sm:text-base text-ink-soft leading-relaxed">
                    Tidak ada target sempurna. Setiap minggu kamu merencanakan, mengerjakan, lalu melihat
                    ke belakang sebentar. Pelan-pelan kamu jadi tahu cara belajar yang cocok untukmu.
                </p>
            </div>

            <ol class="grid sm:grid-cols-2 lg:grid-cols-4 border-t border-ink">
                <li class="pt-6 pb-8 sm:pr-8 lg:border-r border-rule">
                    <span class="font-serif text-5xl text-accent leading-none">1</span>
                    <h3 class="mt-5 text-lg font-semibold">Rencanakan</h3>
                    <p class="mt-2 text-[15px] text-ink-soft leading-relaxed">
                        Tulis tugasnya, harinya, dan seberapa penting. Lalu pecah jadi tiga langkah yang
                        bisa langsung dimulai.
                    </p>
                </li>
                <li class="pt-6 pb-8 sm:pl-0 lg:pl-8 lg:pr-8 lg:border-r border-rule border-t sm:border-t-0">
                    <span class="font-serif text-5xl text-accent leading-none">2</span>
                    <h3 class="mt-5 text-lg font-semibold">Kerjakan</h3>
                    <p class="mt-2 text-[15px] text-ink-soft leading-relaxed">
                        Buka ceklis hari ini, nyalakan timer 25 menit, dan centang setiap langkah yang
                        selesai. Satu langkah sudah cukup untuk hari yang berat.
                    </p>
                </li>
                <li class="pt-6 pb-8 sm:pr-8 lg:pl-8 lg:border-r border-rule border-t lg:border-t-0">
                    <span class="font-serif text-5xl text-accent leading-none">3</span>
                    <h3 class="mt-5 text-lg font-semibold">Renungkan</h3>
                    <p class="mt-2 text-[15px] text-ink-soft leading-relaxed">
                        Di akhir minggu, jawab tiga pertanyaan: apa yang berhasil, apa yang mengganggu,
                        dan satu hal yang mau dicoba minggu depan.
                    </p>
                </li>
                <li class="pt-6 pb-8 lg:pl-8 border-t lg:border-t-0 border-rule">
                    <span class="font-serif text-5xl text-accent leading-none">4</span>
                    <h3 class="mt-5 text-lg font-semibold">Ngobrol dengan Guru BK</h3>
                    <p class="mt-2 text-[15px] text-ink-soft leading-relaxed">
                        Kalau butuh bantuan, kirim permintaan konsultasi: tatap muka saat istirahat,
                        lewat Google Meet, atau cukup pesan tertulis.
                    </p>
                </li>
            </ol>

            <p class="mt-6 text-sm text-ink-faint max-w-2xl">
                Tiga langkah pertama mengikuti siklus belajar mandiri dari Zimmerman (2002):
                perencanaan, pelaksanaan, dan refleksi diri.
            </p>
        </div>
    </section>

    {{-- ===================== Privasi ===================== --}}
    <section id="privasi" class="scroll-mt-24 border-t border-rule">
        <div class="max-w-[1440px] mx-auto px-5 sm:px-10 lg:px-14 py-12 sm:py-20 grid lg:grid-cols-12 gap-8 sm:gap-12">
            <div class="lg:col-span-4">
                <h2 class="font-serif text-[1.75rem] sm:text-4xl font-medium leading-tight tracking-tight">
                    Apa yang dicatat, dan apa yang tidak
                </h2>
                <p class="mt-3.5 sm:mt-5 text-[15px] sm:text-base text-ink-soft leading-relaxed">
                    Siswa baru mau jujur menulis kesulitannya kalau tahu catatannya aman.
                    Jadi kami sengaja menyimpan sesedikit mungkin.
                </p>
            </div>

            <div class="lg:col-span-7 lg:col-start-6 grid sm:grid-cols-2 gap-10">
                <div>
                    <h3 class="text-sm font-semibold pb-3 border-b border-ink">Disimpan</h3>
                    <ul class="divide-y divide-rule text-[15px]">
                        <li class="py-3.5">Nama panggilan dan kelas</li>
                        <li class="py-3.5">Rencana tugas dan ceklis harian</li>
                        <li class="py-3.5">Refleksi mingguan <span class="block text-sm text-ink-faint mt-0.5">Guru BK hanya bisa membaca kalau kamu mencentang izin.</span></li>
                        <li class="py-3.5">Permintaan konsultasi <span class="block text-sm text-ink-faint mt-0.5">Hanya kamu dan Guru BK yang bisa melihat.</span></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold pb-3 border-b border-ink">Tidak diminta</h3>
                    <ul class="divide-y divide-rule text-[15px] text-ink-soft">
                        <li class="py-3.5"><span class="line-through decoration-ink/50">NISN</span></li>
                        <li class="py-3.5"><span class="line-through decoration-ink/50">Nilai atau peringkat kelas</span></li>
                        <li class="py-3.5"><span class="line-through decoration-ink/50">Kata sandi baru</span> <span class="block text-sm text-ink-faint mt-0.5">Masuk cukup dengan akun Google.</span></li>
                        <li class="py-3.5"><span class="line-through decoration-ink/50">Email di daftar Guru BK</span> <span class="block text-sm text-ink-faint mt-0.5">Yang tampil hanya nama panggilan dan kelas.</span></li>
                    </ul>
                </div>
                <p class="sm:col-span-2 text-sm text-ink-soft border-l-2 border-accent pl-4">
                    Isi jurnal tidak masuk rapor dan tidak dipakai untuk menilai siswa.
                </p>
            </div>
        </div>
    </section>

    {{-- ===================== Penutup ===================== --}}
    <section class="bg-sheet border-t border-rule">
        <div class="max-w-[1440px] mx-auto px-5 sm:px-10 lg:px-14 py-12 sm:py-20 flex flex-col md:flex-row md:items-end justify-between gap-6 sm:gap-8">
            <div>
                <h2 class="font-serif text-[1.75rem] sm:text-4xl font-medium tracking-tight">Mulai dari satu tugas saja.</h2>
                <p class="mt-2 sm:mt-3 text-[15px] sm:text-base text-ink-soft">Yang paling dekat tenggatnya. Sisanya bisa menyusul.</p>
            </div>
            @if ($adminUser)
                <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-md bg-ink text-sheet font-semibold hover:bg-black">Kembali ke Ruang Guru BK</a>
            @elseif ($currentStudent)
                <a href="{{ route('siswa.beranda') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-md bg-accent text-sheet font-semibold hover:bg-accent-dark">Buka Jurnal Belajar</a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-md bg-accent text-sheet font-semibold hover:bg-accent-dark">Mulai Jurnal Belajar</a>
            @endif
        </div>
    </section>
</main>

<footer class="bg-ink text-sheet">
    <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-14 pt-16 pb-12 lg:pt-20 lg:pb-16">
        <div class="grid grid-cols-2 lg:grid-cols-12 gap-x-8 gap-y-12">
            {{-- Identitas --}}
            <div class="col-span-2 lg:col-span-4">
                <a href="#top" class="inline-flex items-center gap-3 group">
                    <x-logo class="w-10 h-10 shrink-0 rounded-[10px] ring-1 ring-sheet/15" />
                    <div>
                        <span class="block font-serif text-2xl font-semibold tracking-tight leading-none">JUBEMA</span>
                        <span class="block text-xs text-sheet/55 mt-1">Layanan BK SMP · Kelas VII–IX</span>
                    </div>
                </a>

                <p class="mt-6 text-[15px] text-sheet/70 leading-relaxed max-w-sm">
                    Jurnal digital manajemen waktu belajar. Pecah tugas besar jadi langkah kecil yang tenang dan terukur.
                </p>

                <p class="mt-6 max-w-sm font-serif italic text-base text-sheet/85 leading-relaxed">
                    <span class="marker-dark">“Satu langkah kecil hari ini</span> tetap lebih jauh daripada menunggu sempurna.”
                </p>
            </div>

            {{-- Navigasi --}}
            <div class="lg:col-span-2 lg:col-start-6">
                <h3 class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sheet/45 mb-5">Jelajahi</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="#cara-kerja" class="text-sheet/70 hover:text-sheet transition-colors">Cara kerja</a></li>
                    <li><a href="#privasi" class="text-sheet/70 hover:text-sheet transition-colors">Privasi data</a></li>
                    <li><a href="#top" class="text-sheet/70 hover:text-sheet transition-colors">Halaman utama</a></li>
                </ul>
            </div>

            {{-- Akses sesuai sesi --}}
            <div class="lg:col-span-2">
                <h3 class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sheet/45 mb-5">Akses Jurnal</h3>
                <ul class="space-y-3 text-sm">
                    @if ($adminUser)
                        <li><a href="{{ route('admin.dashboard') }}" class="text-sheet/70 hover:text-sheet transition-colors">Ruang Guru BK</a></li>
                        <li><a href="{{ route('admin.konsultasi-masuk') }}" class="text-sheet/70 hover:text-sheet transition-colors">Konsultasi masuk</a></li>
                        <li><a href="{{ route('admin.rpl-admin') }}" class="text-sheet/70 hover:text-sheet transition-colors">Dokumen RPL</a></li>
                        <li><a href="{{ route('admin.integrasi') }}" class="text-sheet/70 hover:text-sheet transition-colors">Upload materi</a></li>
                    @elseif ($currentStudent)
                        <li><a href="{{ route('siswa.beranda') }}" class="text-sheet/70 hover:text-sheet transition-colors">Beranda jurnal</a></li>
                        <li><a href="{{ route('siswa.perencana') }}" class="text-sheet/70 hover:text-sheet transition-colors">Perencana tugas</a></li>
                        <li><a href="{{ route('siswa.ceklis') }}" class="text-sheet/70 hover:text-sheet transition-colors">Ceklis harian</a></li>
                        <li><a href="{{ route('siswa.refleksi') }}" class="text-sheet/70 hover:text-sheet transition-colors">Refleksi mingguan</a></li>
                        <li><a href="{{ route('siswa.konsultasi') }}" class="text-sheet/70 hover:text-sheet transition-colors">Konsultasi BK</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-sheet/70 hover:text-sheet transition-colors">Mulai Jurnal Belajar</a></li>
                        @if ($googleEnabled)
                            <li><a href="{{ route('auth.google') }}" class="text-sheet/70 hover:text-sheet transition-colors">Masuk dengan Google</a></li>
                        @endif
                    @endif
                </ul>
            </div>

            {{-- Pendampingan BK --}}
            <div class="col-span-2 lg:col-span-3 lg:col-start-10">
                <div class="rounded-2xl bg-sheet/[0.04] ring-1 ring-sheet/10 p-6">
                    <h3 class="font-serif text-xl font-medium leading-snug">Lagi kewalahan?</h3>
                    <p class="mt-2 text-sm text-sheet/65 leading-relaxed">
                        Kamu selalu bisa minta waktu ngobrol dengan Guru BK. Ceritamu aman.
                    </p>

                    <a href="{{ $currentStudent ? route('siswa.konsultasi') : route('login') }}"
                       class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-md bg-[#F9DD7A] text-ink text-sm font-semibold hover:bg-[#FBE89E] transition-colors group">
                        <span>Formulir konsultasi BK</span>
                        <span class="transition-transform group-hover:translate-x-0.5" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolofon --}}
    <div class="border-t border-sheet/10">
        <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-14 py-6 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-sheet/50">
            <p>&copy; {{ date('Y') }} JUBEMA · Layanan Bimbingan dan Konseling SMP</p>
            <p class="md:text-right">Mengacu pada siklus belajar mandiri Zimmerman (2002): rencana, kerjakan, refleksi.</p>
        </div>
    </div>
</footer>

<script>
    (() => {
        const btn = document.getElementById('mobile-nav-btn');
        const menu = document.getElementById('mobile-nav');
        const iconOpen = document.getElementById('mobile-nav-icon-open');
        const iconClose = document.getElementById('mobile-nav-icon-close');

        function setMenuOpen(open) {
            if (!btn || !menu) return;
            menu.classList.toggle('hidden', !open);
            btn.setAttribute('aria-expanded', String(open));
            if (iconOpen && iconClose) {
                iconOpen.classList.toggle('hidden', open);
                iconClose.classList.toggle('hidden', !open);
            }
        }

        btn?.addEventListener('click', () => {
            setMenuOpen(menu.classList.contains('hidden'));
        });

        document.querySelectorAll('.mobile-nav-link').forEach((link) => {
            link.addEventListener('click', () => setMenuOpen(false));
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu && !menu.classList.contains('hidden')) {
                setMenuOpen(false);
                btn?.focus();
            }
        });

        const navLinks = Array.from(document.querySelectorAll('nav a.nav-link[data-section]'));
        const sections = navLinks
            .map((link) => document.getElementById(link.dataset.section))
            .filter(Boolean);

        if ('IntersectionObserver' in window && sections.length > 0) {
            const activeClasses = ['text-ink', 'font-semibold', 'underline', 'underline-offset-[6px]', 'decoration-accent', 'decoration-2'];
            const inactiveClasses = ['text-ink-soft'];

            const setActive = (activeId) => {
                navLinks.forEach((link) => {
                    const isActive = link.dataset.section === activeId;
                    activeClasses.forEach((cls) => link.classList.toggle(cls, isActive));
                    inactiveClasses.forEach((cls) => link.classList.toggle(cls, !isActive));
                });
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        setActive(entry.target.id);
                    }
                });
            }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });

            sections.forEach((section) => observer.observe(section));

            window.addEventListener('scroll', () => {
                if (window.scrollY < 160) {
                    setActive('');
                }
            }, { passive: true });
        }
    })();
</script>

</body>
</html>
