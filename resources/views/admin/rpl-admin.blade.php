@extends('layouts.app')

@section('title', 'Dokumen RPL · Ruang Guru BK')

@section('content')
<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end no-print">
    <div class="lg:col-span-7">
        <p class="text-sm text-ink-faint mb-3">Ruang Guru BK · Perangkat Layanan Klasikal</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Dokumen <span class="marker italic">RPL siap cetak.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-xl leading-relaxed">
            Rencana Pelaksanaan Layanan Bimbingan Klasikal bidang belajar, topik manajemen waktu
            belajar mandiri, alokasi 2 × 40 menit untuk siswa SMP Fase D.
        </p>
    </div>
    <div class="lg:col-span-5 flex flex-wrap lg:justify-end items-center gap-3">
        <a href="{{ $workspaceLinks['gdrive'] }}" target="_blank" rel="noopener noreferrer"
           class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium transition-colors">
            Google Drive ↗
        </a>
        <a href="{{ $workspaceLinks['gsheet'] }}" target="_blank" rel="noopener noreferrer"
           class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-xs sm:text-sm font-medium transition-colors">
            Spreadsheet BK ↗
        </a>
        <button type="button" onclick="window.print()"
                class="px-5 py-2.5 rounded-md bg-ink hover:bg-black text-sheet text-xs sm:text-sm font-semibold transition-colors">
            Cetak / Simpan PDF
        </button>
    </div>
</section>

<div class="mt-12 bg-sheet border border-rule p-8 sm:p-12 lg:p-16 max-w-4xl mx-auto space-y-8 text-sm text-ink-soft leading-relaxed">
    <div class="border-b-2 border-ink pb-6">
        <p class="text-xs uppercase tracking-[0.14em] text-ink-faint">Bimbingan dan Konseling · Bimbingan Klasikal</p>
        <h2 class="mt-2 font-serif text-2xl sm:text-3xl font-medium text-ink tracking-tight">
            Rencana Pelaksanaan Layanan (RPL)
        </h2>
        <p class="mt-1 text-xs text-ink-faint">Fase D (Kelas VII–IX) · Media: JUBEMA — Jurnal Digital Manajemen Waktu Belajar</p>
    </div>

    <dl class="divide-y divide-rule border-b border-rule">
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">A. Komponen Layanan</dt>
            <dd class="sm:col-span-2">Layanan Dasar (Bimbingan Klasikal)</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">B. Bidang Layanan</dt>
            <dd class="sm:col-span-2">Bidang Belajar</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">C. Topik Layanan</dt>
            <dd class="sm:col-span-2">Manajemen Waktu Belajar Mandiri yang Efektif dan Sehat</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">D. Sasaran</dt>
            <dd class="sm:col-span-2">Peserta Didik Kelas VII–IX SMP (Fase D)</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">E. Alokasi Waktu</dt>
            <dd class="sm:col-span-2">2 × 40 Menit</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">F. Media / Alat</dt>
            <dd class="sm:col-span-2">JUBEMA (web), proyektor, Google Form, Google Spreadsheet, Google Drive</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">G. Tujuan</dt>
            <dd class="sm:col-span-2">Peserta didik mampu menyusun rencana belajar mingguan, memecah tugas besar menjadi langkah kecil, dan melakukan refleksi diri secara jujur.</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">H. SKKPD</dt>
            <dd class="sm:col-span-2">Kematangan Intelektual — kesiapan belajar mandiri (Fase D).</dd>
        </div>
    </dl>

    <div>
        <h3 class="font-serif text-lg font-medium text-ink mb-3">I. Langkah Kegiatan</h3>
        <table class="w-full text-sm border-t border-b border-ink">
            <thead class="bg-paper/60 border-b border-rule text-left text-xs text-ink-faint">
                <tr>
                    <th class="py-3 px-4 font-semibold w-32">Tahap</th>
                    <th class="py-3 px-4 font-semibold w-24">Waktu</th>
                    <th class="py-3 px-4 font-semibold">Kegiatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Pendahuluan</td>
                    <td class="py-4 px-4 text-ink-faint">10 Menit</td>
                    <td class="py-4 px-4">Salam, membangun hubungan hangat, menjelaskan tujuan layanan tanpa penilaian.</td>
                </tr>
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Inti</td>
                    <td class="py-4 px-4 text-ink-faint">50 Menit</td>
                    <td class="py-4 px-4">Materi siklus regulasi diri (Zimmerman, 2002), praktik JUBEMA (perencana, ceklis, timer 25 menit), refleksi bersama.</td>
                </tr>
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Penutup</td>
                    <td class="py-4 px-4 text-ink-faint">10 Menit</td>
                    <td class="py-4 px-4">Refleksi mingguan, apresiasi proses, penawaran konsultasi individu.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-8 text-ink text-xs">
        <div class="space-y-16">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <p class="font-semibold underline">(............................)</p>
        </div>
        <div class="space-y-16 sm:text-right">
            <p>Bandar Lampung, November 2026<br>Guru Bimbingan dan Konseling</p>
            <p class="font-semibold underline">{{ config('app.jubema.admin_name') }}</p>
        </div>
    </div>
</div>
@endsection
