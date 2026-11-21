@extends('layouts.app')

@section('title', 'Refleksi Mingguan')

@section('content')
{{-- Pembuka Halaman: Selaras dengan gaya editorial Beranda & Perencana --}}
<section class="pb-10 border-b border-ink">
    <p class="text-sm text-ink-faint mb-3">Langkah 3 · Refleksi Diri</p>
    <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
        Refleksi <span class="marker italic">akhir minggu.</span>
    </h1>
    <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
        Lihat ke belakang sebentar: apa yang berhasil minggu ini, apa yang mengganggu fokusmu, dan
        satu langkah kecil yang mau dicoba minggu depan.
    </p>
</section>

{{-- Grid 2 Kolom Simetris: Header, garis pemisah, dan tinggi kartu sejajar --}}
<div class="mt-10 grid xl:grid-cols-12 gap-8 items-stretch">

    {{-- Kolom Kiri (6 kolom): Formulir Refleksi --}}
    <section class="xl:col-span-6 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">
                Tulis refleksi minggu ini
            </h2>
            <p class="text-xs text-ink-faint mt-1">Jawab dengan jujur untuk dirimu sendiri.</p>
        </div>

        <form method="POST" action="{{ route('siswa.reflections.store') }}" class="p-6 sm:p-8 flex-1 flex flex-col justify-between gap-6">
            <div class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                        @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ref-week" class="block text-xs font-semibold text-ink mb-1.5">Periode Minggu</label>
                        <input id="ref-week" type="text" name="week" required maxlength="60" value="{{ old('week') }}"
                               placeholder="Contoh: Minggu ke-1 November"
                               class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    </div>
                    <div>
                        <label for="ref-mood" class="block text-xs font-semibold text-ink mb-1.5">Perasaanmu Minggu Ini</label>
                        <select id="ref-mood" name="mood"
                                class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                            <option value="Lega & Bangga" @selected(old('mood') === 'Lega & Bangga')>Lega &amp; bangga pada diri sendiri</option>
                            <option value="Cukup Terbantu" @selected(old('mood') === 'Cukup Terbantu')>Cukup terbantu dengan langkah kecil</option>
                            <option value="Masih Kewalahan" @selected(old('mood') === 'Masih Kewalahan')>Masih kewalahan, butuh strategi baru</option>
                            <option value="Sulit Mulai" @selected(old('mood') === 'Sulit Mulai')>Masih sering menunda di awal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ref-good" class="block text-xs font-semibold text-ink mb-1.5">1. Apa yang berjalan baik minggu ini?</label>
                    <textarea id="ref-good" name="good" rows="3" required maxlength="500"
                              placeholder="Contoh: Berhasil mencicil PR IPA tepat waktu..."
                              class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('good') }}</textarea>
                </div>

                <div>
                    <label for="ref-obstacle" class="block text-xs font-semibold text-ink mb-1.5">2. Apa yang paling mengganggu fokusmu?</label>
                    <textarea id="ref-obstacle" name="obstacle" rows="3" required maxlength="500"
                              placeholder="Contoh: Tergoda membuka notifikasi HP saat baru mulai..."
                              class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('obstacle') }}</textarea>
                </div>

                <div>
                    <label for="ref-plan" class="block text-xs font-semibold text-ink mb-1.5">3. Satu hal yang mau dicoba minggu depan</label>
                    <textarea id="ref-plan" name="plan" rows="3" required maxlength="500"
                              placeholder="Contoh: Menaruh HP di mode senyap selama 25 menit belajar..."
                              class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('plan') }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-rule space-y-4">
                <label class="flex items-center gap-2.5 text-xs text-ink-soft cursor-pointer">
                    <input type="checkbox" name="share_bk" value="1" {{ old('share_bk', 1) ? 'checked' : '' }}
                           class="rounded border-rule text-accent focus:ring-accent">
                    <span>Izinkan Guru BK membaca refleksi ini untuk pendampingan</span>
                </label>

                <button type="submit"
                        class="w-full py-3.5 px-5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                    Simpan Refleksi Mingguan
                </button>
            </div>
        </form>
    </section>

    {{-- Kolom Kanan (6 kolom): Lembar Riwayat Refleksi --}}
    <section class="xl:col-span-6 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Catatan minggu sebelumnya</h2>
                <p class="text-xs text-ink-faint mt-1">Arsip perjalanan belajarmu dari minggu ke minggu.</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $reflections->count() }} catatan</span>
        </div>

        @if ($reflections->isEmpty())
            <div class="flex-1 p-10 sm:p-14 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-paper border border-rule flex items-center justify-center text-accent mb-4" aria-hidden="true">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <p class="font-serif text-2xl text-ink">Belum ada catatan refleksi</p>
                <p class="text-sm text-ink-faint mt-1.5 max-w-sm leading-relaxed">
                    Catatan refleksi mingguanmu akan tersimpan rapi di sini setelah kamu mengisi formulir di sebelah kiri.
                </p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($reflections as $ref)
                    <article class="p-6 sm:p-8 space-y-3.5">
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <h3 class="font-serif text-2xl font-medium text-ink">{{ $ref->week }}</h3>
                            <span class="px-2.5 py-0.5 rounded bg-accent/10 text-accent text-xs font-semibold">{{ $ref->mood }}</span>
                        </div>
                        <dl class="space-y-2.5 text-sm pt-1">
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-3 text-xs font-semibold text-accent pt-0.5">Berhasil</dt>
                                <dd class="sm:col-span-9 text-ink-soft leading-relaxed">{{ $ref->good }}</dd>
                            </div>
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-3 text-xs font-semibold text-ink-faint pt-0.5">Hambatan</dt>
                                <dd class="sm:col-span-9 text-ink-soft leading-relaxed">{{ $ref->obstacle }}</dd>
                            </div>
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-3 text-xs font-semibold text-ink pt-0.5">Rencana</dt>
                                <dd class="sm:col-span-9 text-ink leading-relaxed">{{ $ref->plan }}</dd>
                            </div>
                        </dl>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection
