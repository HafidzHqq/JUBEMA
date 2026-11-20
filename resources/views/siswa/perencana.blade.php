@extends('layouts.app')

@section('title', 'Perencana Mingguan')

@section('content')
<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <p class="text-sm text-ink-faint mb-3">Langkah 1 · Perencanaan Mingguan</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Perencana &amp; <span class="marker italic">pemecah tugas.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
            Tugas yang terasa berat biasanya hanya perlu dipecah menjadi tiga langkah kecil yang jelas
            kapan dimulai.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <a href="{{ route('siswa.export') }}"
           class="inline-block px-5 py-3 rounded-md border border-rule bg-sheet hover:border-ink text-ink text-sm font-semibold transition-colors">
            Ekspor Rencana (.CSV) ↓
        </a>
    </div>
</section>

<div class="mt-12 grid lg:grid-cols-12 gap-10 lg:gap-12 items-start">

    {{-- Kolom kiri: Formulir rencana baru --}}
    <div class="lg:col-span-5">
        <form method="POST" action="{{ route('siswa.tasks.store') }}" class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
            @csrf
            <div class="pb-4 border-b border-ink">
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">
                    Tulis tugas baru
                </h2>
                <p class="text-xs text-ink-faint mt-1">Mulai dari satu tugas yang paling dekat tenggatnya.</p>
            </div>

            @if ($errors->any())
                <div class="border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">Mata Pelajaran / Kegiatan</label>
                <input type="text" name="subject" required maxlength="60" value="{{ old('subject') }}"
                       placeholder="Contoh: Matematika / PR IPA"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Hari Target</label>
                    <select name="day" class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        @foreach (\App\Models\Task::DAYS as $day)
                            <option value="{{ $day }}" @selected(old('day') === $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Prioritas</label>
                    <select name="priority" class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <option value="Penting & Mendesak" @selected(old('priority') === 'Penting & Mendesak')>Penting &amp; Mendesak</option>
                        <option value="Penting & Terjadwal" @selected(old('priority') === 'Penting & Terjadwal' || empty(old('priority')))>Penting &amp; Terjadwal</option>
                        <option value="Tambahan / Pengayaan" @selected(old('priority') === 'Tambahan / Pengayaan')>Tambahan / Pengayaan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">Target Tugas</label>
                <input type="text" name="title" required maxlength="120" value="{{ old('title') }}"
                       placeholder="Contoh: Menyelesaikan 10 soal latihan pecahan"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1.5">Waktu Mulai &amp; Durasi</label>
                <input type="text" name="time" required maxlength="60" value="{{ old('time') }}"
                       placeholder="Contoh: Pukul 16.00 (25 menit fokus)"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            </div>

            <div class="pt-4 border-t border-rule space-y-2.5">
                <label class="block text-xs font-semibold text-ink">
                    Pecah jadi 3 langkah kecil
                </label>
                <input type="text" name="subtasks[0]" maxlength="80" placeholder="Langkah 1: Siapkan buku & baca contoh" value="{{ old('subtasks.0') }}"
                       class="w-full px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                <input type="text" name="subtasks[1]" maxlength="80" placeholder="Langkah 2: Kerjakan nomor 1–5 dulu" value="{{ old('subtasks.1') }}"
                       class="w-full px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                <input type="text" name="subtasks[2]" maxlength="80" placeholder="Langkah 3: Periksa ulang & rapikan" value="{{ old('subtasks.2') }}"
                       class="w-full px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-xs text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Simpan ke Rencana Mingguan
            </button>
        </form>
    </div>

    {{-- Kolom kanan: Lembar daftar rencana tugas mingguan --}}
    <div class="lg:col-span-7 bg-sheet border border-rule">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar rencana minggu ini</h2>
                <p class="text-xs text-ink-faint mt-0.5">Centang langkah kecilnya satu per satu saat dikerjakan</p>
            </div>
            <span class="text-xs font-medium text-ink-faint">{{ $tasks->count() }} tugas</span>
        </div>

        <div class="divide-y divide-rule">
            @forelse ($tasks as $task)
                <article class="p-6 sm:p-8 space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="px-2 py-0.5 rounded bg-ink text-sheet font-semibold">{{ $task->day }}</span>
                                <span class="font-medium text-ink">{{ $task->subject }}</span>
                                <span class="text-ink-faint">· {{ $task->time }}</span>
                                <span class="px-2 py-0.5 rounded bg-paper border border-rule text-ink-soft">{{ $task->priority }}</span>
                            </div>
                            <h3 class="font-serif text-2xl font-medium text-ink {{ $task->completed ? 'line-through text-ink-faint' : '' }}">
                                {{ $task->title }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-4 shrink-0 text-xs">
                            <form method="POST" action="{{ route('siswa.tasks.toggle', $task) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="px-3.5 py-1.5 rounded-md font-semibold transition-colors {{ $task->completed ? 'bg-accent/10 text-accent' : 'border border-rule bg-paper hover:border-ink text-ink' }}">
                                    {{ $task->completed ? 'Selesai ✓' : 'Tandai Selesai' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('siswa.tasks.destroy', $task) }}" onsubmit="return confirm('Hapus rencana tugas ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-ink-faint hover:text-red-700 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($task->subtasks->isNotEmpty())
                        <div class="bg-paper/55 border-l-2 border-accent pl-4 py-3 pr-4 space-y-2">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-ink-faint">Langkah Kecil</p>
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
            @empty
                <div class="p-14 text-center">
                    <p class="font-serif text-2xl text-ink">Belum ada rencana tugas minggu ini</p>
                    <p class="text-sm text-ink-faint mt-1">Isi formulir di sebelah kiri untuk memecah tugas pertamamu.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
