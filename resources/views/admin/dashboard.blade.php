@extends('layouts.app')

@section('title', 'Rekap Kelas · Ruang Guru BK')

@section('content')
@php
    $grouped = $tasks->groupBy('student_id');
    $completionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
@endphp

{{-- Pembuka Halaman --}}
<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <p class="text-sm text-ink-faint mb-3">Ruang Guru BK · Pemantauan Kelas VII–IX</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Rekap aktivitas <span class="marker italic">belajar siswa.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
            Pantau rencana tugas, ketuntasan langkah harian, dan catatan refleksi yang dibagikan siswa
            sebelum tugas mereka terlanjur menumpuk.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <a href="{{ route('admin.export.master') }}"
           class="inline-block px-5 py-3 rounded-md bg-ink hover:bg-black text-sheet text-sm font-semibold transition-colors">
            Ekspor Master Spreadsheet (.CSV) ↓
        </a>
    </div>
</section>

{{-- Strip 4 Metrik Utama di atas Lembar Jurnal --}}
<section class="bg-sheet border-b border-x border-rule">
    <dl class="grid grid-cols-2 lg:grid-cols-4">
        <div class="p-6 sm:p-8 border-r border-b lg:border-b-0 border-rule">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Siswa &amp; Rencana</dt>
            <dd class="mt-3 flex items-baseline gap-2.5">
                <span class="font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $totalTasks }}</span>
                <span class="text-xs text-ink-soft">tugas ({{ $grouped->count() }} siswa)</span>
            </dd>
        </div>
        <div class="p-6 sm:p-8 lg:border-r border-b lg:border-b-0 border-rule">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Tugas Selesai</dt>
            <dd class="mt-3 flex items-baseline gap-2.5">
                <span class="font-serif text-4xl sm:text-5xl text-accent leading-none">{{ $doneTasks }}</span>
                <span class="text-xs text-ink-soft">({{ $completionRate }}% tuntas)</span>
            </dd>
        </div>
        <div class="p-6 sm:p-8 border-r border-rule">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Refleksi Mingguan</dt>
            <dd class="mt-3 flex items-baseline gap-2.5">
                <span class="font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $reflections->count() }}</span>
                <span class="text-xs text-ink-soft">{{ $sharedReflections->count() }} dibagikan</span>
            </dd>
        </div>
        <div class="p-6 sm:p-8">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Konsultasi Masuk</dt>
            <dd class="mt-3 flex items-baseline gap-2.5">
                <span class="font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $consultations->count() }}</span>
                <span class="text-xs text-ink-soft">{{ $consultations->where('status', 'sudah_dijadwalkan')->count() }} terjadwal</span>
            </dd>
        </div>
    </dl>
</section>

{{-- Grid 12 Kolom: Tabel Tugas & Catatan Refleksi --}}
<section class="mt-12 grid lg:grid-cols-12 gap-8 items-stretch">

    {{-- Kolom Kiri (7 kolom): Tabel Rencana Tugas --}}
    <div class="lg:col-span-7 bg-sheet border border-rule flex flex-col">
        <div class="px-6 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar rencana tugas siswa</h2>
                <p class="text-xs text-ink-faint mt-0.5">Menampilkan nama panggilan, kelas, target tugas, dan status</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $tasks->count() }} entri</span>
        </div>

        @if ($tasks->isEmpty())
            <div class="flex-1 p-12 flex flex-col items-center justify-center text-center">
                <p class="font-serif text-xl text-ink">Belum ada rencana tugas siswa</p>
                <p class="text-sm text-ink-faint mt-1">Data akan otomatis muncul saat siswa mulai mengisi menu Perencana.</p>
            </div>
        @else
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="bg-paper/60 border-b border-rule text-left text-xs text-ink-faint">
                        <tr>
                            <th class="py-3.5 px-5 font-semibold">Siswa</th>
                            <th class="py-3.5 px-4 font-semibold">Hari</th>
                            <th class="py-3.5 px-4 font-semibold">Target Tugas &amp; Mapel</th>
                            <th class="py-3.5 px-5 font-semibold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule">
                        @foreach ($tasks as $task)
                            <tr class="align-top hover:bg-paper/35 transition-colors">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <p class="font-semibold text-ink">{{ $task->student?->name }}</p>
                                    <p class="text-xs text-ink-faint">{{ $task->student?->kelas }}</p>
                                </td>
                                <td class="py-4 px-4 text-ink-soft whitespace-nowrap">{{ $task->day }}</td>
                                <td class="py-4 px-4">
                                    <p class="font-medium text-ink">{{ $task->title }}</p>
                                    <p class="text-xs text-ink-faint mt-0.5">{{ $task->subject }} · {{ $task->time }} · {{ $task->priority }}</p>
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    @if ($task->completed)
                                        <span class="inline-block px-2.5 py-0.5 rounded bg-accent/10 text-accent text-xs font-semibold">Selesai</span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded bg-[#F9DD7A]/50 text-ink text-xs font-medium">Berproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Kolom Kanan (5 kolom): Refleksi yang Dibagikan --}}
    <div class="lg:col-span-5 bg-sheet border border-rule flex flex-col">
        <div class="px-6 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Refleksi siswa</h2>
                <p class="text-xs text-ink-faint mt-0.5">Hanya menampilkan catatan yang diizinkan siswa</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $sharedReflections->count() }} catatan</span>
        </div>

        @if ($sharedReflections->isEmpty())
            <div class="flex-1 p-12 flex flex-col items-center justify-center text-center">
                <p class="font-serif text-xl text-ink">Belum ada refleksi yang dibagikan</p>
                <p class="text-sm text-ink-faint mt-1">Catatan muncul jika siswa mencentang izin bagi Guru BK.</p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($sharedReflections as $ref)
                    <article class="p-6 space-y-3">
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <p class="font-semibold text-ink">
                                {{ $ref->student?->name }}
                                <span class="text-xs text-ink-faint font-normal">· {{ $ref->student?->kelas }}</span>
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded bg-paper border border-rule text-ink-soft">
                                {{ $ref->week }} · {{ $ref->mood }}
                            </span>
                        </div>
                        <dl class="space-y-2 text-sm pt-1">
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
                @endforeach
            </div>
        @endif
    </div>

</section>
@endsection
