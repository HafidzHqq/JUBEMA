@extends('layouts.app')

@section('title', 'Tautan Sekolah · Ruang Guru BK')

@section('content')
@php
    $fields = [
        ['name' => 'google_sheet_url', 'key' => 'gsheet', 'num' => '01',
         'label' => 'Google Spreadsheet Guru BK', 'hint' => 'Tempat mengimpor hasil ekspor CSV rekap aktivitas siswa.'],
        ['name' => 'google_drive_url', 'key' => 'gdrive', 'num' => '02',
         'label' => 'Folder Google Drive Materi', 'hint' => 'Repositori modul bacaan manajemen waktu dan arsip dokumen RPL.'],
    ];
@endphp

<section class="pb-10 border-b border-ink">
    <p class="text-sm text-ink-faint mb-3">Ruang Guru BK · Pengaturan Tautan</p>
    <h1 class="font-serif text-4xl sm:text-5xl font-medium tracking-tight leading-[1.06] text-ink">
        Tautan <span class="marker italic">Google Workspace.</span>
    </h1>
    <p class="mt-4 text-base text-ink-soft max-w-2xl leading-relaxed">
        Atur tautan Google Spreadsheet rekap kelas dan folder materi Google Drive dari satu halaman.
        Formulir konsultasi siswa kini sudah terintegrasi langsung di web JUBEMA.
    </p>
</section>

<div class="mt-12 grid lg:grid-cols-12 gap-12 items-start">
    <div class="lg:col-span-4 space-y-4">
        <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Tautan aktif saat ini</h2>
        <p class="text-sm text-ink-soft leading-relaxed">
            Setiap perubahan yang disimpan langsung memperbarui tombol tautan di halaman siswa dan
            Ruang Guru BK.
        </p>

        <dl class="pt-4 border-t border-rule divide-y divide-rule text-xs">
            @foreach ($fields as $field)
                @php $url = $workspaceLinks[$field['key']] ?? '#'; @endphp
                <div class="py-3.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <dt class="font-semibold text-ink">{{ $field['label'] }}</dt>
                        <dd class="text-ink-faint truncate font-mono mt-0.5">{{ $url }}</dd>
                    </div>
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="shrink-0 font-semibold text-accent hover:underline">
                        Buka ↗
                    </a>
                </div>
            @endforeach
        </dl>
    </div>

    <div class="lg:col-span-7 lg:col-start-6">
        @if ($errors->any())
            <div class="mb-6 border-l-2 border-red-600 pl-4 py-1 text-red-800 text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <section class="bg-sheet border border-rule">
            <div class="px-6 sm:px-8 py-5 border-b border-ink">
                <h2 class="font-serif text-2xl font-medium tracking-tight text-ink">Perbarui alamat tautan</h2>
                <p class="text-xs text-ink-faint mt-1">Masukkan tautan Google Spreadsheet dan folder Drive yang aktif.</p>
            </div>

            <form method="POST" action="{{ route('admin.links.save') }}" class="p-6 sm:p-8 space-y-6">
                @csrf @method('PUT')

                @foreach ($fields as $field)
                    @php $currentUrl = $workspaceLinks[$field['key']] ?? ''; @endphp
                    <div>
                        <label for="{{ $field['name'] }}" class="flex items-baseline gap-2 text-xs font-semibold text-ink mb-2">
                            <span class="font-serif text-base text-accent">{{ $field['num'] }}</span>
                            <span>{{ $field['label'] }}</span>
                        </label>
                        <input type="url" id="{{ $field['name'] }}" name="{{ $field['name'] }}" required maxlength="500"
                               value="{{ old($field['name'], $currentUrl) }}"
                               placeholder="https://..."
                               class="w-full h-[42px] px-3.5 bg-paper/60 border border-rule rounded-md text-xs font-mono text-ink placeholder:text-ink-faint focus:bg-sheet focus:border-ink transition">
                        <p class="text-xs text-ink-faint mt-1.5">{{ $field['hint'] }}</p>
                    </div>
                @endforeach

                <div class="pt-4 border-t border-rule flex justify-end">
                    <button type="submit"
                            class="px-6 py-3 bg-accent hover:bg-accent-dark text-sheet font-semibold rounded-md text-sm transition-colors">
                        Simpan Pengaturan Tautan
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
