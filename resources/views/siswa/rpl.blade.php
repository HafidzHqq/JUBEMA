@extends('layouts.app')

@section('title', 'Dokumen RPL BK')

@section('content')
<section class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 pb-10 border-b border-ink no-print">
    <div class="max-w-2xl">
        <p class="text-sm text-ink-faint mb-3">Perangkat Layanan BK · Fase D</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Rencana Pelaksanaan <span class="marker italic">Layanan (RPL).</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft leading-relaxed">
            Pedoman layanan bimbingan klasikal bidang belajar dengan topik manajemen waktu belajar
            mandiri melalui JUBEMA untuk siswa SMP kelas VII–IX.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-3 shrink-0">
        <a href="{{ $workspaceLinks['gdrive'] }}" target="_blank" rel="noopener noreferrer"
           class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium transition-colors">
            Google Drive ↗
        </a>
        <button type="button" onclick="window.print()"
                class="px-5 py-2.5 rounded-md bg-ink hover:bg-black text-sheet text-xs sm:text-sm font-semibold transition-colors">
            Cetak / Simpan PDF
        </button>
    </div>
</section>

<x-rpl-document />
@endsection
