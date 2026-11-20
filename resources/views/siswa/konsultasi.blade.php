@extends('layouts.app')

@section('title', 'Formulir Konsultasi BK')

@section('content')
@php
    $cfg = $formConfig ?? \App\Support\ConsultationFormConfig::get();
    $selectedDifficulty = old('difficulty');
@endphp

<section class="pb-10 border-b border-ink">
    <p class="text-sm text-ink-faint mb-3">Langkah 4 · Layanan Konsultasi BK</p>
    <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
        Formulir <span class="marker italic">Konsultasi.</span>
    </h1>
    <p class="mt-4 text-base text-ink-soft max-w-3xl leading-relaxed whitespace-pre-line">
        {{ $cfg['intro'] }}
    </p>
</section>

<div class="mt-12 grid lg:grid-cols-12 gap-10 lg:gap-12 items-start">

    {{-- Kolom Kiri (7 kolom): Formulir Konsultasi Web Dinamis --}}
    <div class="lg:col-span-7">
        <form method="POST" action="{{ route('siswa.consultations.store') }}" class="bg-sheet border border-rule p-6 sm:p-8 lg:p-10 space-y-7">
            @csrf

            <div class="pb-4 border-b border-ink flex flex-wrap items-baseline justify-between gap-4">
                <div>
                    <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Isi formulir konsultasi</h2>
                    <p class="text-xs text-ink-faint mt-1">
                        Otomatis tercatat atas nama <strong class="text-ink">{{ $student->name }}</strong> · {{ $student->kelas }}
                    </p>
                </div>
                <span class="text-xs text-red-700 font-medium">* Wajib diisi</span>
            </div>

            @if ($errors->any())
                <div class="border-l-2 border-red-600 pl-4 py-1 text-red-800 text-xs space-y-1">
                    @foreach ($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Email (jika siswa login dengan Google) --}}
            @if (filled($student->google_email))
                <div class="bg-paper/60 border border-rule p-4 rounded-md space-y-2">
                    <p class="text-xs font-semibold text-ink">Email</p>
                    <label class="flex items-start gap-2.5 text-sm text-ink-soft cursor-pointer">
                        <input type="checkbox" name="record_email" value="1" @checked(old('record_email', true))
                               class="mt-1 rounded border-rule text-accent focus:ring-accent">
                        <span>
                            Sertakan <strong class="text-ink">{{ $student->google_email }}</strong> sebagai email yang dicatat bersama formulir saya
                        </span>
                    </label>
                </div>
            @endif

            {{-- Pertanyaan 1 --}}
            <fieldset class="{{ filled($student->google_email) ? 'pt-6 border-t border-rule' : '' }} space-y-3">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    1. {{ $cfg['q1_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="space-y-2.5 pt-1">
                    @foreach ($cfg['q1_options'] as $option)
                        <label class="flex items-start gap-3 p-3 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                            <input type="radio" name="difficulty" value="{{ $option }}" required
                                   @checked($selectedDifficulty === $option)
                                   onchange="toggleOtherDifficulty()"
                                   class="mt-0.5 text-accent border-rule focus:ring-accent">
                            <span class="text-sm text-ink leading-snug">{{ $option }}</span>
                        </label>
                    @endforeach

                    @if ($cfg['q1_allow_other'])
                        <div class="p-3 rounded-md border border-rule/80 bg-paper/35 space-y-2.5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" id="difficulty-other-radio" name="difficulty" value="__other__" required
                                       @checked($selectedDifficulty === '__other__')
                                       onchange="toggleOtherDifficulty()"
                                       class="text-accent border-rule focus:ring-accent">
                                <span class="text-sm text-ink">Yang lain:</span>
                            </label>
                            <input type="text" id="difficulty-other-input" name="difficulty_other" maxlength="200"
                                   value="{{ old('difficulty_other') }}"
                                   placeholder="Tuliskan kesulitan belajarmu di sini..."
                                   onfocus="document.getElementById('difficulty-other-radio').checked = true; toggleOtherDifficulty();"
                                   class="w-full px-3.5 py-2 bg-sheet border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:border-ink transition">
                        </div>
                    @endif
                </div>
            </fieldset>

            {{-- Pertanyaan 2: Skala fokus 1-4 --}}
            <fieldset class="pt-6 border-t border-rule space-y-4">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    2. {{ $cfg['q2_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="bg-paper/45 border border-rule rounded-md p-5 space-y-3">
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ([1, 2, 3, 4] as $scale)
                            <label class="cursor-pointer">
                                <input type="radio" name="focus_level" value="{{ $scale }}" required class="peer sr-only"
                                       @checked((string) old('focus_level') === (string) $scale)>
                                <div class="py-3.5 text-center rounded-md border border-rule bg-sheet font-serif text-2xl text-ink peer-checked:border-accent peer-checked:bg-accent peer-checked:text-sheet transition">
                                    {{ $scale }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between gap-4 text-xs text-ink-faint pt-1">
                        <span>1 · {{ $cfg['q2_min_label'] }}</span>
                        <span class="text-right">4 · {{ $cfg['q2_max_label'] }}</span>
                    </div>
                </div>
            </fieldset>

            {{-- Pertanyaan 3 --}}
            <fieldset class="pt-6 border-t border-rule space-y-3">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    3. {{ $cfg['q3_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="space-y-2.5 pt-1">
                    @foreach ($cfg['q3_options'] as $topicOption)
                        <label class="flex items-start gap-3 p-3 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                            <input type="radio" name="topic" value="{{ $topicOption }}" required
                                   @checked(old('topic') === $topicOption)
                                   class="mt-0.5 text-accent border-rule focus:ring-accent">
                            <span class="text-sm text-ink leading-snug">{{ $topicOption }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- Pertanyaan 4 --}}
            <fieldset class="pt-6 border-t border-rule space-y-3">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    4. {{ $cfg['q4_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="grid sm:grid-cols-2 gap-3 pt-1">
                    @foreach ($cfg['q4_options'] as $journalOption)
                        <label class="flex items-start gap-3 p-3.5 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                            <input type="radio" name="bring_journal" value="{{ $journalOption }}" required
                                   @checked(old('bring_journal') === $journalOption)
                                   class="mt-0.5 text-accent border-rule focus:ring-accent">
                            <span class="text-sm text-ink leading-snug">{{ $journalOption }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- Pertanyaan 5: Preferensi Waktu --}}
            <fieldset class="pt-6 border-t border-rule space-y-3">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    5. {{ $cfg['q5_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="space-y-2.5 pt-1">
                    @foreach ($cfg['q5_options'] as $timeOption)
                        <label class="flex items-start gap-3 p-3 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                            <input type="radio" name="mode" value="{{ $timeOption }}" required
                                   @checked(old('mode') === $timeOption)
                                   class="mt-0.5 text-accent border-rule focus:ring-accent">
                            <span class="text-sm text-ink leading-snug">{{ $timeOption }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- Pertanyaan 6: Cara Pendampingan / Bentuk Pertemuan --}}
            <fieldset class="pt-6 border-t border-rule space-y-3">
                <legend class="text-sm font-semibold text-ink leading-snug">
                    6. {{ $cfg['q6_label'] }} <span class="text-red-600">*</span>
                </legend>
                <div class="grid sm:grid-cols-2 gap-2.5 pt-1">
                    @foreach ($cfg['q6_options'] as $methodOption)
                        <label class="flex items-start gap-3 p-3 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                            <input type="radio" name="consultation_method" value="{{ $methodOption }}" required
                                   @checked(old('consultation_method') === $methodOption)
                                   class="mt-0.5 text-accent border-rule focus:ring-accent">
                            <span class="text-sm text-ink leading-snug">{{ $methodOption }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- Catatan tambahan opsional --}}
            <div class="pt-6 border-t border-rule">
                <label for="message" class="block text-sm font-semibold text-ink mb-1.5">
                    {{ $cfg['note_label'] }} <span class="text-xs font-normal text-ink-faint">(opsional)</span>
                </label>
                <textarea id="message" name="message" rows="3" maxlength="600"
                          placeholder="{{ $cfg['note_placeholder'] }}"
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-6 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Kirim Formulir Konsultasi
            </button>
        </form>
    </div>

    {{-- Kolom Kanan (5 kolom): Riwayat Konsultasi Siswa --}}
    <div class="lg:col-span-5 bg-sheet border border-rule">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Riwayat formulirmu</h2>
                <p class="text-xs text-ink-faint mt-0.5">Status penjadwalan diperbarui langsung oleh Guru BK</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $consultations->count() }} kiriman</span>
        </div>

        <div class="divide-y divide-rule">
            @forelse ($consultations as $item)
                <article class="p-6 sm:p-7 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs text-ink-faint">
                            {{ $item->created_at?->translatedFormat('d F Y, H:i') }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $item->status === 'sudah_dijadwalkan' ? 'bg-accent/10 text-accent' : 'bg-[#F9DD7A]/55 text-ink' }}">
                            {{ $item->statusLabel() }}
                        </span>
                    </div>

                    <h3 class="font-serif text-xl font-medium text-ink leading-snug">{{ $item->topic }}</h3>

                    <dl class="space-y-1.5 text-xs text-ink-soft pt-1">
                        @if ($item->difficulty)
                            <div><span class="text-ink-faint">Kesulitan utama:</span> {{ $item->difficulty }}</div>
                        @endif
                        @if ($item->focus_level)
                            <div><span class="text-ink-faint">Tingkat fokus minggu ini:</span> <strong class="text-ink">{{ $item->focus_level }} / 4</strong></div>
                        @endif
                        @if ($item->bring_journal)
                            <div><span class="text-ink-faint">Catatan jurnal:</span> {{ $item->bring_journal }}</div>
                        @endif
                        <div><span class="text-ink-faint">Waktu pilihan:</span> {{ $item->mode }}</div>
                        @if ($item->consultation_method)
                            <div><span class="text-ink-faint">Cara pendampingan:</span> {{ $item->consultation_method }}</div>
                        @endif
                    </dl>

                    @if (filled($item->message))
                        <p class="text-sm text-ink-soft bg-paper/55 border-l-2 border-rule pl-3.5 py-2 leading-relaxed whitespace-pre-line">{{ $item->message }}</p>
                    @endif

                    @if ($item->scheduled_at)
                        <p class="text-xs text-accent font-semibold pt-1">
                            ✓ Dijadwalkan pada {{ $item->scheduled_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    @endif
                </article>
            @empty
                <div class="p-12 text-center">
                    <p class="font-serif text-2xl text-ink">Belum ada formulir terkirim</p>
                    <p class="text-sm text-ink-faint mt-1">Formulir konsultasi yang kamu kirim akan tercatat rapi di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleOtherDifficulty() {
        const radio = document.getElementById('difficulty-other-radio');
        const input = document.getElementById('difficulty-other-input');
        if (!radio || !input) return;
        input.required = radio.checked;
    }
    document.addEventListener('DOMContentLoaded', toggleOtherDifficulty);
</script>
@endpush
@endsection
