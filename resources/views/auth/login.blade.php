<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · JUBEMA, Jurnal Digital Manajemen Waktu Belajar</title>
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
        html, body {
            min-height: 100%;
            margin: 0;
        }
        .marker {
            background: linear-gradient(transparent 58%, #F9DD7A 58%, #F9DD7A 92%, transparent 92%);
            padding: 0 .1em;
        }
        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid #0F766E;
            outline-offset: 3px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-paper text-ink font-sans antialiased min-h-screen min-h-[100dvh] selection:bg-[#F9DD7A]">

{{-- Wadah notifikasi toast --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"></div>

{{-- Layout satu layar penuh dua sisi, bersih tanpa kartu melayang --}}
<div class="min-h-screen min-h-[100dvh] w-full grid grid-cols-1 lg:grid-cols-12">

    {{-- Sisi Kiri: Panel Tinta Editorial --}}
    <aside class="order-2 lg:order-1 lg:col-span-5 bg-ink text-sheet px-6 py-10 sm:px-12 sm:py-12 lg:px-14 xl:px-16 flex flex-col justify-between">
        {{-- Brand kiri atas (desktop) --}}
        <div class="hidden lg:flex items-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3.5 group">
                <x-logo class="w-10 h-10 shrink-0 rounded-[10px] ring-1 ring-sheet/15" />
                <span class="font-serif text-[1.6rem] font-semibold tracking-tight leading-none text-sheet">JUBEMA</span>
            </a>
        </div>

        {{-- Pengantar singkat --}}
        <div class="lg:my-auto lg:py-12 max-w-md">
            <p class="text-sm text-sheet/60">Jurnal Manajemen Waktu Belajar</p>

            <h1 class="mt-3 font-serif text-3xl sm:text-4xl leading-[1.1] tracking-tight font-medium text-sheet">
                Satu tempat tenang untuk mengatur tugas sekolahmu.
            </h1>

            <dl class="mt-8 divide-y divide-sheet/15 border-t border-b border-sheet/15 text-sm">
                <div class="py-3.5 flex items-baseline gap-4">
                    <dt class="font-serif text-base text-[#F9DD7A] shrink-0">01</dt>
                    <dd class="text-sheet/80">Pecah tugas besar jadi tiga langkah kecil</dd>
                </div>
                <div class="py-3.5 flex items-baseline gap-4">
                    <dt class="font-serif text-base text-[#F9DD7A] shrink-0">02</dt>
                    <dd class="text-sheet/80">Kerjakan dengan timer fokus 25 menit</dd>
                </div>
                <div class="py-3.5 flex items-baseline gap-4">
                    <dt class="font-serif text-base text-[#F9DD7A] shrink-0">03</dt>
                    <dd class="text-sheet/80">Refleksi tiap minggu &amp; ngobrol dengan Guru BK</dd>
                </div>
            </dl>
        </div>

        {{-- Kolofon bawah --}}
        <p class="mt-8 lg:mt-0 text-xs text-sheet/50">
            &copy; {{ date('Y') }} JUBEMA · Layanan Bimbingan dan Konseling SMP
        </p>
    </aside>

    {{-- Sisi Kanan: Area Masuk Bersih (Paper) --}}
    <main class="order-1 lg:order-2 lg:col-span-7 bg-paper flex flex-col justify-between px-6 py-6 sm:px-12 sm:py-8 lg:px-16 lg:py-10 xl:px-24">

        {{-- Baris atas --}}
        <div class="w-full flex items-center justify-between gap-4">
            <a href="{{ route('landing') }}" class="inline-flex lg:hidden items-center gap-3">
                <x-logo class="w-9 h-9 shrink-0 rounded-[9px]" />
                <span class="font-serif text-[1.4rem] font-semibold tracking-tight leading-none text-ink">JUBEMA</span>
            </a>

            <a href="{{ route('landing') }}" class="ml-auto text-sm text-ink-soft hover:text-ink transition-colors">
                ← Kembali ke beranda
            </a>
        </div>

        {{-- Konten utama langsung di atas kertas, tanpa kotak berlapis --}}
        <div class="w-full max-w-[400px] mx-auto my-auto py-12">

            @if ($adminPreview)
                {{-- GURU BK SUDAH MASUK: mode pratinjau --}}
                <div class="space-y-6">
                    <p class="text-sm text-accent font-medium">Anda sedang masuk sebagai Guru BK</p>

                    <div>
                        <h2 class="font-serif text-3xl sm:text-4xl font-medium tracking-tight text-ink leading-tight">
                            Halaman Utama JUBEMA
                        </h2>
                        <p class="text-[15px] text-ink-soft mt-3 leading-relaxed">
                            Ini adalah halaman masuk yang dilihat siswa. Karena Anda sudah masuk sebagai
                            Guru BK, gunakan tombol di bawah untuk kembali ke ruang kerja.
                        </p>
                    </div>

                    <div class="py-4 border-t border-b border-rule flex items-center gap-3.5">
                        @if (filled($adminUser->avatar_url))
                            <img src="{{ $adminUser->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover border border-rule">
                        @else
                            <div class="w-10 h-10 rounded-full bg-ink text-sheet flex items-center justify-center font-serif text-base font-semibold">
                                {{ strtoupper(substr($adminUser->name ?? 'G', 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink truncate">{{ $adminUser->name }}</p>
                            <p class="text-xs text-ink-faint truncate">{{ $adminUser->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('admin.dashboard') }}"
                           class="w-full py-3.5 px-6 rounded-md bg-ink hover:bg-black text-sheet font-semibold text-sm transition-colors flex items-center justify-center">
                            Kembali ke Ruang Guru BK
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full py-2.5 text-xs font-medium text-ink-soft hover:text-red-700 transition-colors">
                                Keluar dari akun ini
                            </button>
                        </form>
                    </div>
                </div>
            @else

                @if ($errors->any())
                    <div class="mb-6 border-l-2 border-red-600 pl-4 py-1 text-red-800 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @unless ($googleEnabled)
                    <div class="mb-6 border-l-2 border-ink pl-4 py-1 text-ink text-xs leading-relaxed">
                        Login Google belum aktif. Isi <code class="font-mono">GOOGLE_CLIENT_ID</code> dan
                        <code class="font-mono">GOOGLE_CLIENT_SECRET</code> di file <code class="font-mono">.env</code>.
                    </div>
                @endunless

                {{-- PANEL MASUK TUNGGAL: Bersih & Fokus --}}
                <div id="panel-login">
                    <p class="text-sm text-ink-faint">Untuk siswa SMP kelas VII, VIII, dan IX</p>

                    <h2 class="mt-3 font-serif text-4xl sm:text-[2.65rem] font-medium tracking-tight text-ink leading-[1.06]">
                        Masuk ke <span class="marker italic">jurnalmu.</span>
                    </h2>

                    <p class="mt-4 text-[15px] text-ink-soft leading-relaxed">
                        Gunakan akun Google kamu. Pada kunjungan pertama, kamu hanya perlu mengisi
                        nama panggilan dan memilih kelas.
                    </p>

                    <a href="{{ route('auth.google') }}"
                       class="mt-8 w-full py-3.5 px-6 rounded-md bg-ink hover:bg-black text-sheet font-semibold text-[15px] transition-colors flex items-center justify-center gap-3 {{ $googleEnabled ? '' : 'opacity-40 pointer-events-none' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.6l6.7-6.7C35.6 2.5 30.2 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.8 6.1C12.3 13.3 17.6 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.1 24.6c0-1.6-.1-2.8-.4-4.1H24v8.4h12.5c-.3 2.1-1.6 5.2-4.7 7.3l7.6 5.9c4.4-4.1 6.7-10.1 6.7-17.5z"/>
                            <path fill="#FBBC05" d="M10.4 28.7A14.6 14.6 0 0 1 9.6 24c0-1.6.3-3.2.8-4.7l-7.8-6.1A24 24 0 0 0 0 24c0 3.9.9 7.5 2.6 10.8l7.8-6.1z"/>
                            <path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.4-5.9l-7.6-5.9c-2 1.4-4.7 2.4-7.8 2.4-6.4 0-11.7-3.8-13.6-9.9l-7.8 6.1C6.5 42.6 14.6 48 24 48z"/>
                        </svg>
                        <span>Masuk dengan Google</span>
                    </a>

                    <p class="mt-5 text-xs text-ink-faint">
                        Tidak perlu membuat kata sandi baru.
                    </p>
                </div>

            @endif
        </div>

        {{-- Garis bawah selaras dengan hero landing page --}}
        <div class="hidden lg:flex items-center justify-between text-xs text-ink-faint border-t border-rule/80 pt-4">
            <span>Bimbingan Klasikal &amp; Pendampingan Mandiri</span>
            <span>Fase D</span>
        </div>
    </main>
</div>

{{-- ============================================================
     POP UP: Nama Panggilan + Kelas (muncul SETELAH login Google)
     ============================================================ --}}
@if ($pendingGoogle)
    <div id="profile-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm">
        <div class="w-full max-w-md bg-sheet border border-rule rounded-xl shadow-xl overflow-hidden max-h-[92vh] overflow-y-auto p-6 sm:p-8">

            <div class="pb-5 border-b border-rule">
                <p class="text-xs text-ink-faint">Masuk sebagai {{ $pendingGoogle['email'] ?? '-' }}</p>
                <h2 class="font-serif text-2xl sm:text-3xl font-medium text-ink mt-1 tracking-tight">
                    Satu langkah lagi, {{ $pendingGoogle['name'] ?? 'Siswa' }}.
                </h2>
            </div>

            <form method="POST" action="{{ route('login.siswa') }}" class="mt-6 space-y-5">
                @csrf

                <div id="modal-error-box" class="{{ $errors->any() ? '' : 'hidden' }} border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>

                <div>
                    <label for="modal-name" class="block text-xs font-semibold text-ink mb-2">Nama Panggilan</label>
                    <input type="text" name="name" id="modal-name" required maxlength="40" autocomplete="off"
                           value="{{ old('name', $pendingGoogle['name']) }}"
                           placeholder="Contoh: Rani"
                           class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div>
                    <span class="block text-xs font-semibold text-ink mb-2">Kelas</span>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['Kelas VII', 'Kelas VIII', 'Kelas IX'] as $kelas)
                            <label class="cursor-pointer">
                                <input type="radio" name="kelas" value="{{ $kelas }}" class="peer sr-only" @checked(old('kelas', 'Kelas VIII') === $kelas)>
                                <div class="text-center py-2.5 rounded-md border border-rule bg-paper/40 text-xs font-medium text-ink-soft peer-checked:border-ink peer-checked:bg-ink peer-checked:text-sheet transition">
                                    {{ $kelas }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-start gap-2.5 text-xs text-ink-soft pt-1 cursor-pointer leading-relaxed">
                    <input type="checkbox" name="consent" value="1" checked required class="mt-0.5 rounded border-rule text-accent focus:ring-accent">
                    <span>Saya memahami bahwa catatan jurnal saya dapat dilihat Guru BK untuk keperluan pendampingan belajar.</span>
                </label>

                <button type="submit" class="w-full py-3.5 px-6 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                    Mulai Jurnal Belajar
                </button>

                <a href="{{ route('auth.google.forget') }}"
                   class="block text-center text-xs text-ink-faint hover:text-ink transition-colors">
                    Ganti akun Google
                </a>
            </form>
        </div>
    </div>
@endif

<script>
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const colors = {
            success: 'bg-accent text-sheet',
            error: 'bg-red-700 text-sheet',
            info: 'bg-ink text-sheet',
        };
        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.info} px-4 py-3 rounded-md shadow-md text-xs sm:text-sm font-medium pointer-events-auto`;
        toast.innerHTML = `<span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if ($pendingGoogle)
            const modalName = document.getElementById('modal-name');
            if (modalName && !modalName.value.trim()) modalName.focus();
        @endif

        @foreach (['success', 'error', 'info'] as $type)
            @if (session($type))
                showToast(@json(session($type)), '{{ $type }}');
            @endif
        @endforeach
    });
</script>
</body>
</html>
