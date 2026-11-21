@extends('layouts.app')

@section('title', 'Akun Guru BK · Ruang Guru BK')

@section('content')
<section class="pb-7 sm:pb-10 border-b border-ink">
    <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Ruang Guru BK · Manajemen Akses</p>
    <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
        Kelola akun <span class="marker italic">Guru BK.</span>
    </h1>
    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-2xl leading-relaxed">
        Tambahkan alamat email Google rekan Guru BK yang diizinkan mengelola JUBEMA. Saat masuk
        menggunakan akun Google tersebut, mereka akan otomatis diarahkan ke Ruang Guru BK.
    </p>
</section>

<div class="mt-7 sm:mt-10 grid lg:grid-cols-12 gap-6 sm:gap-8 items-stretch">

    {{-- Kolom Kiri: Form Tambah Akun Guru BK --}}
    <section class="lg:col-span-5 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">
                Tambah akun Guru BK
            </h2>
            <p class="text-xs text-ink-faint mt-1">
                Cukup masukkan nama dan alamat email Google yang akan dipakai login.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.admins.store') }}" class="p-6 sm:p-8 flex-1 flex flex-col justify-between gap-6">
            <div class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="admin-name" class="block text-xs font-semibold text-ink mb-1.5">Nama Lengkap / Panggilan</label>
                    <input type="text" id="admin-name" name="name" required maxlength="80" value="{{ old('name') }}"
                           placeholder="Contoh: Ibu Siti Rahmawati"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div>
                    <label for="admin-email" class="block text-xs font-semibold text-ink mb-1.5">Alamat Email Google</label>
                    <input type="email" id="admin-email" name="email" required maxlength="120" value="{{ old('email') }}"
                           placeholder="contoh: gurubk@gmail.com"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    <p class="text-xs text-ink-faint mt-1.5">
                        Pastikan email sesuai dengan akun Google yang digunakan saat masuk.
                    </p>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Tambahkan Akun Guru BK
            </button>
        </form>
    </section>

    {{-- Kolom Kanan: Daftar Akun Guru BK Terdaftar --}}
    <section class="lg:col-span-7 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Akun terdaftar</h2>
                <p class="text-xs text-ink-faint mt-1">Daftar akun yang memiliki akses ke Ruang Guru BK.</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $admins->count() }} akun</span>
        </div>

        @if ($admins->isEmpty())
            <div class="flex-1 p-12 flex flex-col items-center justify-center text-center">
                <p class="font-serif text-xl text-ink">Belum ada akun Guru BK di database</p>
                <p class="text-sm text-ink-faint mt-1">Tambahkan akun pertama melalui formulir di sebelah kiri.</p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($admins as $item)
                    @php
                        $isCurrent = $item->id === auth()->id();
                        $isEnvProtected = in_array(strtolower($item->email), $envAdminEmails ?? [], true);
                        $initial = strtoupper(substr($item->name ?: 'G', 0, 1));
                    @endphp
                    <article class="p-6 sm:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            @if (filled($item->avatar_url))
                                <img src="{{ $item->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover border border-rule shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-ink text-sheet font-serif text-base font-semibold flex items-center justify-center shrink-0">
                                    {{ $initial }}
                                </div>
                            @endif

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-ink truncate">{{ $item->name }}</p>
                                    @if ($isCurrent)
                                        <span class="px-2 py-0.5 rounded bg-[#F9DD7A]/60 text-ink text-[11px] font-semibold">Anda</span>
                                    @endif
                                    @if ($isEnvProtected)
                                        <span class="px-2 py-0.5 rounded bg-paper border border-rule text-ink-soft text-[11px] font-medium">Akun Utama</span>
                                    @endif
                                </div>
                                <p class="text-xs text-ink-faint truncate mt-0.5">{{ $item->email }}</p>
                                <p class="text-[11px] mt-1 {{ filled($item->google_id) ? 'text-accent font-medium' : 'text-ink-faint' }}">
                                    {{ filled($item->google_id) ? 'Terhubung dengan Google' : 'Menunggu login Google pertama' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @php $canRemove = $admins->count() > 1; @endphp
                            @if (! $canRemove)
                                <span class="text-xs text-ink-faint" title="Tambahkan minimal satu admin aktif lain sebelum keluar dari admin">
                                    Satu-satunya admin
                                </span>
                            @elseif ($isCurrent)
                                <form method="POST" action="{{ route('admin.admins.destroy', $item) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Keluar dari akses Admin Guru BK untuk '.$item->email.'? Pengelolaan akan diteruskan oleh admin aktif lainnya.') }});">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3.5 py-1.5 rounded-md border border-red-300 bg-red-50 hover:bg-red-100 text-xs font-semibold text-red-700 transition-colors">
                                        Keluar dari admin
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.admins.destroy', $item) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Cabut akses Ruang Guru BK untuk '.$item->email.'?') }});">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3.5 py-1.5 rounded-md border border-rule bg-paper hover:border-red-300 hover:bg-red-50 text-xs font-medium text-red-700 transition-colors">
                                        Hapus akses
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection
