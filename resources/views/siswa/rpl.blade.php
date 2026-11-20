@extends('layouts.app')

@section('title', 'Dokumen RPL BK')

@section('content')
<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end no-print">
    <div class="lg:col-span-8">
        <p class="text-sm text-ink-faint mb-3">Perangkat Layanan BK · Fase D</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Rencana Pelaksanaan <span class="marker italic">Layanan (RPL).</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
            Pedoman layanan bimbingan klasikal bidang belajar dengan topik manajemen waktu belajar
            mandiri untuk siswa SMP kelas VII–IX.
        </p>
    </div>
    <div class="lg:col-span-4 flex flex-wrap lg:justify-end items-center gap-3">
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

<div class="mt-12 bg-sheet border border-rule p-8 sm:p-12 lg:p-16 max-w-4xl mx-auto space-y-8 text-sm text-ink-soft leading-relaxed">
    <div class="border-b-2 border-ink pb-6">
        <p class="text-xs uppercase tracking-[0.14em] text-ink-faint">Bimbingan dan Konseling · Bimbingan Klasikal</p>
        <h2 class="mt-2 font-serif text-2xl sm:text-3xl font-medium text-ink tracking-tight">
            Rencana Pelaksanaan Layanan (RPL)
        </h2>
        <p class="mt-1 text-xs text-ink-faint">Satuan Pendidikan: SMP Negeri / Swasta · Fase D (Kelas VII–IX)</p>
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
            <dt class="font-semibold text-ink">C. Topik / Tema Layanan</dt>
            <dd class="sm:col-span-2">Manajemen Waktu Belajar Mandiri yang Efektif dan Sehat</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">D. Sasaran Layanan</dt>
            <dd class="sm:col-span-2">Peserta Didik Kelas VII–IX (Fase D)</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">E. Alokasi Waktu</dt>
            <dd class="sm:col-span-2">2 × 40 Menit</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">F. Media / Alat</dt>
            <dd class="sm:col-span-2">JUBEMA (Jurnal Digital Manajemen Waktu Belajar), Proyektor, Google Form, Spreadsheet</dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">G. Tujuan Layanan</dt>
            <dd class="sm:col-span-2 space-y-1">
                <p>1. Peserta didik dapat menjelaskan pentingnya manajemen waktu belajar.</p>
                <p>2. Peserta didik dapat menyusun jadwal belajar mingguan.</p>
                <p>3. Peserta didik dapat memecah tugas besar menjadi langkah kecil.</p>
                <p>4. Peserta didik mampu melakukan refleksi diri secara jujur.</p>
            </dd>
        </div>
        <div class="py-3.5 grid sm:grid-cols-3 gap-2">
            <dt class="font-semibold text-ink">H. SKKPD</dt>
            <dd class="sm:col-span-2">Kematangan Intelektual — Kesiapan belajar dan pengambilan keputusan karier belajar mandiri.</dd>
        </div>
    </dl>

    <div>
        <h3 class="font-serif text-lg font-medium text-ink mb-3">I. Langkah-Langkah Kegiatan</h3>
        <table class="w-full text-sm border-t border-b border-ink">
            <thead class="bg-paper/60 border-b border-rule text-left text-xs text-ink-faint">
                <tr>
                    <th class="py-3 px-4 font-semibold w-32">Tahap</th>
                    <th class="py-3 px-4 font-semibold w-24">Alokasi</th>
                    <th class="py-3 px-4 font-semibold">Deskripsi Kegiatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Pendahuluan</td>
                    <td class="py-4 px-4 text-ink-faint">10 Menit</td>
                    <td class="py-4 px-4 space-y-1">
                        <p>1. Guru BK membuka dengan salam dan menanyakan kesiapan peserta didik.</p>
                        <p>2. Guru BK menjelaskan tujuan layanan dan membangun hubungan hangat tanpa penilaian.</p>
                        <p>3. Peserta didik mengisi refleksi singkat awal via JUBEMA.</p>
                    </td>
                </tr>
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Inti</td>
                    <td class="py-4 px-4 text-ink-faint">50 Menit</td>
                    <td class="py-4 px-4 space-y-1">
                        <p>1. Guru BK menyajikan materi siklus regulasi diri (Zimmerman, 2002).</p>
                        <p>2. Peserta didik menyusun target mingguan dan memecah tugas melalui menu Perencana.</p>
                        <p>3. Praktik ceklis harian dan pengatur fokus 25 menit.</p>
                        <p>4. Diskusi kelompok kecil membahas hambatan belajar dan solusinya.</p>
                    </td>
                </tr>
                <tr class="align-top">
                    <td class="py-4 px-4 font-semibold text-ink">Penutup</td>
                    <td class="py-4 px-4 text-ink-faint">10 Menit</td>
                    <td class="py-4 px-4 space-y-1">
                        <p>1. Peserta didik mengisi refleksi mingguan pada JUBEMA.</p>
                        <p>2. Guru BK memberikan apresiasi atas proses, bukan hanya hasil.</p>
                        <p>3. Menyampaikan jadwal konsultasi individu bagi yang membutuhkan.</p>
                    </td>
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
