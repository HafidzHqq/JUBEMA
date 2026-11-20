@extends('layouts.app')

@section('title', 'Refleksi Mingguan')

@section('content')
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

<div class="mt-12 grid lg:grid-cols-12 gap-10 lg:gap-12 items-start">

    {{-- Kolom kiri: Formulir refleksi --}}
    <div class="lg:col-span-6">
        <form method="POST" action="{{ route('siswa.reflections.store') }}" class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
            @csrf
            <div class="pb-4 border-b border-ink">
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">
                    Tulis refleksi minggu ini
                </h2>
                <p class="text-xs text-ink-faint mt-1">Jawab dengan jujur untuk dirimu sendiri.</p>
            </div>

            @if ($errors->any())
                <div class="border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                    @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Periode Minggu</label>
                    <input type="text" name="week" required maxlength="60" value="{{ old('week') }}"
                           placeholder="Contoh: Minggu ke-1 November"
                           class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Perasaanmu Minggu Ini</label>
                    <select name="mood" class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <option value="Lega & Bangga" @selected(old('mood') === 'Lega & Bangga')>Lega &amp; bangga pada diri sendiri</option>
                        <option value="Cukup Terbantu" @selected(old('mood') === 'Cukup Terbantu')>Cukup terbantu dengan langkah kecil</option>
                        <option value="Masih Kewalahan" @selected(old('mood') === 'Masih Kewalahan')>Masih kewalahan, butuh strategi baru</option>
                        <option value="Sulit Mulai" @selected(old('mood') === 'Sulit Mulai')>Masih sering menunda di awal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">1. Apa yang berjalan baik minggu ini?</label>
                <textarea name="good" rows="2" required maxlength="500"
                          placeholder="Contoh: Berhasil mencicil PR IPA tepat waktu..."
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('good') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">2. Apa yang paling mengganggu fokusmu?</label>
                <textarea name="obstacle" rows="2" required maxlength="500"
                          placeholder="Contoh: Tergoda membuka notifikasi HP saat baru mulai..."
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('obstacle') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">3. Satu hal yang mau dicoba minggu depan</label>
                <textarea name="plan" rows="2" required maxlength="500"
                          placeholder="Contoh: Menaruh HP di mode senyap selama 25 menit belajar..."
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('plan') }}</textarea>
            </div>

            <div class="pt-3 border-t border-rule space-y-4">
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
    </div>

    {{-- Kolom kanan: Lembar riwayat refleksi --}}
    <div class="lg:col-span-6 bg-sheet border border-rule">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Catatan minggu sebelumnya</h2>
                <p class="text-xs text-ink-faint mt-0.5">Arsip perjalanan belajarmu dari minggu ke minggu</p>
            </div>
            <span class="text-xs font-medium text-ink-faint">{{ $reflections->count() }} catatan</span>
        </div>

        <div class="divide-y divide-rule">
            @forelse ($reflections as $ref)
                <article class="p-6 sm:p-8 space-y-3.5">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h3 class="font-serif text-2xl font-medium text-ink">{{ $ref->week }}</h3>
                        <span class="px-2.5 py-0.5 rounded bg-accent/10 text-accent text-xs font-semibold">{{ $ref->mood }}</span>
                    </div>
                    <dl class="space-y-2.5 text-sm pt-1">
                        <div class="grid grid-cols-12 gap-2">
                            <dt class="col-span-3 text-xs font-medium text-accent pt-0.5">Berhasil</dt>
                            <dd class="col-span-9 text-ink-soft leading-relaxed">{{ $ref->good }}</dd>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <dt class="col-span-3 text-xs font-medium text-ink-faint pt-0.5">Hambatan</dt>
                            <dd class="col-span-9 text-ink-soft leading-relaxed">{{ $ref->obstacle }}</dd>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <dt class="col-span-3 text-xs font-medium text-ink pt-0.5">Rencana</dt>
                            <dd class="col-span-9 text-ink leading-relaxed">{{ $ref->plan }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <div class="p-14 text-center">
                    <p class="font-serif text-2xl text-ink">Belum ada catatan refleksi</p>
                    <p class="text-sm text-ink-faint mt-1">Catatan refleksi mingguanmu akan tersimpan rapi di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
