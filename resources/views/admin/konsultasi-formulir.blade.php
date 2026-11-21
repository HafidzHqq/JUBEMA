@extends('layouts.app')

@section('title', 'Edit Formulir Konsultasi · Ruang Guru BK')

@section('content')
@php
    $questions = old('questions', $formConfig['questions']);
    if (! is_array($questions) || count($questions) === 0) {
        $questions = $formConfig['questions'];
    }
    $questionTypes = \App\Support\ConsultationFormConfig::QUESTION_TYPES;
@endphp

<section class="grid lg:grid-cols-12 gap-5 sm:gap-8 pb-7 sm:pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <a href="{{ route('admin.konsultasi-masuk') }}" class="inline-block text-xs sm:text-sm text-ink-soft hover:text-ink mb-2 sm:mb-3 transition-colors">
            ← Kembali ke Konsultasi Masuk
        </a>
        <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
            Edit pertanyaan <span class="marker italic">formulir konsultasi.</span>
        </h1>
        <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-2xl leading-relaxed">
            Tambah pertanyaan baru, hapus pertanyaan yang tidak diperlukan, ubah jenis jawaban, atau atur urutannya.
            Perubahan langsung aktif di halaman siswa tanpa menghapus respons lama.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <form method="POST" action="{{ route('admin.konsultasi.form.reset') }}"
              onsubmit="return confirm('Kembalikan seluruh daftar pertanyaan dan pilihan formulir ke bawaan awal?');">
            @csrf
            <button type="submit"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-xs sm:text-sm font-medium text-ink-soft hover:text-ink transition-colors">
                Kembalikan ke Bawaan
            </button>
        </form>
    </div>
</section>

@if ($errors->any())
    <div class="mt-8 bg-sheet border border-red-300 border-l-4 border-l-red-600 px-5 py-4 text-red-800 text-xs space-y-1 max-w-4xl">
        <p class="font-semibold text-sm text-red-900">Periksa kembali isian formulir:</p>
        @foreach ($errors->all() as $err)
            <p>• {{ $err }}</p>
        @endforeach
    </div>
@endif

<form id="consultation-builder-form" method="POST" action="{{ route('admin.konsultasi.form.update') }}" class="mt-10 space-y-6 max-w-4xl">
    @csrf
    @method('PUT')

    {{-- Pengantar Formulir --}}
    <section class="bg-sheet border border-rule">
        <div class="px-6 sm:px-8 py-5 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium text-ink">Pengantar formulir</h2>
            <p class="text-xs text-ink-faint mt-1">Paragraf pembuka yang dibaca siswa di bagian atas formulir.</p>
        </div>
        <div class="p-6 sm:p-8">
            <label for="intro" class="block text-xs font-semibold text-ink mb-1.5">Teks Pengantar</label>
            <textarea id="intro" name="intro" rows="3" required maxlength="1000"
                      class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">{{ old('intro', $formConfig['intro']) }}</textarea>
        </div>
    </section>

    {{-- Daftar Pertanyaan Dinamis --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
        <div>
            <h2 class="font-serif text-2xl font-medium text-ink">Daftar pertanyaan</h2>
            <p class="text-xs text-ink-faint mt-0.5">
                Total <strong id="question-count" class="text-ink">{{ count($questions) }}</strong> pertanyaan aktif pada formulir siswa.
            </p>
        </div>
        <button type="button" onclick="addQuestionCard('radio')"
                class="px-4 py-2.5 rounded-md bg-ink hover:bg-black text-sheet text-xs sm:text-sm font-semibold transition-colors">
            + Tambah Pertanyaan
        </button>
    </div>

    <div id="questions-container" class="space-y-6">
        @foreach (array_values($questions) as $idx => $q)
            @php
                $qId = $q['id'] ?? ('q_'.($idx + 1));
                $qType = $q['type'] ?? 'radio';
                $qRequired = ! empty($q['required']);
                $qOptions = is_array($q['options'] ?? null) && count($q['options']) > 0
                    ? $q['options']
                    : ['Pilihan 1', 'Pilihan 2'];
                $qAllowOther = ! empty($q['allow_other']);
                $qMinLabel = $q['min_label'] ?? 'Sangat kurang';
                $qMaxLabel = $q['max_label'] ?? 'Sangat baik';
                $qPlaceholder = $q['placeholder'] ?? 'Tuliskan jawabanmu di sini...';
            @endphp
            <section class="question-card bg-sheet border border-rule" data-index="{{ $idx }}">
                <input type="hidden" data-field="id" name="questions[{{ $idx }}][id]" value="{{ $qId }}">

                <div class="px-6 sm:px-8 py-4 border-b border-ink flex flex-wrap items-center justify-between gap-3 bg-paper/35">
                    <div class="flex items-center gap-2.5">
                        <span class="question-number font-serif text-xl font-semibold text-accent leading-none">#{{ $idx + 1 }}</span>
                        <h3 class="font-serif text-xl font-medium text-ink">Pertanyaan <span class="question-number-text">{{ $idx + 1 }}</span></h3>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="moveQuestion(this, -1)" title="Geser ke atas"
                                class="px-2.5 py-1.5 rounded border border-rule bg-sheet hover:border-ink text-xs text-ink-soft hover:text-ink transition-colors">
                            ↑
                        </button>
                        <button type="button" onclick="moveQuestion(this, 1)" title="Geser ke bawah"
                                class="px-2.5 py-1.5 rounded border border-rule bg-sheet hover:border-ink text-xs text-ink-soft hover:text-ink transition-colors">
                            ↓
                        </button>
                        <button type="button" onclick="removeQuestionCard(this)"
                                class="ml-1 px-3 py-1.5 rounded border border-rule bg-sheet hover:border-red-300 hover:bg-red-50 text-xs font-medium text-red-700 transition-colors">
                            Hapus Pertanyaan
                        </button>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-5">
                    <div class="grid sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-8">
                            <label class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan <span class="text-red-600">*</span></label>
                            <input type="text" data-field="label" name="questions[{{ $idx }}][label]" required maxlength="250"
                                   value="{{ $q['label'] ?? '' }}"
                                   placeholder="Tulis pertanyaan yang akan dijawab siswa..."
                                   class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-ink mb-1.5">Jenis Jawaban</label>
                            <select data-field="type" name="questions[{{ $idx }}][type]" onchange="handleTypeChange(this)"
                                    class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                                @foreach ($questionTypes as $typeKey => $typeLabel)
                                    <option value="{{ $typeKey }}" @selected($qType === $typeKey)>{{ $typeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Panel Pilihan Ganda --}}
                    <div class="panel-radio space-y-3 {{ $qType === 'radio' ? '' : 'hidden' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                            <button type="button" onclick="addOptionToCard(this)"
                                    class="text-xs font-semibold text-accent hover:underline">
                                + Tambah pilihan
                            </button>
                        </div>
                        <div class="options-list space-y-2">
                            @foreach ($qOptions as $opt)
                                <div class="option-row flex items-center gap-2">
                                    <input type="text" data-field="option" name="questions[{{ $idx }}][options][]"
                                           {{ $qType === 'radio' ? 'required' : '' }} maxlength="200" value="{{ $opt }}"
                                           placeholder="Tulis pilihan jawaban..."
                                           class="flex-1 h-[38px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                                    <button type="button" onclick="removeOptionFromCard(this)"
                                            class="px-3 h-[38px] rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">
                                        Hapus
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <label class="flex items-center gap-2.5 text-xs sm:text-sm text-ink-soft pt-1 cursor-pointer">
                            <input type="checkbox" data-field="allow_other" name="questions[{{ $idx }}][allow_other]" value="1"
                                   @checked($qAllowOther)
                                   class="rounded border-rule text-accent focus:ring-accent">
                            <span>Aktifkan opsi tambahan <strong>"Yang lain:"</strong> (siswa bisa menulis jawaban sendiri)</span>
                        </label>
                    </div>

                    {{-- Panel Skala 1–4 --}}
                    <div class="panel-scale grid sm:grid-cols-2 gap-4 {{ $qType === 'scale' ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 1 (Terendah)</label>
                            <input type="text" data-field="min_label" name="questions[{{ $idx }}][min_label]"
                                   {{ $qType === 'scale' ? 'required' : '' }} maxlength="100" value="{{ $qMinLabel }}"
                                   class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 4 (Tertinggi)</label>
                            <input type="text" data-field="max_label" name="questions[{{ $idx }}][max_label]"
                                   {{ $qType === 'scale' ? 'required' : '' }} maxlength="100" value="{{ $qMaxLabel }}"
                                   class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        </div>
                    </div>

                    {{-- Panel Teks / Paragraf --}}
                    <div class="panel-text {{ in_array($qType, ['text', 'textarea'], true) ? '' : 'hidden' }}">
                        <label class="block text-xs font-semibold text-ink mb-1.5">Teks Petunjuk (Placeholder)</label>
                        <input type="text" data-field="placeholder" name="questions[{{ $idx }}][placeholder]" maxlength="250"
                               value="{{ $qPlaceholder }}"
                               placeholder="Contoh: Tuliskan jawabanmu di sini..."
                               class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                    </div>

                    {{-- Footer Pengaturan Wajib/Opsional --}}
                    <div class="pt-4 border-t border-rule flex items-center justify-between">
                        <label class="flex items-center gap-2.5 text-xs sm:text-sm text-ink cursor-pointer">
                            <input type="checkbox" data-field="required" name="questions[{{ $idx }}][required]" value="1"
                                   @checked($qRequired)
                                   class="rounded border-rule text-accent focus:ring-accent">
                            <span class="font-medium">Wajib diisi oleh siswa</span>
                        </label>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- Tombol Tambah Pertanyaan di Bawah Daftar --}}
    <div class="border border-dashed border-rule bg-sheet/60 p-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs sm:text-sm text-ink-soft">Ingin menambah pertanyaan baru pada formulir konsultasi?</p>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="addQuestionCard('radio')"
                    class="px-3.5 py-2 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-semibold text-ink transition-colors">
                + Pilihan Ganda
            </button>
            <button type="button" onclick="addQuestionCard('text')"
                    class="px-3.5 py-2 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-semibold text-ink transition-colors">
                + Jawaban Singkat
            </button>
            <button type="button" onclick="addQuestionCard('textarea')"
                    class="px-3.5 py-2 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-semibold text-ink transition-colors">
                + Paragraf
            </button>
            <button type="button" onclick="addQuestionCard('scale')"
                    class="px-3.5 py-2 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-semibold text-ink transition-colors">
                + Skala 1–4
            </button>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 pt-3">
        <a href="{{ route('admin.konsultasi-masuk') }}" class="text-sm text-ink-soft hover:text-ink">
            Batal
        </a>
        <button type="submit"
                class="px-7 py-3.5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
            Simpan Perubahan Formulir
        </button>
    </div>
