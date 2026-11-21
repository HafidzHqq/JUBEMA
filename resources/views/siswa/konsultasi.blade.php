@extends('layouts.app')

@section('title', 'Formulir Konsultasi BK')

@section('content')
@php
    $cfg = $formConfig ?? \App\Support\ConsultationFormConfig::get();
    $questions = $cfg['questions'] ?? [];
@endphp

{{-- Pembuka Halaman: Selaras dengan gaya editorial Beranda, Perencana & Refleksi --}}
<section class="pb-7 sm:pb-10 border-b border-ink">
    <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Langkah 4 · Layanan Konsultasi BK</p>
    <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
        Formulir <span class="marker italic">Konsultasi.</span>
    </h1>
    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-3xl leading-relaxed whitespace-pre-line">
        {{ $cfg['intro'] }}
    </p>
</section>

{{-- Grid 2 Kolom Simetris: Header, garis pemisah, dan tinggi kartu sejajar --}}
<div class="mt-7 sm:mt-10 grid xl:grid-cols-12 gap-6 sm:gap-8 items-stretch">

    {{-- Kolom Kiri (7 kolom): Formulir Konsultasi Web Dinamis --}}
    <section class="xl:col-span-7 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex flex-wrap items-baseline justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Isi formulir konsultasi</h2>
                <p class="text-xs text-ink-faint mt-1">
                    Otomatis tercatat atas nama <strong class="text-ink">{{ $student->name }}</strong> · {{ $student->kelas }}
                </p>
            </div>
            <span class="text-xs text-red-700 font-medium shrink-0">* Wajib diisi</span>
        </div>

        <form method="POST" action="{{ route('siswa.consultations.store') }}" class="p-6 sm:p-8 flex-1 flex flex-col justify-between gap-7">
            <div class="space-y-6">
                @csrf

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

                {{-- Daftar Pertanyaan Dinamis dari Konfigurasi Guru BK --}}
                @foreach ($questions as $index => $q)
                    @php
                        $qId = $q['id'];
                        $qNum = $index + 1;
                        $qType = $q['type'] ?? 'text';
                        $isRequired = ! empty($q['required']);
                        $oldVal = old("answers.{$qId}");
                        $oldOther = old("answers_other.{$qId}");
                        $dividerClass = ($index > 0 || filled($student->google_email)) ? 'pt-6 border-t border-rule' : '';
                    @endphp

                    @if ($qType === 'radio')
                        <fieldset class="{{ $dividerClass }} space-y-3">
                            <legend class="text-sm font-semibold text-ink leading-snug">
                                {{ $qNum }}. {{ $q['label'] }}
                                @if ($isRequired)
                                    <span class="text-red-600">*</span>
                                @else
                                    <span class="text-xs font-normal text-ink-faint">(opsional)</span>
                                @endif
                            </legend>
                            @php
                                $options = $q['options'] ?? [];
                                $twoCols = count($options) <= 4 && empty($q['allow_other']) && collect($options)->every(fn ($o) => mb_strlen($o) <= 48);
                            @endphp
                            <div class="{{ $twoCols ? 'grid sm:grid-cols-2 gap-2.5' : 'space-y-2' }} pt-0.5">
                                @foreach ($options as $option)
                                    <label class="flex items-start gap-3 p-3 rounded-md border border-rule/80 bg-paper/35 hover:border-ink/40 cursor-pointer transition-colors">
                                        <input type="radio" name="answers[{{ $qId }}]" value="{{ $option }}"
                                               {{ $isRequired ? 'required' : '' }}
                                               @checked($oldVal === $option)
                                               onchange="syncOtherInputs()"
                                               class="mt-0.5 text-accent border-rule focus:ring-accent">
                                        <span class="text-sm text-ink leading-snug">{{ $option }}</span>
                                    </label>
                                @endforeach

                                @if (! empty($q['allow_other']))
                                    <div class="p-3 rounded-md border border-rule/80 bg-paper/35 space-y-2.5">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio"
                                                   data-other-radio="{{ $qId }}"
                                                   name="answers[{{ $qId }}]"
                                                   value="__other__"
                                                   {{ $isRequired ? 'required' : '' }}
                                                   @checked($oldVal === '__other__')
                                                   onchange="syncOtherInputs()"
                                                   class="text-accent border-rule focus:ring-accent">
                                            <span class="text-sm text-ink">Yang lain:</span>
                                        </label>
                                        <input type="text"
                                               data-other-input="{{ $qId }}"
                                               name="answers_other[{{ $qId }}]"
                                               maxlength="200"
                                               value="{{ $oldOther }}"
                                               placeholder="Tuliskan jawaban lainnya di sini..."
                                               onfocus="selectOtherRadio('{{ $qId }}')"
                                               class="w-full h-[40px] px-3.5 bg-sheet border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:border-ink transition">
                                    </div>
                                @endif
                            </div>
                        </fieldset>

                    @elseif ($qType === 'scale')
                        <fieldset class="{{ $dividerClass }} space-y-3.5">
                            <legend class="text-sm font-semibold text-ink leading-snug">
                                {{ $qNum }}. {{ $q['label'] }}
                                @if ($isRequired)
                                    <span class="text-red-600">*</span>
                                @else
                                    <span class="text-xs font-normal text-ink-faint">(opsional)</span>
                                @endif
                            </legend>
                            <div class="bg-paper/45 border border-rule rounded-md p-4 sm:p-5 space-y-3">
                                <div class="grid grid-cols-4 gap-2.5 sm:gap-3">
                                    @foreach ([1, 2, 3, 4] as $scale)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="answers[{{ $qId }}]" value="{{ $scale }}"
                                                   {{ $isRequired ? 'required' : '' }}
                                                   class="peer sr-only"
                                                   @checked((string) $oldVal === (string) $scale)>
                                            <div class="py-3 text-center rounded-md border border-rule bg-sheet font-serif text-2xl text-ink peer-checked:border-accent peer-checked:bg-accent peer-checked:text-sheet transition">
                                                {{ $scale }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-between gap-4 text-xs text-ink-faint pt-1">
                                    <span>1 · {{ $q['min_label'] ?: 'Sangat kurang' }}</span>
                                    <span class="text-right">4 · {{ $q['max_label'] ?: 'Sangat baik' }}</span>
                                </div>
                            </div>
                        </fieldset>

                    @elseif ($qType === 'text')
                        <div class="{{ $dividerClass }}">
                            <label for="answer-{{ $qId }}" class="block text-sm font-semibold text-ink mb-2 leading-snug">
                                {{ $qNum }}. {{ $q['label'] }}
                                @if ($isRequired)
                                    <span class="text-red-600">*</span>
                                @else
                                    <span class="text-xs font-normal text-ink-faint">(opsional)</span>
                                @endif
                            </label>
                            <input id="answer-{{ $qId }}" type="text" name="answers[{{ $qId }}]"
                                   {{ $isRequired ? 'required' : '' }} maxlength="250"
                                   value="{{ $oldVal }}"
                                   placeholder="{{ $q['placeholder'] ?: 'Tuliskan jawabanmu di sini...' }}"
                                   class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                        </div>

                    @else
                        <div class="{{ $dividerClass }}">
                            <label for="answer-{{ $qId }}" class="block text-sm font-semibold text-ink mb-2 leading-snug">
                                {{ $qNum }}. {{ $q['label'] }}
                                @if ($isRequired)
                                    <span class="text-red-600">*</span>
                                @else
                                    <span class="text-xs font-normal text-ink-faint">(opsional)</span>
                                @endif
                            </label>
                            <textarea id="answer-{{ $qId }}" name="answers[{{ $qId }}]" rows="3"
                                      {{ $isRequired ? 'required' : '' }} maxlength="1000"
                                      placeholder="{{ $q['placeholder'] ?: 'Tuliskan jawabanmu di sini...' }}"
                                      class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ $oldVal }}</textarea>
                        </div>
                    @endif
                @endforeach
            </div>

            <button type="submit"
                    class="w-full py-3.5 px-6 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Kirim Formulir Konsultasi
            </button>
        </form>
    </section>

    {{-- Kolom Kanan (5 kolom): Riwayat Konsultasi Siswa --}}
    <section class="xl:col-span-5 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Riwayat formulirmu</h2>
                <p class="text-xs text-ink-faint mt-1">Status penjadwalan diperbarui langsung oleh Guru BK.</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $consultations->count() }} kiriman</span>
        </div>

        @if ($consultations->isEmpty())
            <div class="flex-1 p-10 sm:p-14 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-paper border border-rule flex items-center justify-center text-accent mb-4" aria-hidden="true">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <p class="font-serif text-2xl text-ink">Belum ada formulir terkirim</p>
                <p class="text-sm text-ink-faint mt-1.5 max-w-xs leading-relaxed">
                    Formulir konsultasi yang kamu kirim akan tercatat rapi di sini beserta status penjadwalan dari Guru BK.
                </p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($consultations as $item)
                    @php $itemAnswers = $item->formattedAnswers(); @endphp
                    <article class="p-6 sm:p-8 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs text-ink-faint">
                                {{ $item->created_at?->translatedFormat('d F Y, H:i') }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $item->status === 'sudah_dijadwalkan' ? 'bg-accent/10 text-accent' : 'bg-[#F9DD7A]/55 text-ink' }}">
                                    {{ $item->statusLabel() }}
                                </span>
                                <form method="POST" action="{{ route('siswa.consultations.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus formulir konsultasi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-0.5 rounded border border-rule bg-paper hover:border-red-300 hover:bg-red-50 text-[11px] font-medium text-red-700 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="font-serif text-xl font-medium text-ink leading-snug">{{ $item->displayTopic() }}</h3>

                        @php
                            $headingText = $item->displayTopic();
                            $skipHeadingOnce = false;
                        @endphp
                        <dl class="space-y-1.5 text-xs text-ink-soft pt-1">
                            @foreach ($itemAnswers as $ans)
                                @php
                                    if (! $skipHeadingOnce && ($ans['id'] === 'topic' || $ans['value'] === $headingText)) {
                                        $skipHeadingOnce = true;
                                        continue;
                                    }
                                @endphp
                                @if ($ans['type'] !== 'textarea')
                                    <div>
                                        <span class="text-ink-faint">{{ $ans['label'] }}:</span>
                                        <span class="text-ink">{{ $ans['value'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </dl>

                        @foreach ($itemAnswers as $ans)
                            @if ($ans['type'] === 'textarea')
                                <div class="bg-paper/55 border-l-2 border-rule pl-3.5 py-2 space-y-0.5">
                                    <p class="text-[11px] font-semibold text-ink-faint">{{ $ans['label'] }}</p>
                                    <p class="text-sm text-ink-soft leading-relaxed whitespace-pre-line">{{ $ans['value'] }}</p>
                                </div>
                            @endif
                        @endforeach

                        @if ($item->status === 'sudah_dijadwalkan' && $item->scheduled_at)
                            <div class="bg-accent/[0.07] border-l-2 border-accent pl-3.5 py-2.5 pr-3 space-y-1 text-xs">
                                <p class="font-semibold text-accent">✓ Jadwal Konsultasi dari Guru BK</p>
                                <p class="text-ink">
                                    <span class="text-ink-faint">Waktu:</span>
                                    <strong>{{ $item->scheduled_at->translatedFormat('l, d F Y · H:i') }} WIB</strong>
                                </p>
                                @if (filled($item->scheduled_location))
                                    <p class="text-ink break-words">
                                        <span class="text-ink-faint">Tempat:</span>
                                        @if (filter_var($item->scheduled_location, FILTER_VALIDATE_URL))
                                            <a href="{{ $item->scheduled_location }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-accent underline">
                                                {{ $item->scheduled_location }} ↗
                                            </a>
                                        @else
                                            <strong>{{ $item->scheduled_location }}</strong>
                                        @endif
                                    </p>
                                @endif
                                @if (filled($item->scheduled_note))
                                    <p class="text-ink-soft pt-0.5">
                                        <span class="text-ink-faint">Catatan:</span> {{ $item->scheduled_note }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>

@push('scripts')
<script>
    function selectOtherRadio(questionId) {
        const radio = document.querySelector(`input[data-other-radio="${questionId}"]`);
        if (radio) {
            radio.checked = true;
            syncOtherInputs();
        }
    }

    function syncOtherInputs() {
        document.querySelectorAll('input[data-other-radio]').forEach((radio) => {
            const qId = radio.dataset.otherRadio;
            const input = document.querySelector(`input[data-other-input="${qId}"]`);
            if (input) {
                input.required = radio.checked;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', syncOtherInputs);
</script>
@endpush
@endsection
