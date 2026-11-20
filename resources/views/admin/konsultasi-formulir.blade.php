@extends('layouts.app')

@section('title', 'Edit Formulir Konsultasi · Ruang Guru BK')

@section('content')
@php
    $q1Options = old('q1_options', $formConfig['q1_options']);
    $q3Options = old('q3_options', $formConfig['q3_options']);
    $q4Options = old('q4_options', $formConfig['q4_options']);
    $q5Options = old('q5_options', $formConfig['q5_options']);
    $q6Options = old('q6_options', $formConfig['q6_options']);
@endphp

<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <a href="{{ route('admin.konsultasi-masuk') }}" class="inline-block text-sm text-ink-soft hover:text-ink mb-3 transition-colors">
            ← Kembali ke Konsultasi Masuk
        </a>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Edit pertanyaan <span class="marker italic">formulir konsultasi.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
            Ubah teks pengantar, pertanyaan, maupun daftar pilihan jawaban yang tampil di halaman
            Konsultasi Siswa. Perubahan langsung aktif setelah disimpan tanpa mengubah respons lama.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <form method="POST" action="{{ route('admin.konsultasi.form.reset') }}"
              onsubmit="return confirm('Kembalikan seluruh teks dan pilihan formulir ke bawaan awal?');">
            @csrf
            <button type="submit"
                    class="px-4 py-2.5 rounded-md border border-rule bg-sheet hover:border-ink text-xs sm:text-sm font-medium text-ink-soft hover:text-ink transition-colors">
                Kembalikan ke Bawaan
            </button>
        </form>
    </div>
</section>