</form>

{{-- Template Pertanyaan Baru --}}
<template id="question-card-template">
    <section class="question-card bg-sheet border border-rule" data-index="__IDX__">
        <input type="hidden" data-field="id" name="questions[__IDX__][id]" value="__ID__">

        <div class="px-6 sm:px-8 py-4 border-b border-ink flex flex-wrap items-center justify-between gap-3 bg-paper/35">
            <div class="flex items-center gap-2.5">
                <span class="question-number font-serif text-xl font-semibold text-accent leading-none">#__NUM__</span>
                <h3 class="font-serif text-xl font-medium text-ink">Pertanyaan <span class="question-number-text">__NUM__</span></h3>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="moveQuestion(this, -1)" title="Geser ke atas"
                        class="px-2.5 py-1.5 rounded border border-rule bg-sheet hover:border-ink text-xs text-ink-soft hover:text-ink transition-colors">
                    ↑
                </button>
                <button type="button" onclick="moveQuestion(this, 1)" title="Geser ke bawah"
                        class="px-2.5 py-1.5 rounded border border-rule bg-sheet hover:border-ink text-xs text-ink-soft hover:text-ink transition-colors">
                    ↓
                </button>
                <button type="button" onclick="removeQuestionCard(this)"
                        class="ml-1 px-3 py-1.5 rounded border border-rule bg-sheet hover:border-red-300 hover:bg-red-50 text-xs font-medium text-red-700 transition-colors">
                    Hapus Pertanyaan
                </button>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-5">
            <div class="grid sm:grid-cols-12 gap-4">
                <div class="sm:col-span-8">
                    <label class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan <span class="text-red-600">*</span></label>
                    <input type="text" data-field="label" name="questions[__IDX__][label]" required maxlength="250"
                           placeholder="Tulis pertanyaan yang akan dijawab siswa..."
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-ink mb-1.5">Jenis Jawaban</label>
                    <select data-field="type" name="questions[__IDX__][type]" onchange="handleTypeChange(this)"
                            class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        @foreach ($questionTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="panel-radio space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                    <button type="button" onclick="addOptionToCard(this)"
                            class="text-xs font-semibold text-accent hover:underline">
                        + Tambah pilihan
                    </button>
                </div>
                <div class="options-list space-y-2">
                    <div class="option-row flex items-center gap-2">
                        <input type="text" data-field="option" name="questions[__IDX__][options][]" required maxlength="200"
                               placeholder="Pilihan jawaban 1..."
                               class="flex-1 h-[38px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionFromCard(this)"
                                class="px-3 h-[38px] rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">
                            Hapus
                        </button>
                    </div>
                    <div class="option-row flex items-center gap-2">
                        <input type="text" data-field="option" name="questions[__IDX__][options][]" required maxlength="200"
                               placeholder="Pilihan jawaban 2..."
                               class="flex-1 h-[38px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionFromCard(this)"
                                class="px-3 h-[38px] rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2.5 text-xs sm:text-sm text-ink-soft pt-1 cursor-pointer">
                    <input type="checkbox" data-field="allow_other" name="questions[__IDX__][allow_other]" value="1"
                           class="rounded border-rule text-accent focus:ring-accent">
                    <span>Aktifkan opsi tambahan <strong>"Yang lain:"</strong> (siswa bisa menulis jawaban sendiri)</span>
                </label>
            </div>

            <div class="panel-scale grid sm:grid-cols-2 gap-4 hidden">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 1 (Terendah)</label>
                    <input type="text" data-field="min_label" name="questions[__IDX__][min_label]" maxlength="100"
                           value="Sangat kurang"
                           class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 4 (Tertinggi)</label>
                    <input type="text" data-field="max_label" name="questions[__IDX__][max_label]" maxlength="100"
                           value="Sangat baik"
                           class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                </div>
            </div>

            <div class="panel-text hidden">
                <label class="block text-xs font-semibold text-ink mb-1.5">Teks Petunjuk (Placeholder)</label>
                <input type="text" data-field="placeholder" name="questions[__IDX__][placeholder]" maxlength="250"
                       value="Tuliskan jawabanmu di sini..."
                       class="w-full h-[40px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            </div>

            <div class="pt-4 border-t border-rule flex items-center justify-between">
                <label class="flex items-center gap-2.5 text-xs sm:text-sm text-ink cursor-pointer">
                    <input type="checkbox" data-field="required" name="questions[__IDX__][required]" value="1" checked
                           class="rounded border-rule text-accent focus:ring-accent">
                    <span class="font-medium">Wajib diisi oleh siswa</span>
                </label>
            </div>
        </div>
    </section>
</template>

@push('scripts')
<script>
    function reindexQuestions() {
        const container = document.getElementById('questions-container');
        if (!container) return;

        const cards = container.querySelectorAll('.question-card');
        cards.forEach((card, index) => {
            const num = index + 1;
            card.dataset.index = String(index);

            const badge = card.querySelector('.question-number');
            if (badge) badge.textContent = `#${num}`;
            const numText = card.querySelector('.question-number-text');
            if (numText) numText.textContent = String(num);

            card.querySelectorAll('[data-field]').forEach((el) => {
                const field = el.dataset.field;
                if (field === 'option') {
                    el.name = `questions[${index}][options][]`;
                } else {
                    el.name = `questions[${index}][${field}]`;
                }
            });
        });

        const countEl = document.getElementById('question-count');
        if (countEl) countEl.textContent = String(cards.length);
    }

    function applyTypeVisibility(card, type) {
        const radioPanel = card.querySelector('.panel-radio');
        const scalePanel = card.querySelector('.panel-scale');
        const textPanel = card.querySelector('.panel-text');

        const isRadio = type === 'radio';
        const isScale = type === 'scale';
        const isText = type === 'text' || type === 'textarea';

        radioPanel?.classList.toggle('hidden', !isRadio);
        scalePanel?.classList.toggle('hidden', !isScale);
        textPanel?.classList.toggle('hidden', !isText);

        card.querySelectorAll('.options-list input[data-field="option"]').forEach((input) => {
            input.required = isRadio;
        });

        const minInput = card.querySelector('input[data-field="min_label"]');
        const maxInput = card.querySelector('input[data-field="max_label"]');
        if (minInput) minInput.required = isScale;
        if (maxInput) maxInput.required = isScale;
    }

    function handleTypeChange(selectEl) {
        const card = selectEl.closest('.question-card');
        if (!card) return;
        applyTypeVisibility(card, selectEl.value);
    }

    function addQuestionCard(defaultType = 'radio') {
        const container = document.getElementById('questions-container');
        const tpl = document.getElementById('question-card-template');
        if (!container || !tpl) return;

        const existingCount = container.querySelectorAll('.question-card').length;
        if (existingCount >= 25) {
            showToast('Maksimal 25 pertanyaan dalam satu formulir.', 'info');
            return;
        }

        const uniqueId = 'q_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 6);
        const html = tpl.innerHTML
            .replaceAll('__IDX__', String(existingCount))
            .replaceAll('__NUM__', String(existingCount + 1))
            .replaceAll('__ID__', uniqueId);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const card = wrapper.firstElementChild;
        container.appendChild(card);

        const typeSelect = card.querySelector('select[data-field="type"]');
        if (typeSelect) {
            typeSelect.value = defaultType;
            applyTypeVisibility(card, defaultType);
        }

        reindexQuestions();
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.querySelector('input[data-field="label"]')?.focus();
    }

    function removeQuestionCard(button) {
        const container = document.getElementById('questions-container');
        const card = button.closest('.question-card');
        if (!container || !card) return;

        if (container.querySelectorAll('.question-card').length <= 1) {
            showToast('Formulir minimal harus memiliki satu pertanyaan.', 'info');
            return;
        }

        card.remove();
        reindexQuestions();
    }

    function moveQuestion(button, direction) {
        const container = document.getElementById('questions-container');
        const card = button.closest('.question-card');
        if (!container || !card) return;

        if (direction < 0 && card.previousElementSibling) {
            container.insertBefore(card, card.previousElementSibling);
        } else if (direction > 0 && card.nextElementSibling) {
            container.insertBefore(card.nextElementSibling, card);
        }
        reindexQuestions();
    }

    function addOptionToCard(button) {
        const card = button.closest('.question-card');
        const list = card?.querySelector('.options-list');
        if (!card || !list) return;

        const idx = card.dataset.index || '0';
        const row = document.createElement('div');
        row.className = 'option-row flex items-center gap-2';
        row.innerHTML = `
            <input type="text" data-field="option" name="questions[${idx}][options][]" required maxlength="200"
                   placeholder="Tulis pilihan jawaban baru..."
                   class="flex-1 h-[38px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            <button type="button" onclick="removeOptionFromCard(this)"
                    class="px-3 h-[38px] rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">
                Hapus
            </button>
        `;
        list.appendChild(row);
        row.querySelector('input')?.focus();
    }

    function removeOptionFromCard(button) {
        const row = button.closest('.option-row');
        const list = row?.parentElement;
        if (!list) return;

        if (list.querySelectorAll('.option-row').length <= 1) {
            showToast('Minimal harus tersisa satu pilihan jawaban.', 'info');
            return;
        }
        row.remove();
    }
</script>
@endpush
@endsection
