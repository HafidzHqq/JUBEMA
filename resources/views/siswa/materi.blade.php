@extends('layouts.app')

@section('title', 'Materi Belajar')

@section('content')
<section class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 pb-10 border-b border-ink">
    <div class="max-w-2xl">
        <p class="text-sm text-ink-faint mb-3">Repositori Bacaan · Layanan BK SMP</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Materi <span class="marker italic">manajemen waktu.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft leading-relaxed">
            Panduan singkat yang mudah dipraktikkan untuk memulai tugas tanpa menunggu mood dan
            mengatur waktu belajar harian.
        </p>
    </div>

    <a href="{{ $workspaceLinks['gdrive'] }}" target="_blank" rel="noopener noreferrer"
       class="self-start xl:self-auto px-5 py-3 rounded-md bg-ink hover:bg-black text-sheet text-sm font-semibold transition-colors shrink-0">
        Buka Folder Google Drive ↗
    </a>
</section>

<div class="mt-10 grid md:grid-cols-2 gap-8 items-stretch">
    <article class="bg-sheet border border-rule p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-ink/40 transition-colors">
        <div class="space-y-4">
            <div class="flex items-baseline justify-between pb-4 border-b border-ink">
                <span class="font-serif text-4xl text-accent leading-none">01</span>
                <span class="text-xs font-medium text-ink-faint">Bacaan 3 menit · Regulasi Diri</span>
            </div>
            <h2 class="font-serif text-2xl sm:text-3xl font-medium text-ink tracking-tight leading-snug">
                Tiga Siklus Belajar Mandiri (Zimmerman)
            </h2>
            <p class="text-sm sm:text-[15px] text-ink-soft leading-relaxed">
                Belajar efektif tidak menuntut kesempurnaan, melainkan berputar dalam tiga tahap:
                perencanaan awal, pelaksanaan terfokus, dan refleksi jujur di akhir minggu.
            </p>
        </div>
        <div class="pt-4 border-t border-rule flex items-center justify-between text-xs">
            <span class="text-ink-faint">Dokumen Google Drive</span>
            <a href="https://docs.google.com/document/d/1YU04gttpATKqHqN44Z-qEWDjFXxY7Xke/edit" target="_blank" rel="noopener noreferrer"
               class="font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Baca Modul 1 ↗
            </a>
        </div>
    </article>

    <article class="bg-sheet border border-rule p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-ink/40 transition-colors">
        <div class="space-y-4">
            <div class="flex items-baseline justify-between pb-4 border-b border-ink">
                <span class="font-serif text-4xl text-accent leading-none">02</span>
                <span class="text-xs font-medium text-ink-faint">Bacaan 4 menit · Memulai Tugas</span>
            </div>
            <h2 class="font-serif text-2xl sm:text-3xl font-medium text-ink tracking-tight leading-snug">
                Mengatasi Kebiasaan Menunda Tugas
            </h2>
            <p class="text-sm sm:text-[15px] text-ink-soft leading-relaxed">
                Gunakan aturan lima menit: cukup mulai bagian paling ringan selama lima menit pertama
                agar otak tidak lagi melihat tugas sebagai beban besar.
            </p>
        </div>
        <div class="pt-4 border-t border-rule flex items-center justify-between text-xs">
            <span class="text-ink-faint">Dokumen Google Drive</span>
            <a href="https://docs.google.com/document/d/1ACNAUQSvz0i_VgzbSy8QqzEmWLOIGt92/edit" target="_blank" rel="noopener noreferrer"
               class="font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Baca Modul 2 ↗
            </a>
        </div>
    </article>
</div>
@endsection
