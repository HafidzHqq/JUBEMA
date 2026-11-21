@extends('layouts.app')

@section('title', 'Materi Belajar')

@section('content')
@php
    $modules = ($materials ?? collect())->values()->map(function ($mat, $idx) {
        return [
            'id' => 'modul-'.$mat->id,
            'num' => str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
            'meta' => $mat->meta ?: $mat->sourceLabel(),
            'source_label' => $mat->sourceLabel(),
            'title' => $mat->title,
            'summary' => $mat->summary ?: 'Klik kartu ini untuk membaca isi materi secara langsung di halaman JUBEMA.',
            'preview_url' => $mat->previewUrl(),
            'external_url' => $mat->openUrl(),
        ];
    })->all();
@endphp

<section class="pb-7 sm:pb-10 border-b border-ink">
    <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">Repositori Bacaan · Layanan BK SMP</p>
    <h1 class="font-serif text-[2rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
        Materi <span class="marker italic">manajemen waktu.</span>
    </h1>
    <p class="mt-3 sm:mt-4 text-sm sm:text-base text-ink-soft max-w-2xl leading-relaxed">
        Pilih modul di bawah ini untuk membacanya langsung di halaman JUBEMA tanpa perlu berpindah tab.
    </p>
</section>

@if (count($modules) === 0)
    <section class="mt-10 bg-sheet border border-rule p-14 text-center">
        <p class="font-serif text-2xl text-ink">Belum ada materi belajar yang tersedia</p>
        <p class="text-sm text-ink-faint mt-1.5">Materi akan langsung muncul di sini begitu diunggah oleh Guru BK.</p>
    </section>
@else
    {{-- Pilihan Kartu Modul --}}
    <div class="mt-10 grid md:grid-cols-2 gap-6 items-stretch">
        @foreach ($modules as $idx => $mod)
            <article data-module-card="{{ $mod['id'] }}"
                     onclick="selectModule('{{ $mod['id'] }}', true)"
                     role="button"
                     tabindex="0"
                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); selectModule('{{ $mod['id'] }}', true); }"
                     class="module-card cursor-pointer bg-sheet border p-6 sm:p-7 flex flex-col justify-between space-y-5 transition-colors {{ $idx === 0 ? 'border-ink ring-1 ring-ink' : 'border-rule hover:border-ink/50' }}">
                <div class="space-y-3.5">
                    <div class="flex items-baseline justify-between pb-3.5 border-b border-ink gap-3">
                        <div class="flex items-baseline gap-2.5">
                            <span class="font-serif text-4xl text-accent leading-none">{{ $mod['num'] }}</span>
                            <span data-module-badge="{{ $mod['id'] }}"
                                  class="px-2 py-0.5 rounded bg-ink text-sheet text-[11px] font-semibold {{ $idx === 0 ? '' : 'hidden' }}">
                                Sedang Dibaca
                            </span>
                        </div>
                        <span class="text-xs font-medium text-ink-faint text-right">{{ $mod['meta'] }}</span>
                    </div>
                    <h2 class="font-serif text-2xl font-medium text-ink tracking-tight leading-snug">
                        {{ $mod['title'] }}
                    </h2>
                    <p class="text-sm text-ink-soft leading-relaxed">
                        {{ $mod['summary'] }}
                    </p>
                </div>
                <div class="pt-4 border-t border-rule flex items-center justify-between text-xs">
                    <span class="text-ink-faint">{{ $mod['source_label'] }}</span>
                    <span data-module-cta="{{ $mod['id'] }}"
                          class="font-semibold {{ $idx === 0 ? 'text-accent' : 'text-ink underline underline-offset-4 decoration-rule' }}">
                        {{ $idx === 0 ? 'Aktif di pembaca ↓' : 'Baca Modul '.$mod['num'].' ↓' }}
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    {{-- Pembaca Modul Tertanam di Halaman Web --}}
    <section id="module-reader" class="mt-8 bg-sheet border border-rule">
        <div class="px-6 sm:px-8 py-4 border-b border-ink bg-paper/45 flex flex-wrap items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-ink-faint">Pembaca Modul JUBEMA</p>
                <h2 id="reader-title" class="font-serif text-xl sm:text-2xl font-medium text-ink truncate mt-0.5">
                    {{ $modules[0]['num'] }} · {{ $modules[0]['title'] }}
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" id="reader-fullscreen-btn" onclick="toggleReaderFullscreen()"
                        class="px-3.5 py-1.5 rounded-md border border-rule bg-sheet hover:border-ink text-xs font-medium text-ink transition-colors">
                    Layar Penuh
                </button>
            </div>
        </div>

        {{-- Wadah Iframe Dokumen Modul --}}
        <div id="reader-frame-wrap" class="relative bg-white">
            <iframe id="module-iframe"
                    title="Pembaca Modul Materi JUBEMA"
                    src="{{ $modules[0]['preview_url'] }}"
                    class="w-full h-[64vh] min-h-[420px] sm:h-[78vh] sm:min-h-[620px] border-0 bg-white block"
                    allow="fullscreen"></iframe>
        </div>

        <div class="px-6 sm:px-8 py-3 border-t border-rule bg-paper/35 flex flex-wrap items-center justify-between gap-2 text-xs text-ink-faint">
            <span>Dokumen ditampilkan langsung di halaman web JUBEMA.</span>
            <a id="reader-fallback-link" href="{{ $modules[0]['external_url'] }}" target="_blank" rel="noopener noreferrer"
               class="font-medium text-ink-soft hover:text-ink underline underline-offset-4">
                Buka berkas di tab baru ↗
            </a>
        </div>
    </section>

    @push('scripts')
    <script>
        const JUBEMA_MODULES = @json($modules);
        let activeModuleId = JUBEMA_MODULES[0]?.id || '';

        function selectModule(moduleId, shouldScroll = false) {
            const mod = JUBEMA_MODULES.find((item) => item.id === moduleId);
            if (!mod) return;

            const changed = activeModuleId !== mod.id;
            activeModuleId = mod.id;

            const iframe = document.getElementById('module-iframe');
            const titleEl = document.getElementById('reader-title');
            const fallbackLink = document.getElementById('reader-fallback-link');

            if (titleEl) titleEl.textContent = `${mod.num} · ${mod.title}`;
            if (fallbackLink) fallbackLink.href = mod.external_url;

            if (iframe && (changed || iframe.src !== mod.preview_url)) {
                iframe.src = mod.preview_url;
            }

            JUBEMA_MODULES.forEach((item) => {
                const isActive = item.id === mod.id;
                const card = document.querySelector(`[data-module-card="${item.id}"]`);
                const badge = document.querySelector(`[data-module-badge="${item.id}"]`);
                const cta = document.querySelector(`[data-module-cta="${item.id}"]`);

                if (card) {
                    card.classList.toggle('border-ink', isActive);
                    card.classList.toggle('ring-1', isActive);
                    card.classList.toggle('ring-ink', isActive);
                    card.classList.toggle('border-rule', !isActive);
                }
                if (badge) badge.classList.toggle('hidden', !isActive);
                if (cta) {
                    cta.textContent = isActive ? 'Aktif di pembaca ↓' : `Baca Modul ${item.num} ↓`;
                    cta.classList.toggle('text-accent', isActive);
                    cta.classList.toggle('text-ink', !isActive);
                    cta.classList.toggle('underline', !isActive);
                }
            });

            if (shouldScroll) {
                document.getElementById('module-reader')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function toggleReaderFullscreen() {
            const reader = document.getElementById('module-reader');
            if (!reader) return;
            if (!document.fullscreenElement) {
                reader.requestFullscreen?.().catch(() => {});
            } else {
                document.exitFullscreen?.().catch(() => {});
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const iframe = document.getElementById('module-iframe');
            const loading = document.getElementById('reader-loading');
            iframe?.addEventListener('load', () => {
                if (!loading) return;
                loading.classList.add('opacity-0');
                setTimeout(() => loading.classList.add('hidden'), 200);
            });

            const fsBtn = document.getElementById('reader-fullscreen-btn');
            document.addEventListener('fullscreenchange', () => {
                if (fsBtn) {
                    fsBtn.textContent = document.fullscreenElement ? 'Keluar Layar Penuh' : 'Layar Penuh';
                }
            });
        });
    </script>
    @endpush
@endif
@endsection