@if ($errors->any())
    <div class="mt-8 border-l-2 border-red-600 pl-4 py-1 text-red-800 text-xs space-y-1">
        @foreach ($errors->all() as $err)
            <p>{{ $err }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.konsultasi.form.update') }}" class="mt-10 space-y-8 max-w-4xl">
    @csrf
    @method('PUT')

    {{-- Pengantar Formulir --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-4">
        <div class="pb-3 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium text-ink">Pengantar formulir</h2>
            <p class="text-xs text-ink-faint mt-0.5">Paragraf pembuka yang dibaca siswa di bagian atas formulir.</p>
        </div>
        <div>
            <label for="intro" class="block text-xs font-semibold text-ink mb-1.5">Teks Pengantar</label>
            <textarea id="intro" name="intro" rows="3" required maxlength="1000"
                      class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">{{ old('intro', $formConfig['intro']) }}</textarea>
        </div>
    </div>

    {{-- Pertanyaan 1: Kesulitan Utama --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 1 · Pilihan Ganda</h2>
            <span class="text-xs text-ink-faint">Kesulitan pengelolaan waktu</span>
        </div>

        <div>
            <label for="q1_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q1_label" name="q1_label" required maxlength="250"
                   value="{{ old('q1_label', $formConfig['q1_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                <button type="button" onclick="addOptionRow('q1-options-list', 'q1_options[]', 200)"
                        class="text-xs font-semibold text-accent hover:underline">
                    + Tambah pilihan
                </button>
            </div>
            <div id="q1-options-list" class="space-y-2">
                @foreach ($q1Options as $opt)
                    <div class="option-row flex items-center gap-2">
                        <input type="text" name="q1_options[]" required maxlength="200" value="{{ $opt }}"
                               class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionRow(this)"
                                class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors"
                                title="Hapus pilihan">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>

        <label class="flex items-center gap-2.5 text-sm text-ink-soft pt-2 cursor-pointer">
            <input type="checkbox" name="q1_allow_other" value="1"
                   @checked(old('q1_allow_other', $formConfig['q1_allow_other']))
                   class="rounded border-rule text-accent focus:ring-accent">
            <span>Aktifkan opsi tambahan <strong>"Yang lain:"</strong> (siswa bisa menulis jawaban sendiri)</span>
        </label>
    </div>

    {{-- Pertanyaan 2: Skala 1-4 --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 2 · Skala 1–4</h2>
            <span class="text-xs text-ink-faint">Tingkat fokus / kendali waktu</span>
        </div>

        <div>
            <label for="q2_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q2_label" name="q2_label" required maxlength="250"
                   value="{{ old('q2_label', $formConfig['q2_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="q2_min_label" class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 1 (Terendah)</label>
                <input type="text" id="q2_min_label" name="q2_min_label" required maxlength="100"
                       value="{{ old('q2_min_label', $formConfig['q2_min_label']) }}"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>
            <div>
                <label for="q2_max_label" class="block text-xs font-semibold text-ink mb-1.5">Keterangan Skala 4 (Tertinggi)</label>
                <input type="text" id="q2_max_label" name="q2_max_label" required maxlength="100"
                       value="{{ old('q2_max_label', $formConfig['q2_max_label']) }}"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>
        </div>
    </div>

    {{-- Pertanyaan 3: Topik Utama --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 3 · Topik Diskusi</h2>
            <span class="text-xs text-ink-faint">Pilihan topik konsultasi</span>
        </div>

        <div>
            <label for="q3_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q3_label" name="q3_label" required maxlength="250"
                   value="{{ old('q3_label', $formConfig['q3_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                <button type="button" onclick="addOptionRow('q3-options-list', 'q3_options[]', 200)"
                        class="text-xs font-semibold text-accent hover:underline">
                    + Tambah pilihan
                </button>
            </div>
            <div id="q3-options-list" class="space-y-2">
                @foreach ($q3Options as $opt)
                    <div class="option-row flex items-center gap-2">
                        <input type="text" name="q3_options[]" required maxlength="200" value="{{ $opt }}"
                               class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionRow(this)"
                                class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pertanyaan 4: Membawa Catatan Jurnal --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 4 · Catatan Jurnal</h2>
            <span class="text-xs text-ink-faint">Kesiapan catatan belajar</span>
        </div>

        <div>
            <label for="q4_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q4_label" name="q4_label" required maxlength="250"
                   value="{{ old('q4_label', $formConfig['q4_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                <button type="button" onclick="addOptionRow('q4-options-list', 'q4_options[]', 120)"
                        class="text-xs font-semibold text-accent hover:underline">
                    + Tambah pilihan
                </button>
            </div>
            <div id="q4-options-list" class="space-y-2">
                @foreach ($q4Options as $opt)
                    <div class="option-row flex items-center gap-2">
                        <input type="text" name="q4_options[]" required maxlength="120" value="{{ $opt }}"
                               class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionRow(this)"
                                class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pertanyaan 5: Waktu Luang --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 5 · Preferensi Waktu</h2>
            <span class="text-xs text-ink-faint">Jadwal sesi konsultasi</span>
        </div>

        <div>
            <label for="q5_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q5_label" name="q5_label" required maxlength="250"
                   value="{{ old('q5_label', $formConfig['q5_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                <button type="button" onclick="addOptionRow('q5-options-list', 'q5_options[]', 120)"
                        class="text-xs font-semibold text-accent hover:underline">
                    + Tambah pilihan
                </button>
            </div>
            <div id="q5-options-list" class="space-y-2">
                @foreach ($q5Options as $opt)
                    <div class="option-row flex items-center gap-2">
                        <input type="text" name="q5_options[]" required maxlength="120" value="{{ $opt }}"
                               class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionRow(this)"
                                class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pertanyaan 6: Cara Pendampingan / Bentuk Pertemuan --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-5">
        <div class="pb-3 border-b border-ink flex items-baseline justify-between gap-4">
            <h2 class="font-serif text-2xl font-medium text-ink">Pertanyaan 6 · Cara Pendampingan</h2>
            <span class="text-xs text-ink-faint">Tatap muka, Google Meet, atau pesan</span>
        </div>

        <div>
            <label for="q6_label" class="block text-xs font-semibold text-ink mb-1.5">Teks Pertanyaan</label>
            <input type="text" id="q6_label" name="q6_label" required maxlength="250"
                   value="{{ old('q6_label', $formConfig['q6_label']) }}"
                   class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink">Pilihan Jawaban</span>
                <button type="button" onclick="addOptionRow('q6-options-list', 'q6_options[]', 120)"
                        class="text-xs font-semibold text-accent hover:underline">
                    + Tambah pilihan
                </button>
            </div>
            <div id="q6-options-list" class="space-y-2">
                @foreach ($q6Options as $opt)
                    <div class="option-row flex items-center gap-2">
                        <input type="text" name="q6_options[]" required maxlength="120" value="{{ $opt }}"
                               class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
                        <button type="button" onclick="removeOptionRow(this)"
                                class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Catatan Tambahan --}}
    <div class="bg-sheet border border-rule p-6 sm:p-8 space-y-4">
        <div class="pb-3 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium text-ink">Kolom catatan tambahan (opsional)</h2>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="note_label" class="block text-xs font-semibold text-ink mb-1.5">Judul Kolom</label>
                <input type="text" id="note_label" name="note_label" required maxlength="150"
                       value="{{ old('note_label', $formConfig['note_label']) }}"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>
            <div>
                <label for="note_placeholder" class="block text-xs font-semibold text-ink mb-1.5">Teks Petunjuk (Placeholder)</label>
                <input type="text" id="note_placeholder" name="note_placeholder" required maxlength="250"
                       value="{{ old('note_placeholder', $formConfig['note_placeholder']) }}"
                       class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
        <a href="{{ route('admin.konsultasi-masuk') }}" class="text-sm text-ink-soft hover:text-ink">
            Batal
        </a>
        <button type="submit"
                class="px-7 py-3.5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
            Simpan Perubahan Formulir
        </button>
    </div>
</form>

@push('scripts')
<script>
    function addOptionRow(containerId, inputName, maxLength) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'option-row flex items-center gap-2';
        row.innerHTML = `
            <input type="text" name="${inputName}" required maxlength="${maxLength}" placeholder="Tulis pilihan jawaban baru..."
                   class="flex-1 px-3.5 py-2 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
            <button type="button" onclick="removeOptionRow(this)"
                    class="px-3 py-2 rounded-md border border-rule text-xs text-ink-faint hover:text-red-700 hover:border-red-300 transition-colors">Hapus</button>
        `;
        container.appendChild(row);
        row.querySelector('input')?.focus();
    }

    function removeOptionRow(button) {
        const row = button.closest('.option-row');
        const container = row?.parentElement;
        if (!container) return;
        if (container.querySelectorAll('.option-row').length <= 1) {
            showToast('Minimal harus tersisa satu pilihan jawaban.', 'info');
            return;
        }
        row.remove();
    }
</script>
@endpush
@endsection
