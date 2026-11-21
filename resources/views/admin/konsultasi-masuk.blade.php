@extends('layouts.app')

@section('title', 'Konsultasi Masuk · Ruang Guru BK')

@section('content')
@php
    $pending = $consultations->where('status', '!=', 'sudah_dijadwalkan');
    $scheduled = $consultations->where('status', 'sudah_dijadwalkan');
@endphp

<section class="grid lg:grid-cols-12 gap-5 sm:gap-8 pb-7 sm:pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Ruang Guru BK · Layanan Konseling</p>
        <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
            Respon formulir <span class="marker italic">konsultasi siswa.</span>
        </h1>
        <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-2xl leading-relaxed">
            Seluruh formulir konsultasi yang diisi siswa melalui web JUBEMA langsung masuk ke sini.
            Tetapkan tanggal, jam, dan tempat pendampingan agar siswa langsung menerima jadwalnya.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <a href="{{ route('admin.konsultasi.form.edit') }}"
           class="w-full sm:w-auto inline-block text-center px-5 py-3 rounded-md bg-ink hover:bg-black text-sheet text-sm font-semibold transition-colors">
            Edit Pertanyaan Formulir →
        </a>
    </div>
</section>

<section class="bg-sheet border-b border-x border-rule">
    <dl class="grid grid-cols-3 divide-x divide-rule">
        <div class="p-3.5 sm:p-8">
            <dt class="text-[10px] sm:text-xs font-medium text-ink-faint uppercase tracking-[0.08em] sm:tracking-[0.12em]">Total Masuk</dt>
            <dd class="mt-1.5 sm:mt-2.5 font-serif text-2xl sm:text-5xl text-ink leading-none">{{ $consultations->count() }}</dd>
        </div>
        <div class="p-3.5 sm:p-8">
            <dt class="text-[10px] sm:text-xs font-medium text-ink-faint uppercase tracking-[0.08em] sm:tracking-[0.12em]">Menunggu</dt>
            <dd class="mt-1.5 sm:mt-2.5 font-serif text-2xl sm:text-5xl text-ink leading-none">{{ $pending->count() }}</dd>
        </div>
        <div class="p-3.5 sm:p-8">
            <dt class="text-[10px] sm:text-xs font-medium text-ink-faint uppercase tracking-[0.08em] sm:tracking-[0.12em]">Terjadwal</dt>
            <dd class="mt-1.5 sm:mt-2.5 font-serif text-2xl sm:text-5xl text-accent leading-none">{{ $scheduled->count() }}</dd>
        </div>
    </dl>
</section>

@if ($errors->any())
    <div class="mt-8 bg-sheet border border-red-300 border-l-4 border-l-red-600 px-5 py-4 text-xs text-red-800 space-y-1">
        <p class="font-semibold text-sm text-red-900">Jadwal belum tersimpan:</p>
        @foreach ($errors->all() as $err)
            <p>• {{ $err }}</p>
        @endforeach
    </div>
@endif

