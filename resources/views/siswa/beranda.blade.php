@extends('layouts.app')

@section('title', 'Beranda Jurnal')

@section('content')
{{-- Pembuka Beranda: Grid 12 kolom selaras dengan landing page --}}
<section class="grid xl:grid-cols-12 gap-6 xl:gap-10 pb-7 sm:pb-10 border-b border-ink items-end">
    <div class="xl:col-span-7">
        <p class="text-xs sm:text-sm text-ink-faint mb-2 sm:mb-3">{{ $student->kelas }} · Jurnal Belajar Mandiri</p>

        <h1 class="font-serif text-[2.1rem] sm:text-5xl font-medium tracking-tight leading-[1.08] sm:leading-[1.06] text-ink">
            Halo, <span class="marker italic">{{ $student->name }}.</span>
        </h1>

        <p class="mt-3.5 sm:mt-5 text-[15px] sm:text-lg text-ink-soft leading-relaxed max-w-xl">
            Setiap langkah kecil yang kamu rencanakan hari ini membantu mengurangi rasa kewalahan saat
            tugas menumpuk. Mulai dari satu tugas yang paling dekat tenggatnya.
        </p>

        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 sm:gap-x-7 sm:gap-y-4">
            <a href="{{ route('siswa.perencana') }}"
               class="px-6 py-3.5 rounded-md bg-accent hover:bg-accent-dark text-sheet text-sm font-semibold text-center transition-colors">
                Tambah Rencana Tugas
            </a>
            <a href="{{ route('siswa.ceklis') }}"
               class="py-2 sm:py-0 text-sm font-medium text-ink text-center sm:text-left underline underline-offset-[6px] decoration-rule hover:decoration-ink">
                Buka ceklis hari ini →
            </a>
        </div>
    </div>

    <div class="xl:col-span-4 xl:col-start-9 bg-sheet border border-rule p-5 sm:p-7">
        <p class="text-xs font-medium uppercase tracking-[0.12em] text-ink-faint">Kemajuan Minggu Ini</p>
        <div class="mt-2.5 sm:mt-3 flex items-baseline gap-2.5">
            <span class="font-serif text-4xl sm:text-5xl text-ink leading-none">{{ $doneTasks }}</span>
            <span class="text-sm text-ink-soft">dari {{ $totalTasks }} tugas selesai</span>
        </div>
        <div class="w-full bg-paper h-1.5 mt-4 sm:mt-5 overflow-hidden">
            <div class="bg-accent h-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
        </div>
        <p class="mt-2.5 sm:mt-3 text-xs text-ink-faint">{{ $progressPercent }}% rencana mingguan tercapai</p>
    </div>
</section>

{{-- Lembar Jurnal: Siklus 4 langkah di atas permukaan bg-sheet --}}
<section class="mt-7 sm:mt-10 bg-sheet border border-rule p-5 sm:p-9 xl:p-11">
    <div class="grid xl:grid-cols-12 gap-4 sm:gap-6 mb-6 sm:mb-9">
        <h2 class="xl:col-span-5 font-serif text-2xl sm:text-3xl font-medium tracking-tight text-ink">
            Siklus belajar mingguanmu
        </h2>
        <p class="xl:col-span-6 xl:col-start-7 text-sm text-ink-soft leading-relaxed">
            Mengikuti siklus belajar mandiri Zimmerman (2002): merencanakan tugas, mengerjakan dengan
            fokus singkat, lalu melihat ke belakang sebentar di akhir minggu.
        </p>
    </div>

    <ol class="grid sm:grid-cols-2 xl:grid-cols-4 border-t border-ink">
        <li class="pt-6 pb-5 sm:pr-7 sm:border-r border-rule flex flex-col justify-between">
            <div>
                <span class="font-serif text-5xl text-accent leading-none">1</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Rencanakan</h3>
                <p class="mt-2 text-sm text-ink-soft leading-relaxed">
                    Tulis tugasnya, pilih harinya, lalu pecah jadi tiga langkah kecil yang bisa langsung dimulai.
                </p>
            </div>
            <a href="{{ route('siswa.perencana') }}"
               class="mt-6 inline-block text-sm font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Buka perencana →
            </a>
        </li>

        <li class="pt-6 pb-5 sm:pl-7 xl:px-7 xl:border-r border-rule border-t sm:border-t-0 flex flex-col justify-between">
            <div>
                <span class="font-serif text-5xl text-accent leading-none">2</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Kerjakan</h3>
                <p class="mt-2 text-sm text-ink-soft leading-relaxed">
                    Nyalakan timer fokus 25 menit dan centang setiap langkah kecil yang berhasil diselesaikan.
                </p>
            </div>
            <a href="{{ route('siswa.ceklis') }}"
               class="mt-6 inline-block text-sm font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Buka ceklis →
            </a>
        </li>

        <li class="pt-6 pb-5 sm:pr-7 xl:px-7 sm:border-r border-rule border-t xl:border-t-0 flex flex-col justify-between">
            <div>
                <span class="font-serif text-5xl text-accent leading-none">3</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Renungkan</h3>
                <p class="mt-2 text-sm text-ink-soft leading-relaxed">
                    Di akhir minggu, catat apa yang berjalan baik, apa yang mengganggu, dan rencana minggu depan.
                </p>
            </div>
            <a href="{{ route('siswa.refleksi') }}"
               class="mt-6 inline-block text-sm font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Tulis refleksi →
            </a>
        </li>

        <li class="pt-6 pb-5 sm:pl-7 border-t xl:border-t-0 border-rule flex flex-col justify-between">
            <div>
                <span class="font-serif text-5xl text-accent leading-none">4</span>
                <h3 class="mt-5 text-lg font-semibold text-ink">Ngobrol dengan Guru BK</h3>
                <p class="mt-2 text-sm text-ink-soft leading-relaxed">
                    Kalau tugas tetap terasa berat atau bingung mulai dari mana, kirim permintaan konsultasi.
                </p>
            </div>
            <a href="{{ route('siswa.konsultasi') }}"
               class="mt-6 inline-block text-sm font-semibold text-ink underline underline-offset-4 decoration-rule hover:decoration-ink">
                Hubungi Guru BK →
            </a>
        </li>
    </ol>
</section>
@endsection
