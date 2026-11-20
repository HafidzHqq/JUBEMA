@extends('layouts.app')

@section('title', 'Ceklis Harian & Fokus')

@section('content')
@php
    $days = ['Semua', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    $activeDay = request('day', 'Semua');
    $filtered = $activeDay === 'Semua' ? $tasks : $tasks->where('day', $activeDay);
@endphp

{{-- Pembuka halaman & Timer Fokus 25 Menit --}}
<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end">
    <div class="lg:col-span-7">
        <p class="text-sm text-ink-faint mb-3">Langkah 2 · Pelaksanaan Harian</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Ceklis harian &amp; <span class="marker italic">fokus 25 menit.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-xl leading-relaxed">
            Pilih satu tugas untuk hari ini, nyalakan timer 25 menit sekali duduk, dan centang setiap
            langkah kecil yang berhasil kamu selesaikan.
        </p>
    </div>

    <div class="lg:col-span-5 bg-ink text-sheet p-6 sm:p-7 flex items-center justify-between gap-6">
        <div>
            <p class="text-xs text-[#F9DD7A] uppercase tracking-[0.12em] font-medium">Timer Fokus 25 Menit</p>
            <p id="pomodoro-display" class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-none mt-2">25:00</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleTimer()" id="pomodoro-btn"
                    class="px-5 py-2.5 rounded-md bg-accent hover:bg-accent-dark text-sheet text-sm font-semibold transition-colors">
                Mulai
            </button>
            <button type="button" onclick="resetTimer()"
                    class="px-3 py-2.5 rounded-md border border-sheet/20 text-xs text-sheet/75 hover:text-sheet hover:border-sheet/50 transition-colors">
                Reset
            </button>
        </div>
    </div>
</section>

{{-- Lembar Jurnal Ceklis --}}
<section class="mt-10 bg-sheet border border-rule">
    {{-- Baris Filter Hari --}}
    <div class="px-6 sm:px-8 py-4 border-b border-ink flex flex-wrap items-center justify-between gap-4 bg-paper/45">
        <div class="flex gap-1.5 overflow-x-auto py-0.5">
            @foreach ($days as $day)
                <a href="?day={{ urlencode($day) }}"
                   class="whitespace-nowrap px-3.5 py-1.5 rounded-md text-xs font-semibold transition-colors {{ $activeDay === $day ? 'bg-ink text-sheet' : 'text-ink-soft hover:text-ink hover:bg-sheet' }}">
                    {{ $day === 'Semua' ? 'Semua Hari' : $day }}
                </a>
            @endforeach
        </div>
        <span class="text-xs font-medium text-ink-faint">{{ $filtered->count() }} tugas ditampilkan</span>
    </div>

    {{-- Daftar Tugas & Sub-langkah --}}
    <div class="divide-y divide-rule">
        @forelse ($filtered as $task)
            <article class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-start justify-between gap-5">
                <div class="flex items-start gap-4 flex-1">
                    <form method="POST" action="{{ route('siswa.tasks.toggle', $task) }}">
                        @csrf @method('PATCH')
                        <input type="checkbox" {{ $task->completed ? 'checked' : '' }} onchange="this.form.submit()"
                               class="mt-1.5 w-5 h-5 rounded border-rule text-accent focus:ring-accent cursor-pointer">
                    </form>
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-2 py-0.5 rounded bg-ink text-sheet font-semibold">{{ $task->day }}</span>
                            <span class="font-medium text-ink">{{ $task->subject }}</span>
                            <span class="text-ink-faint">· {{ $task->time }}</span>
                            <span class="text-ink-faint">· {{ $task->priority }}</span>
                        </div>

                        <h2 class="font-serif text-2xl font-medium text-ink {{ $task->completed ? 'line-through text-ink-faint' : '' }}">
                            {{ $task->title }}
                        </h2>

                        @if ($task->subtasks && $task->subtasks->isNotEmpty())
                            <div class="pt-2 pl-4 border-l-2 border-rule space-y-2">
                                @foreach ($task->subtasks as $sub)
                                    <form method="POST" action="{{ route('siswa.subtasks.toggle', [$task, $sub]) }}">
                                        @csrf @method('PATCH')
                                        <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                            <input type="checkbox" {{ $sub->done ? 'checked' : '' }} onchange="this.form.submit()"
                                                   class="rounded border-rule text-accent focus:ring-accent">
                                            <span class="{{ $sub->done ? 'line-through text-ink-faint' : 'text-ink-soft' }}">{{ $sub->text }}</span>
                                        </label>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="sm:text-right shrink-0">
                    <span class="inline-block px-3 py-1 rounded text-xs font-semibold {{ $task->completed ? 'bg-accent/10 text-accent' : 'bg-[#F9DD7A]/50 text-ink' }}">
                        {{ $task->completed ? 'Tuntas ✓' : 'Berproses' }}
                    </span>
                </div>
            </article>
        @empty
            <div class="p-14 text-center space-y-3">
                <p class="font-serif text-2xl text-ink">Tidak ada jadwal untuk hari {{ $activeDay }}</p>
                <p class="text-sm text-ink-faint">Belum ada tugas yang dijadwalkan pada hari ini.</p>
                <div class="pt-2">
                    <a href="{{ route('siswa.perencana') }}"
                       class="inline-block px-5 py-2.5 rounded-md bg-accent hover:bg-accent-dark text-sheet text-xs font-semibold transition-colors">
                        Tambah Rencana Tugas →
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</section>

@push('scripts')
<script>
    let timerSeconds = 25 * 60;
    let timerInterval = null;
    function toggleTimer() {
        const btn = document.getElementById('pomodoro-btn');
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; btn.textContent = 'Lanjut'; }
        else {
            btn.textContent = 'Jeda';
            timerInterval = setInterval(() => {
                if (timerSeconds > 0) { timerSeconds--; updateTimerDisplay(); }
                else { clearInterval(timerInterval); timerInterval = null; btn.textContent = 'Mulai'; showToast('Sesi fokus 25 menit selesai!', 'success'); }
            }, 1000);
        }
    }
    function resetTimer() { clearInterval(timerInterval); timerInterval = null; timerSeconds = 25 * 60; document.getElementById('pomodoro-btn').textContent = 'Mulai'; updateTimerDisplay(); }
    function updateTimerDisplay() {
        const m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
        const s = String(timerSeconds % 60).padStart(2, '0');
        document.getElementById('pomodoro-display').textContent = `${m}:${s}`;
    }
</script>
@endpush
@endsection