<section class="mt-12 bg-sheet border border-rule">
    <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between">
        <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar formulir konsultasi</h2>
        <span class="text-xs text-ink-faint">{{ $consultations->count() }} respons</span>
    </div>

    <div class="divide-y divide-rule">
        @forelse ($consultations as $item)
            @php
                $isScheduled = $item->status === 'sudah_dijadwalkan';
                $itemAnswers = $item->formattedAnswers();
                $schedulePayload = [
                    'id' => $item->id,
                    'action' => route('admin.konsultasi.mark', $item),
                    'student' => $item->displayName().' · '.$item->displayKelas(),
                    'topic' => $item->displayTopic(),
                    'preferred_time' => $item->mode ?: '-',
                    'method' => $item->consultation_method ?: '-',
                    'date' => $item->scheduled_at ? $item->scheduled_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'time' => $item->scheduled_at ? $item->scheduled_at->format('H:i') : '09:30',
                    'location' => $item->scheduled_location ?: (str_contains(strtolower((string) $item->consultation_method), 'meet') ? 'Google Meet' : 'Ruang BK'),
                    'note' => $item->scheduled_note ?? '',
                    'is_scheduled' => $isScheduled,
                ];
            @endphp
            <article class="p-6 sm:p-8 grid lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-3 space-y-1">
                    <p class="font-semibold text-base text-ink">{{ $item->displayName() }}</p>
                    <p class="text-xs text-ink-soft">{{ $item->displayKelas() }}</p>
                    @if (filled($item->email))
                        <p class="text-xs text-ink-faint break-all">{{ $item->email }}</p>
                    @endif
                    @if ($item->created_at)
                        <p class="text-xs text-ink-faint pt-2">{{ $item->created_at->translatedFormat('d F Y, H:i') }}</p>
                    @endif
                </div>

                <div class="lg:col-span-6 space-y-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="text-xs px-2.5 py-0.5 rounded {{ $isScheduled ? 'bg-accent/10 text-accent font-semibold' : 'bg-[#F9DD7A]/55 text-ink font-medium' }}">
                            {{ $item->statusLabel() }}
                        </span>
                        @if ($item->focus_level)
                            <span class="text-xs px-2.5 py-0.5 rounded bg-paper border border-rule text-ink font-medium">
                                Skala fokus: {{ $item->focus_level }} / 4
                            </span>
                        @endif
                    </div>

                    <h3 class="font-serif text-xl sm:text-2xl font-medium text-ink leading-snug">{{ $item->displayTopic() }}</h3>

                    @php
                        $headingText = $item->displayTopic();
                        $skipHeadingOnce = false;
                    @endphp
                    <dl class="space-y-2 text-sm">
                        @foreach ($itemAnswers as $ans)
                            @php
                                if (! $skipHeadingOnce && ($ans['id'] === 'topic' || $ans['value'] === $headingText)) {
                                    $skipHeadingOnce = true;
                                    continue;
                                }
                            @endphp
                            @if ($ans['type'] !== 'textarea')
                                <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                    <dt class="sm:col-span-4 text-xs text-ink-faint pt-0.5 leading-snug">{{ $ans['label'] }}</dt>
                                    <dd class="sm:col-span-8 text-ink-soft leading-snug">{{ $ans['value'] }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>

                    @foreach ($itemAnswers as $ans)
                        @if ($ans['type'] === 'textarea')
                            <div class="bg-paper/55 border-l-2 border-rule pl-4 py-2.5 space-y-1">
                                <p class="text-xs font-semibold text-ink-faint">{{ $ans['label'] }}</p>
                                <p class="text-sm text-ink-soft leading-relaxed whitespace-pre-line">{{ $ans['value'] }}</p>
                            </div>
                        @endif
                    @endforeach

                    @if ($isScheduled && $item->scheduled_at)
                        <div class="bg-accent/[0.06] border-l-2 border-accent pl-4 py-3 pr-4 space-y-1.5 text-xs">
                            <p class="font-semibold uppercase tracking-[0.1em] text-accent">Jadwal Konsultasi Ditetapkan</p>
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <span class="sm:col-span-3 text-ink-faint">Waktu</span>
                                <span class="sm:col-span-9 font-semibold text-ink">
                                    {{ $item->scheduled_at->translatedFormat('l, d F Y · H:i') }} WIB
                                </span>
                            </div>
                            @if (filled($item->scheduled_location))
                                <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                    <span class="sm:col-span-3 text-ink-faint">Tempat</span>
                                    <span class="sm:col-span-9 font-medium text-ink break-words">
                                        @if (filter_var($item->scheduled_location, FILTER_VALIDATE_URL))
                                            <a href="{{ $item->scheduled_location }}" target="_blank" rel="noopener noreferrer" class="text-accent underline">
                                                {{ $item->scheduled_location }} ↗
                                            </a>
                                        @else
                                            {{ $item->scheduled_location }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if (filled($item->scheduled_note))
                                <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                    <span class="sm:col-span-3 text-ink-faint">Catatan BK</span>
                                    <span class="sm:col-span-9 text-ink-soft">{{ $item->scheduled_note }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-3 flex flex-wrap lg:flex-col lg:items-end gap-2.5">
                    <button type="button"
                            data-schedule="{{ json_encode($schedulePayload) }}"
                            onclick="openScheduleModal(JSON.parse(this.dataset.schedule))"
                            class="px-4 py-2.5 rounded-md text-xs font-semibold transition-colors {{ $isScheduled ? 'border border-rule bg-sheet text-ink hover:border-ink' : 'bg-accent hover:bg-accent-dark text-sheet' }}">
                        {{ $isScheduled ? 'Ubah Jadwal' : 'Atur Jadwal Konsultasi' }}
                    </button>

                    @if ($isScheduled)
                        <form method="POST" action="{{ route('admin.konsultasi.mark', $item) }}"
                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Batalkan jadwal konsultasi untuk '.$item->displayName().'?') }});">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit"
                                    class="px-3.5 py-2 rounded-md border border-rule bg-paper hover:border-ink text-xs font-medium text-ink-soft hover:text-ink transition-colors">
                                Batalkan Jadwal
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.konsultasi.destroy', $item) }}"
                          onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus respons konsultasi dari '.$item->displayName().'?') }});">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3.5 py-2 rounded-md border border-rule bg-paper hover:border-red-300 hover:bg-red-50 text-xs font-medium text-red-700 transition-colors">
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="p-14 text-center">
                <p class="font-serif text-2xl text-ink">Belum ada formulir konsultasi</p>
                <p class="text-sm text-ink-faint mt-1">Respons formulir konsultasi dari siswa akan langsung tampil di sini.</p>
            </div>
        @endforelse
    </div>
</section>

{{-- Modal Penjadwalan Konsultasi (Bottom-Sheet di Mobile, Modal Tengah di Desktop) --}}
<div id="schedule-modal"
     class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-ink/50 backdrop-blur-[1px]"
     role="dialog" aria-modal="true" aria-labelledby="schedule-modal-title">
    <div class="w-full max-w-lg bg-sheet border-t sm:border border-rule rounded-t-2xl sm:rounded-none shadow-xl overflow-hidden max-h-[92dvh] overflow-y-auto">
        <div class="w-10 h-1 bg-rule rounded-full mx-auto mt-3 sm:hidden" aria-hidden="true"></div>
        <div class="px-6 py-5 border-b border-ink flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-accent">Penjadwalan Konsultasi</p>
                <h2 id="schedule-modal-title" class="font-serif text-2xl font-medium text-ink mt-1">Atur waktu &amp; tempat</h2>
            </div>
            <button type="button" onclick="closeScheduleModal()" aria-label="Tutup jendela penjadwalan"
                    class="w-9 h-9 rounded-md border border-rule bg-paper/60 text-ink flex items-center justify-center hover:bg-paper transition-colors shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>

        <form id="schedule-modal-form" method="POST" action="" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            {{-- Ringkasan permintaan siswa --}}
            <div class="bg-paper/60 border border-rule p-4 space-y-1 text-xs">
                <p id="schedule-student" class="font-semibold text-sm text-ink"></p>
                <p id="schedule-topic" class="text-ink-soft"></p>
                <div class="pt-2 mt-2 border-t border-rule/80 space-y-0.5 text-ink-faint">
                    <p>Waktu pilihan siswa: <span id="schedule-pref-time" class="text-ink font-medium"></span></p>
                    <p>Cara pendampingan: <span id="schedule-pref-method" class="text-ink font-medium"></span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="scheduled-date" class="block text-xs font-semibold text-ink mb-1.5">Tanggal Konsultasi <span class="text-red-600">*</span></label>
                    <input type="date" id="scheduled-date" name="scheduled_date" required
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                </div>
                <div>
                    <label for="scheduled-time" class="block text-xs font-semibold text-ink mb-1.5">Jam Konsultasi <span class="text-red-600">*</span></label>
                    <input type="time" id="scheduled-time" name="scheduled_time" required
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                </div>
            </div>

            <div>
                <label for="scheduled-location" class="block text-xs font-semibold text-ink mb-1.5">Tempat / Tautan Pertemuan <span class="text-red-600">*</span></label>
                <input type="text" id="scheduled-location" name="scheduled_location" required maxlength="150"
                       placeholder="Contoh: Ruang BK atau tautan Google Meet"
                       class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] text-ink-faint mr-1">Pilihan cepat:</span>
                    @foreach (['Ruang BK', 'Google Meet', 'Ruang Kelas', 'Perpustakaan'] as $preset)
                        <button type="button"
                                onclick="document.getElementById('scheduled-location').value = @js($preset); document.getElementById('scheduled-location').focus();"
                                class="px-2.5 py-1 rounded border border-rule bg-paper/50 hover:border-ink text-[11px] text-ink-soft hover:text-ink transition-colors">
                            {{ $preset }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="scheduled-note" class="block text-xs font-semibold text-ink mb-1.5">
                    Catatan Guru BK <span class="font-normal text-ink-faint">(opsional)</span>
                </label>
                <textarea id="scheduled-note" name="scheduled_note" rows="2" maxlength="300"
                          placeholder="Contoh: Silakan datang 5 menit sebelum sesi dan bawa catatan tugas minggu ini."
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition"></textarea>
            </div>

            <div class="pt-3 border-t border-rule flex items-center justify-end gap-3">
                <button type="button" onclick="closeScheduleModal()"
                        class="px-4 py-2.5 rounded-md border border-rule bg-paper/60 hover:border-ink text-xs sm:text-sm font-medium text-ink transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-accent hover:bg-accent-dark text-sheet text-xs sm:text-sm font-semibold transition-colors">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const scheduleModal = document.getElementById('schedule-modal');
    const scheduleForm = document.getElementById('schedule-modal-form');

    function openScheduleModal(data) {
        if (!scheduleModal || !scheduleForm) return;

        scheduleForm.action = data.action;
        document.getElementById('schedule-modal-title').textContent = data.is_scheduled
            ? 'Ubah jadwal konsultasi'
            : 'Atur waktu & tempat';
        document.getElementById('schedule-student').textContent = data.student;
        document.getElementById('schedule-topic').textContent = data.topic;
        document.getElementById('schedule-pref-time').textContent = data.preferred_time;
        document.getElementById('schedule-pref-method').textContent = data.method;
        document.getElementById('scheduled-date').value = data.date || '';
        document.getElementById('scheduled-time').value = data.time || '';
        document.getElementById('scheduled-location').value = data.location || '';
        document.getElementById('scheduled-note').value = data.note || '';

        scheduleModal.classList.remove('hidden');
        scheduleModal.classList.add('flex');
        document.getElementById('scheduled-date')?.focus();
    }

    function closeScheduleModal() {
        if (!scheduleModal) return;
        scheduleModal.classList.add('hidden');
        scheduleModal.classList.remove('flex');
    }

    scheduleModal?.addEventListener('click', (e) => {
        if (e.target === scheduleModal) {
            closeScheduleModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && scheduleModal && !scheduleModal.classList.contains('hidden')) {
            closeScheduleModal();
        }
    });
</script>
@endpush
@endsection
