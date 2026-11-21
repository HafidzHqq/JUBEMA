@extends('layouts.app')

@section('title', 'Upload Materi · Ruang Guru BK')

@section('content')
<section class="pb-7 sm:pb-10 border-b border-ink">
    <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Ruang Guru BK · Manajemen Materi Belajar</p>
    <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
        Upload &amp; kelola <span class="marker italic">materi belajar.</span>
    </h1>
    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-2xl leading-relaxed">
        Unggah modul bacaan berformat PDF untuk siswa. Seluruh materi yang diunggah otomatis tampil
        dan dapat dibaca langsung di halaman Materi siswa tanpa perlu keluar dari web JUBEMA.
    </p>
</section>

<div class="mt-7 sm:mt-10 grid lg:grid-cols-12 gap-6 sm:gap-8 items-stretch">

    {{-- Kolom Kiri (5 kolom): Formulir Upload File PDF --}}
    <section class="lg:col-span-5 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink">
            <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Upload materi baru</h2>
            <p class="text-xs text-ink-faint mt-1">Pilih berkas modul PDF dari perangkat Anda.</p>
        </div>

        <form id="upload-material-form" method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data"
              class="p-6 sm:p-8 flex-1 flex flex-col justify-between gap-6">
            <div class="space-y-4">
                @csrf
                <input type="hidden" name="source_type" value="file">
                <input type="hidden" id="material-file-base64" name="material_file_base64" value="">
                <input type="hidden" id="material-file-name" name="material_file_name" value="">

                @if ($errors->any())
                    <div class="border-l-2 border-red-600 pl-3.5 text-red-800 text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Kotak Dropzone File PDF --}}
                <div>
                    <label for="material-file" class="block text-xs font-semibold text-ink mb-1.5">
                        File Modul (PDF) <span class="text-red-600">*</span>
                    </label>

                    <label for="material-file" id="pdf-dropzone"
                           class="block border-2 border-dashed border-rule hover:border-ink bg-paper/45 rounded-md p-5 text-center cursor-pointer transition-colors">
                        <input type="file" id="material-file" name="material_file" accept=".pdf,application/pdf" required
                               onchange="handlePdfSelected(this, 'material-title', 'selected-file-info', 'material-file-base64', 'material-file-name')"
                               class="sr-only">

                        <div id="dropzone-default" class="space-y-1.5">
                            <div class="w-10 h-10 rounded-full bg-sheet border border-rule mx-auto flex items-center justify-center text-accent">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-ink">Klik untuk memilih file PDF</p>
                            <p class="text-[11px] text-ink-faint">Format .PDF · Maksimal 10 MB</p>
                        </div>

                        <div id="selected-file-info" class="hidden items-center justify-between gap-3 text-left bg-sheet border border-rule px-3.5 py-2.5 rounded">
                            <div class="min-w-0">
                                <p id="selected-file-title" class="text-xs font-semibold text-ink truncate"></p>
                                <p id="selected-file-size" class="text-[11px] text-accent font-medium mt-0.5"></p>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-paper border border-rule text-[11px] font-semibold text-ink shrink-0">
                                Ganti File
                            </span>
                        </div>
                    </label>
                </div>

                <div>
                    <label for="material-title" class="block text-xs font-semibold text-ink mb-1.5">
                        Judul Materi <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="material-title" name="title" required maxlength="150"
                           value="{{ old('title') }}"
                           placeholder="Contoh: Tiga Siklus Belajar Mandiri (Zimmerman)"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div>
                    <label for="material-meta" class="block text-xs font-semibold text-ink mb-1.5">
                        Label / Durasi Baca <span class="font-normal text-ink-faint">(opsional)</span>
                    </label>
                    <input type="text" id="material-meta" name="meta" maxlength="100"
                           value="{{ old('meta') }}"
                           placeholder="Contoh: Bacaan 5 menit · Regulasi Diri"
                           class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                </div>

                <div>
                    <label for="material-summary" class="block text-xs font-semibold text-ink mb-1.5">
                        Ringkasan Singkat <span class="font-normal text-ink-faint">(opsional)</span>
                    </label>
                    <textarea id="material-summary" name="summary" rows="3" maxlength="500"
                              placeholder="Gambaran singkat isi modul agar siswa tahu apa yang akan dipelajari..."
                              class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">{{ old('summary') }}</textarea>
                </div>
            </div>

            <button type="submit" id="upload-submit-btn"
                    class="w-full py-3.5 px-5 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                Upload &amp; Publikasikan Materi
            </button>
        </form>
    </section>

    {{-- Kolom Kanan (7 kolom): Daftar Materi Aktif --}}
    <section class="lg:col-span-7 bg-sheet border border-rule flex flex-col">
        <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between gap-4">
            <div>
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar materi aktif</h2>
                <p class="text-xs text-ink-faint mt-1">Materi yang langsung dapat dibaca siswa di halaman Materi Belajar.</p>
            </div>
            <span class="text-xs font-medium text-ink-faint shrink-0">{{ $materials->count() }} materi</span>
        </div>

        @if ($materials->isEmpty())
            <div class="flex-1 p-12 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-paper border border-rule flex items-center justify-center text-accent mb-4" aria-hidden="true">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>
                    </svg>
                </div>
                <p class="font-serif text-2xl text-ink">Belum ada materi belajar</p>
                <p class="text-sm text-ink-faint mt-1.5 max-w-sm leading-relaxed">
                    Unggah file PDF modul pertama melalui formulir di sebelah kiri.
                </p>
            </div>
        @else
            <div class="divide-y divide-rule flex-1">
                @foreach ($materials as $index => $mat)
                    @php
                        $matPayload = [
                            'id' => $mat->id,
                            'num' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                            'title' => $mat->title,
                            'meta' => $mat->meta ?? '',
                            'summary' => $mat->summary ?? '',
                            'file_name' => $mat->source_type === 'file' ? ($mat->file_name ?: 'Modul PDF') : 'Dokumen Modul',
                            'preview_url' => $mat->previewUrl(),
                            'open_url' => $mat->openUrl(),
                            'update_url' => route('admin.materials.update', $mat),
                        ];
                    @endphp
                    <article class="p-5 sm:p-8 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-2 min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-serif text-lg font-semibold text-accent leading-none">
                                    {{ $matPayload['num'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-accent/10 text-accent">
                                    {{ $mat->source_type === 'file' ? 'Modul PDF' : 'Modul Bacaan' }}
                                </span>
                                @if (filled($mat->meta))
                                    <span class="text-ink-faint">· {{ $mat->meta }}</span>
                                @endif
                            </div>

                            <h3 class="font-serif text-xl sm:text-2xl font-medium text-ink leading-snug">
                                {{ $mat->title }}
                            </h3>

                            @if (filled($mat->summary))
                                <p class="text-xs sm:text-sm text-ink-soft leading-relaxed">
                                    {{ $mat->summary }}
                                </p>
                            @endif

                            @if ($mat->source_type === 'file' && filled($mat->file_name))
                                <p class="text-xs text-ink-faint truncate">
                                    Berkas: <span class="font-medium text-ink-soft">{{ $mat->file_name }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 sm:flex sm:flex-wrap items-center gap-2 pt-3 sm:pt-0 border-t sm:border-t-0 border-rule/70 shrink-0">
                            <button type="button"
                                    data-material="{{ json_encode($matPayload) }}"
                                    onclick="openPreviewModal(JSON.parse(this.dataset.material))"
                                    class="px-3.5 py-2 sm:py-1.5 rounded-md border border-rule bg-paper/60 hover:border-ink text-xs font-semibold text-ink text-center transition-colors">
                                Lihat
                            </button>

                            <button type="button"
                                    data-material="{{ json_encode($matPayload) }}"
                                    onclick="openEditMaterialModal(JSON.parse(this.dataset.material))"
                                    class="px-3.5 py-2 sm:py-1.5 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-medium text-ink text-center transition-colors">
                                Edit
                            </button>

                            <form method="POST" action="{{ route('admin.materials.destroy', $mat) }}"
                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus materi "'.$mat->title.'" dari halaman siswa?') }});">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full px-3.5 py-2 sm:py-1.5 rounded-md border border-rule bg-paper hover:border-red-300 hover:bg-red-50 text-xs font-medium text-red-700 text-center transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

</div>

{{-- Modal Pratinjau Modul langsung di Halaman Web --}}
<div id="preview-material-modal"
     class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-ink/55 backdrop-blur-[1px]"
     role="dialog" aria-modal="true" aria-labelledby="preview-modal-title">
    <div class="w-full max-w-4xl bg-sheet border-t sm:border border-rule rounded-t-2xl sm:rounded-none shadow-xl overflow-hidden flex flex-col h-[88dvh] sm:h-[85vh]">
        <div class="px-5 sm:px-6 py-4 border-b border-ink flex items-center justify-between gap-4 bg-paper/45 shrink-0">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-accent">Pratinjau Modul</p>
                <h2 id="preview-modal-title" class="font-serif text-lg sm:text-xl font-medium text-ink truncate"></h2>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="preview-modal-open" href="#" target="_blank" rel="noopener noreferrer"
                   class="px-3 py-1.5 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-medium text-ink transition-colors">
                    Tab Baru ↗
                </a>
                <button type="button" onclick="closePreviewModal()" aria-label="Tutup pratinjau"
                        class="w-9 h-9 rounded-md border border-rule bg-sheet text-ink flex items-center justify-center hover:bg-paper transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>
        </div>
        <iframe id="preview-modal-iframe" title="Pratinjau Materi" src="about:blank" class="w-full flex-1 border-0 bg-white"></iframe>
    </div>
</div>

{{-- Modal Edit Materi --}}
<div id="edit-material-modal"
     class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-ink/55 backdrop-blur-[1px]"
     role="dialog" aria-modal="true" aria-labelledby="edit-material-title-heading">
    <div class="w-full max-w-lg bg-sheet border-t sm:border border-rule rounded-t-2xl sm:rounded-none shadow-xl overflow-hidden max-h-[92dvh] overflow-y-auto">
        <div class="w-10 h-1 bg-rule rounded-full mx-auto mt-3 sm:hidden" aria-hidden="true"></div>
        <div class="px-6 py-5 border-b border-ink flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-accent">Edit Materi Belajar</p>
                <h2 id="edit-material-title-heading" class="font-serif text-2xl font-medium text-ink mt-1">Perbarui detail materi</h2>
            </div>
            <button type="button" onclick="closeEditMaterialModal()" aria-label="Tutup"
                    class="w-9 h-9 rounded-md border border-rule bg-paper/60 text-ink flex items-center justify-center hover:bg-paper transition-colors shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>

        <form id="edit-material-form" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-file-base64" name="material_file_base64" value="">
            <input type="hidden" id="edit-file-name" name="material_file_name" value="">

            <div>
                <label for="edit-title" class="block text-xs font-semibold text-ink mb-1.5">Judul Materi <span class="text-red-600">*</span></label>
                <input type="text" id="edit-title" name="title" required maxlength="150"
                       class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>

            <div>
                <label for="edit-meta" class="block text-xs font-semibold text-ink mb-1.5">Label / Durasi Baca <span class="font-normal text-ink-faint">(opsional)</span></label>
                <input type="text" id="edit-meta" name="meta" maxlength="100"
                       class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition">
            </div>

            <div>
                <label for="edit-summary" class="block text-xs font-semibold text-ink mb-1.5">Ringkasan Singkat <span class="font-normal text-ink-faint">(opsional)</span></label>
                <textarea id="edit-summary" name="summary" rows="3" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-paper/60 border border-rule rounded-md text-sm text-ink focus:bg-sheet focus:border-ink transition"></textarea>
            </div>

            <div>
                <label for="edit-file" class="block text-xs font-semibold text-ink mb-1.5">
                    Ganti File PDF <span class="font-normal text-ink-faint">(kosongkan jika tidak diganti)</span>
                </label>
                <input type="file" id="edit-file" name="material_file" accept=".pdf,application/pdf"
                       onchange="handlePdfSelected(this, null, null, 'edit-file-base64', 'edit-file-name')"
                       class="w-full px-3 py-2 bg-paper/60 border border-dashed border-rule rounded-md text-xs text-ink file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-ink file:text-sheet file:text-xs file:font-semibold hover:file:bg-black cursor-pointer">
            </div>

            <div class="pt-3 border-t border-rule flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditMaterialModal()"
                        class="px-4 py-2.5 rounded-md border border-rule bg-paper/60 hover:border-ink text-xs sm:text-sm font-medium text-ink transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-accent hover:bg-accent-dark text-sheet text-xs sm:text-sm font-semibold transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function handlePdfSelected(input, titleInputId, infoBoxId, base64InputId, nameInputId) {
        const file = input.files?.[0];
        const base64El = base64InputId ? document.getElementById(base64InputId) : null;
        const nameEl = nameInputId ? document.getElementById(nameInputId) : null;
        if (base64El) base64El.value = '';
        if (nameEl) nameEl.value = '';

        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            showToast('Ukuran file PDF maksimal 10 MB.', 'error');
            input.value = '';
            return;
        }

        if (infoBoxId) {
            const defaultBox = document.getElementById('dropzone-default');
            const infoBox = document.getElementById(infoBoxId);
            const nameLabel = document.getElementById('selected-file-title');
            const sizeLabel = document.getElementById('selected-file-size');

            defaultBox?.classList.add('hidden');
            infoBox?.classList.remove('hidden');
            infoBox?.classList.add('flex');
            if (nameLabel) nameLabel.textContent = file.name;
            if (sizeLabel) sizeLabel.textContent = `Siap diunggah · ${formatBytes(file.size)}`;
        }

        if (titleInputId) {
            const titleEl = document.getElementById(titleInputId);
            if (titleEl && !titleEl.value.trim()) {
                titleEl.value = file.name
                    .replace(/\.pdf$/i, '')
                    .replace(/[_-]+/g, ' ')
                    .trim();
            }
        }

        // Cadangan otomatis: bila file > 1.8 MB (melebihi upload_max_filesize 2M bawaan PHP),
        // kirimkan isi PDF lewat field base64 dan kosongkan input file saat submit.
        if (file.size > 1800 * 1024 && base64El && nameEl) {
            const reader = new FileReader();
            reader.onload = () => {
                base64El.value = String(reader.result || '');
                nameEl.value = file.name;
                input.removeAttribute('name');
                input.required = false;
            };
            reader.readAsDataURL(file);
        } else {
            input.setAttribute('name', 'material_file');
        }
    }

    const previewModal = document.getElementById('preview-material-modal');
    const previewIframe = document.getElementById('preview-modal-iframe');

    function openPreviewModal(data) {
        if (!previewModal || !previewIframe) return;
        document.getElementById('preview-modal-title').textContent = `${data.num} · ${data.title}`;
        document.getElementById('preview-modal-open').href = data.open_url;
        previewIframe.src = data.preview_url;
        previewModal.classList.remove('hidden');
        previewModal.classList.add('flex');
    }

    function closePreviewModal() {
        if (!previewModal) return;
        previewModal.classList.add('hidden');
        previewModal.classList.remove('flex');
        if (previewIframe) previewIframe.src = 'about:blank';
    }

    const editModal = document.getElementById('edit-material-modal');
    const editForm = document.getElementById('edit-material-form');

    function openEditMaterialModal(data) {
        if (!editModal || !editForm) return;
        editForm.action = data.update_url;
        document.getElementById('edit-title').value = data.title || '';
        document.getElementById('edit-meta').value = data.meta || '';
        document.getElementById('edit-summary').value = data.summary || '';
        document.getElementById('edit-file').value = '';
        document.getElementById('edit-file-base64').value = '';
        document.getElementById('edit-file-name').value = '';
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
        document.getElementById('edit-title')?.focus();
    }

    function closeEditMaterialModal() {
        if (!editModal) return;
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
    }

    previewModal?.addEventListener('click', (e) => {
        if (e.target === previewModal) closePreviewModal();
    });
    editModal?.addEventListener('click', (e) => {
        if (e.target === editModal) closeEditMaterialModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (previewModal && !previewModal.classList.contains('hidden')) closePreviewModal();
            if (editModal && !editModal.classList.contains('hidden')) closeEditMaterialModal();
        }
    });
</script>
@endpush
@endsection
