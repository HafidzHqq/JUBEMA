@extends('layouts.app')

@section('title', 'Dokumen RPL · Ruang Guru BK')

@section('content')
<section class="grid lg:grid-cols-12 gap-5 sm:gap-8 pb-7 sm:pb-10 border-b border-ink items-end no-print">
    <div class="lg:col-span-7">
        <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Ruang Guru BK · Perangkat Layanan Klasikal</p>
        <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
            Dokumen <span class="marker italic">RPL siap cetak.</span>
        </h1>
        <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-xl leading-relaxed">
            Rencana Pelaksanaan Layanan Bimbingan Klasikal bidang belajar, topik manajemen waktu
            belajar mandiri melalui JUBEMA, alokasi 2 × 40 menit untuk siswa SMP Fase D.
        </p>
    </div>
    <div class="lg:col-span-5 grid grid-cols-2 sm:flex sm:flex-wrap lg:justify-end items-center gap-2.5 sm:gap-3">
        <a href="{{ $workspaceLinks['gdrive'] }}" target="_blank" rel="noopener noreferrer"
           class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium text-center transition-colors">
            Google Drive ↗
        </a>
        <a href="{{ $workspaceLinks['gsheet'] }}" target="_blank" rel="noopener noreferrer"
           class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium text-center transition-colors">
            Spreadsheet BK ↗
        </a>
        <button type="button" onclick="window.print()"
                class="col-span-2 sm:col-span-1 px-5 py-2.5 rounded-md bg-ink hover:bg-black text-sheet text-xs sm:text-sm font-semibold text-center transition-colors">
            Cetak / Simpan PDF
        </button>
    </div>
</section>

<div class="max-w-4xl mx-auto mt-10 print:max-w-none print:mt-0">
    <x-rpl-document />
</div>
@endsection
