@extends('layouts.app')

@section('title', 'Konsultasi Masuk · Ruang Guru BK')

@section('content')
@php
    $pending = $consultations->where('status', '!=', 'sudah_dijadwalkan');
    $scheduled = $consultations->where('status', 'sudah_dijadwalkan');
@endphp

<section class="grid lg:grid-cols-12 gap-8 pb-10 border-b border-ink items-end">
    <div class="lg:col-span-8">
        <p class="text-sm text-ink-faint mb-3">Ruang Guru BK · Layanan Konseling</p>
        <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
            Respon formulir <span class="marker italic">konsultasi siswa.</span>
        </h1>
        <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
            Seluruh formulir konsultasi yang diisi siswa melalui web JUBEMA langsung masuk ke sini.
            Tandai saat jadwal pendampingan sudah ditetapkan.
        </p>
    </div>
    <div class="lg:col-span-4 lg:text-right">
        <a href="{{ route('admin.konsultasi.form.edit') }}"
           class="inline-block px-5 py-3 rounded-md bg-ink hover:bg-black text-sheet text-sm font-semibold transition-colors">
            Edit Pertanyaan Formulir →
        </a>
    </div>
</section>

<section class="bg-sheet border-b border-x border-rule">
    <dl class="grid grid-cols-3 divide-x divide-rule">
        <div class="p-6 sm:p-8">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Total Masuk</dt>
            <dd class="mt-2.5 font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $consultations->count() }}</dd>
        </div>
        <div class="p-6 sm:p-8">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Menunggu Jadwal</dt>
            <dd class="mt-2.5 font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $pending->count() }}</dd>
        </div>
        <div class="p-6 sm:p-8">
            <dt class="text-xs font-medium text-ink-faint uppercase tracking-[0.12em]">Sudah Dijadwalkan</dt>
            <dd class="mt-2.5 font-serif text-4xl sm:text-5xl text-accent leading-none">{{ $scheduled->count() }}</dd>
        </div>
    </dl>
</section>

<section class="mt-12 bg-sheet border border-rule">
    <div class="px-6 sm:px-8 py-5 border-b border-ink flex items-baseline justify-between">
        <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Daftar formulir konsultasi</h2>
        <span class="text-xs text-ink-faint">{{ $consultations->count() }} respons</span>
    </div>

    <div class="divide-y divide-rule">
        @forelse ($consultations as $item)
            @php $isScheduled = $item->status === 'sudah_dijadwalkan'; @endphp
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

                    <h3 class="font-serif text-xl sm:text-2xl font-medium text-ink leading-snug">{{ $item->topic }}</h3>

                    <dl class="space-y-1.5 text-sm">
                        @if ($item->difficulty)
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-4 text-xs text-ink-faint pt-0.5">Kesulitan utama</dt>
                                <dd class="sm:col-span-8 text-ink">{{ $item->difficulty }}</dd>
                            </div>
                        @endif
                        @if ($item->bring_journal)
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-4 text-xs text-ink-faint pt-0.5">Catatan jurnal</dt>
                                <dd class="sm:col-span-8 text-ink-soft">{{ $item->bring_journal }}</dd>
                            </div>
                        @endif
                        <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                            <dt class="sm:col-span-4 text-xs text-ink-faint pt-0.5">Waktu pilihan</dt>
                            <dd class="sm:col-span-8 text-ink-soft">{{ $item->mode }}</dd>
                        </div>
                        @if ($item->consultation_method)
                            <div class="grid sm:grid-cols-12 gap-1 sm:gap-2">
                                <dt class="sm:col-span-4 text-xs text-ink-faint pt-0.5">Cara pendampingan</dt>
                                <dd class="sm:col-span-8 text-ink-soft">{{ $item->consultation_method }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if (filled($item->message))
                        <p class="text-sm text-ink-soft bg-paper/55 border-l-2 border-rule pl-4 py-2.5 leading-relaxed whitespace-pre-line">{{ $item->message }}</p>
                    @endif

                    @if ($item->scheduled_at)
                        <p class="text-xs text-accent font-semibold pt-1">
                            ✓ Dijadwalkan pada {{ $item->scheduled_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    @endif
                </div>

                <div class="lg:col-span-3 lg:text-right">
                    <form method="POST" action="{{ route('admin.konsultasi.mark', $item) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2.5 rounded-md text-xs font-semibold transition-colors {{ $isScheduled ? 'border border-rule bg-paper text-ink-soft hover:text-ink hover:border-ink' : 'bg-accent hover:bg-accent-dark text-sheet' }}">
                            {{ $isScheduled ? 'Batalkan Jadwal' : 'Tandai Sudah Dijadwalkan' }}
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
@endsection
