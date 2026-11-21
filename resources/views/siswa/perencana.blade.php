@extends('layouts.app')

@section('title', 'Perencana Mingguan')

@section('content')
{{-- Pembuka Halaman: Selaras dengan gaya editorial Beranda --}}
<section class="flex flex-col xl:flex-row xl:items-end justify-between gap-5 sm:gap-6 pb-7 sm:pb-10 border-b border-ink">
    <div class="max-w-2xl">
        <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Langkah 1 · Perencanaan Mingguan</p>
        <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
            Perencana &amp; <span class="marker italic">pemecah tugas.</span>
        </h1>
        <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft leading-relaxed">
            Tugas yang terasa berat biasanya hanya perlu dipecah menjadi tiga langkah kecil yang jelas
            kapan dimulai.
        </p>
    </div>

    <div class="shrink-0">
        <a href="{{ route('siswa.export') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-sm font-semibold transition-colors">
            <span>Ekspor Rencana (.CSV)</span>
            <span aria-hidden="true">↓</span>
        </a>
    </div>
</section>

{{-- Grid 2 Kolom Simetris: Header, garis pemisah, dan tinggi kartu sejajar --}}
<div class="mt-7 sm:mt-10 grid xl:grid-cols-12 gap-6 sm:gap-8 items-stretch">

    {{-- Kolom Kiri (5 kolom): Formulir Rencana Baru --}}
    <section class="xl:col-span-5 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">
                Tulis tugas baru
            </h2>
            <p class="text-xs text-ink-faint mt-1">Mulai dari satu tugas yang paling dekat tenggatnya.</p>
        </div>

        <form method="POST" action="{{ route('siswa.tasks.store') }}" class="p-6 sm:p-8 flex-1 flex flex-col justify-between gap-6">
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
                    <label for="task-subject" class="block text-xs font-semibold text-ink mb-1.5">Mata Pelajaran / Kegiatan</label>
                    <input id="task-subject" type="text" name="subject" required maxlength="60" value="{{ old('subject') }}"
                           placeholder="Contoh: Matematika / PR IPA"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="task-day" class="block text-xs font-semibold text-ink mb-1.5">Hari Target</label>
                        <select id="task-day" name="day"
                                class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                            @foreach (\App\Models\Task::DAYS as $day)
                                <option value="{{ $day }}" @selected(old('day') === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="task-priority" class="block text-xs font-semibold text-ink mb-1.5">Prioritas</label>
                        <select id="task-priority" name="priority"
                                class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                            <option value="Penting & Mendesak" @selected(old('priority') === 'Penting & Mendesak')>Penting &amp; Mendesak</option>
                            <option value="Penting & Terjadwal" @selected(old('priority') === 'Penting & Terjadwal' || empty(old('priority')))>Penting &amp; Terjadwal</option>
                            <option value="Tambahan / Pengayaan" @selected(old('priority') === 'Tambahan / Pengayaan')>Tambahan / Pengayaan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="task-title" class="block text-xs font-semibold text-ink mb-1.5">Target Tugas</label>
                    <input id="task-title" type="text" name="title" required maxlength="120" value="{{ old('title') }}"
                           placeholder="Contoh: Menyelesaikan 10 soal latihan pecahan"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div>
                    <label for="task-time" class="block text-xs font-semibold text-ink mb-1.5">Waktu Mulai &amp; Durasi</label>
                    <input id="task-time" type="text" name="time" required maxlength="60" value="{{ old('time') }}"
                           placeholder="Contoh: Pukul 16.00 (25 menit fokus)"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div class="pt-4 border-t border-rule space-y-2.5">
                    <div class="flex items-baseline justify-between gap-2">
                        <span class="text-xs font-semibold text-ink">Pecah jadi 3 langkah kecil</span>
                        <span class="text-[11px] text-ink-faint">Opsional</span>
                    </div>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 w-8 flex items-center justify-center font-serif text-sm font-semibold text-accent">1</span>
                        <input type="text" name="subtasks[0]" maxlength="80" placeholder="Siapkan buku & baca contoh" value="{{ old('subtasks.0') }}"
                               class="w-full h-[38px] pl-8 pr-3.5 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    </div>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 w-8 flex items-center justify-center font-serif text-sm font-semibold text-accent">2</span>
                        <input type="text" name="subtasks[1]" maxlength="80" placeholder="Kerjakan nomor 1–5 dulu" value="{{ old('subtasks.1') }}"
                               class="w-full h-[38px] pl-8 pr-3.5 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    </div>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 w-8 flex items-center justify-center font-serif text-sm font-semibold text-accent">3</span>
                        <input type="text" name="subtasks[2]" maxlength="80" placeholder="Periksa ulang & rapikan" value="{{ old('subtasks.2') }}"
                               class="w-full h-[38px] pl-8 pr-3.5 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Simpan ke Rencana Mingguan
            </button>
        </form>
    </section>

    {{-- Kolom Kanan (7 kolom): Lembar Daftar Rencana Tugas Mingguan --}}
    <section class="xl:col-span-7 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar rencana minggu ini</h2>
                <p class="text-xs text-ink-faint mt-1">Centang langkah kecilnya satu per satu saat dikerjakan.</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">
                @if ($tasks->isNotEmpty())
                    {{ $doneTasks }}/{{ $tasks->count() }} selesai
                @else
                    0 tugas
                @endif
            </span>
        </div>

        @if ($tasks->isEmpty())
            <div class="flex-1 p-10 sm:p-14 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-paper border border-rule flex items-center justify-center text-accent mb-4" aria-hidden="true">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </div>
                <p class="font-serif text-2xl text-ink">Belum ada rencana tugas minggu ini</p>
                <p class="text-sm text-ink-faint mt-1.5 max-w-sm leading-relaxed">
                    Isi formulir di sebelah kiri untuk mencatat dan memecah tugas pertamamu menjadi tiga langkah kecil.
                </p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($tasks as $task)
                    <article class="p-5 sm:p-8 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3.5 sm:gap-4">
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-ink text-sheet font-semibold">{{ $task->day }}</span>
                                    <span class="font-medium text-ink">{{ $task->subject }}</span>
                                    <span class="text-ink-faint">· {{ $task->time }}</span>
                                    <span class="px-2 py-0.5 rounded bg-paper border border-rule text-ink-soft">{{ $task->priority }}</span>
                                </div>
                                <h3 class="font-serif text-xl sm:text-2xl font-medium text-ink {{ $task->completed ? 'line-through text-ink-faint' : '' }}">
                                    {{ $task->title }}
                                </h3>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-rule/70 shrink-0 text-xs">
                                <form method="POST" action="{{ route('siswa.tasks.toggle', $task) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="px-3.5 py-2 sm:py-1.5 rounded-md font-semibold transition-colors {{ $task->completed ? 'bg-accent/10 text-accent' : 'border border-rule bg-paper hover:border-ink text-ink' }}">
                                        {{ $task->completed ? 'Selesai ✓' : 'Tandai Selesai' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('siswa.tasks.destroy', $task) }}" onsubmit="return confirm('Hapus rencana tugas ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 sm:p-0 text-red-700 sm:text-ink-faint hover:text-red-700 font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($task->subtasks->isNotEmpty())
                            @php $doneSubtasks = $task->subtasks->where('done', true)->count(); @endphp
                            <div class="bg-paper/55 border-l-2 border-accent pl-4 py-3.5 pr-4 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-ink-faint">Langkah Kecil</p>
                                    <span class="text-[11px] font-medium text-ink-faint">{{ $doneSubtasks }}/{{ $task->subtasks->count() }}</span>
                                </div>
                                @foreach ($task->subtasks as $sub)
                                    <form method="POST" action="{{ route('siswa.subtasks.toggle', [$task, $sub]) }}">
                                        @csrf @method('PATCH')
                                        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                            <input type="checkbox" {{ $sub->done ? 'checked' : '' }} onchange="this.form.submit()"
                                                   class="rounded border-rule text-accent focus:ring-accent">
                                            <span class="{{ $sub->done ? 'line-through text-ink-faint' : 'text-ink' }}">{{ $sub->text }}</span>
                                        </label>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection
