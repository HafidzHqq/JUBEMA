<article class="rpl-sheet mt-7 bg-white border border-neutral-300 rounded-lg shadow-sm overflow-hidden text-sm text-neutral-800 leading-relaxed print:mt-0 print:border-0 print:rounded-none print:shadow-none">

    {{-- Kop & Judul Dokumen --}}
    <header class="px-6 sm:px-10 lg:px-14 pt-8 sm:pt-11 pb-6 border-b-2 border-black text-center bg-white print:px-0 print:pt-0">
        <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-700">
            Universitas Islam Negeri Raden Intan Lampung
        </p>
        <p class="text-[11px] sm:text-xs uppercase tracking-[0.1em] text-neutral-600 mt-0.5">
            Fakultas Tarbiyah dan Keguruan · Bimbingan dan Konseling Pendidikan Islam
        </p>

        <div class="w-16 h-0.5 bg-black mx-auto my-4"></div>

        <h2 class="font-serif text-2xl sm:text-3xl font-semibold text-black tracking-tight">
            Rencana Pelaksanaan Layanan (RPL)
        </h2>
        <p class="mt-1 text-xs sm:text-sm font-medium text-neutral-700">
            Bimbingan Klasikal Bidang Belajar (Akademik)
        </p>
    </header>

    <div class="px-6 sm:px-10 lg:px-14 py-8 sm:py-10 space-y-9 print:px-0 print:py-6 print:space-y-6">

        {{-- Identitas Layanan --}}
        <section>
            <div class="flex items-center justify-between gap-4 mb-3.5">
                <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-neutral-600">Identitas Layanan</h3>
                <span class="text-xs font-medium text-neutral-700">Alokasi 2 × 40 Menit · Fase D</span>
            </div>

            <div class="border border-neutral-300 rounded-md overflow-hidden print:rounded-none print:border-black/40">
                <dl class="divide-y divide-neutral-300 text-sm">
                    @php
                        $identitas = [
                            ['Satuan Pendidikan', 'SMP Negeri / Swasta'],
                            ['Kelas / Semester', 'VII / VIII / IX (Fase D)'],
                            ['Komponen Layanan', 'Layanan Dasar'],
                            ['Bidang Layanan', 'Belajar / Akademik'],
                            ['Topik Layanan', 'Manajemen Waktu Belajar Mandiri melalui Jurnal Digital (JUBEMA)'],
                            ['Fungsi Layanan', 'Pemahaman, pengembangan, pencegahan'],
                            ['Sasaran', 'Seluruh peserta didik kelas VII–IX'],
                            ['Alokasi Waktu', '2 × 40 menit (satu pertemuan)'],
                            ['Metode', 'Diskusi terbimbing, studi kasus, demonstrasi, refleksi, latihan perencanaan'],
                            ['Media', 'Presentasi, papan tulis, lembar kerja, kartu kasus, website JUBEMA, versi cetak PDF'],
                            ['Dasar Kebutuhan', 'Hasil asesmen kebutuhan peserta didik (angket kebutuhan) dan/atau observasi guru BK, diverifikasi sebelum pelaksanaan'],
                        ];
                    @endphp
                    @foreach ($identitas as [$label, $value])
                        <div class="grid sm:grid-cols-12 print:grid-cols-12">
                            <dt class="sm:col-span-4 print:col-span-4 px-4 py-2.5 bg-neutral-50 font-semibold text-black sm:border-r border-neutral-300 print:border-r">
                                {{ $label }}
                            </dt>
                            <dd class="sm:col-span-8 print:col-span-8 px-4 py-2.5 text-neutral-800 {{ $label === 'Topik Layanan' ? 'font-medium text-black' : '' }}">
                                {{ $value }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- A. Rasional dan Tujuan --}}
        <section class="space-y-4">
            <h3 class="font-serif text-xl font-semibold text-black pb-2 border-b border-neutral-300 flex items-baseline gap-2.5">
                <span>A.</span>
                <span>Rasional dan Tujuan</span>
            </h3>

            <p class="text-justify leading-relaxed text-neutral-800">
                Peserta didik kelas VII–IX berada pada masa remaja awal dan menghadapi peralihan tuntutan belajar:
                jumlah mata pelajaran bertambah, guru berganti-ganti, dan tugas dari banyak mata pelajaran sering
                menumpuk. Pada usia ini kemandirian belajar mulai tumbuh tetapi belum stabil, sehingga peserta didik
                masih memerlukan contoh konkret, struktur sederhana, dan umpan balik yang cepat. Layanan ini membantu
                mereka menyusun rencana belajar mingguan, memecah tugas, memantau pelaksanaan, dan merefleksikan
                hasilnya dengan bantuan media Jurnal Digital Manajemen Waktu Belajar (JUBEMA). Materi menggunakan
                prinsip manajemen waktu, regulasi diri, penetapan tujuan, dan efikasi diri
                <em>(Britton &amp; Tesser, 1991; Zimmerman, 2002; Locke &amp; Latham, 2002; Bandura, 1997)</em>,
                serta memperhatikan faktor penyebab penundaan tugas <em>(Steel, 2007)</em>.
            </p>

            <div class="bg-neutral-50 border-l-2 border-black rounded-r-md px-4 py-3.5 print:bg-white">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-black mb-1">Tujuan Umum</p>
                <p class="text-neutral-900 leading-relaxed">
                    Peserta didik mampu menyusun dan menerapkan rencana belajar mingguan yang realistis dengan bantuan
                    JUBEMA, memantau kemajuannya, serta menentukan langkah perbaikan ketika menghadapi hambatan dalam
                    mengelola waktu belajar.
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-neutral-600 mb-2.5">Tujuan Khusus</p>
                <ol class="space-y-2">
                    @php
                        $tujuanKhusus = [
                            'Mengidentifikasi sedikitnya dua kekuatan dan dua hambatan dalam mengelola waktu belajar.',
                            'Menjelaskan tiga strategi manajemen waktu: menentukan prioritas, memecah tugas, dan mengurangi gangguan.',
                            'Menyusun rencana belajar satu minggu dengan tujuan mingguan dan tugas beserta tenggatnya pada JUBEMA.',
                            'Mempraktikkan pemecahan satu tugas besar menjadi langkah-langkah kecil dan respons adaptif ketika menunda tugas.',
                            'Menentukan indikator pemantauan (ceklis harian dan refleksi mingguan) serta sumber bantuan, termasuk permintaan konsultasi kepada guru BK.',
                        ];
                    @endphp
                    @foreach ($tujuanKhusus as $index => $item)
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-neutral-100 border border-neutral-300 text-black text-xs font-semibold flex items-center justify-center shrink-0 mt-0.5 print:border-black/40">
                                {{ $index + 1 }}
                            </span>
                            <span class="leading-relaxed text-neutral-800">{{ $item }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- B. Materi Pokok --}}
        <section class="space-y-4">
            <h3 class="font-serif text-xl font-semibold text-black pb-2 border-b border-neutral-300 flex items-baseline gap-2.5">
                <span>B.</span>
                <span>Materi Pokok</span>
            </h3>

            @php
                $materiPokok = [
                    ['Pengenalan pola belajar', 'Tujuan, kebiasaan, gangguan perhatian, dan sumber dukungan.'],
                    ['Strategi manajemen waktu', 'Menetapkan tujuan dan prioritas, memecah tugas besar menjadi langkah kecil, menggunakan jadwal dan ceklis, serta mengurangi gangguan saat belajar.'],
                    ['Regulasi diri', 'Merencanakan (perencana mingguan), melaksanakan dan memantau (ceklis harian), serta merefleksikan hasil (refleksi mingguan) melalui JUBEMA.'],
                    ['Menghadapi tantangan', 'Mengenali penyebab menunda tugas yang dapat diperbaiki, mengganti strategi, meminta bantuan, dan menghindari pelabelan diri.'],
                    ['Pengenalan dan penggunaan JUBEMA', 'Panduan singkat, perencana mingguan, pemecah tugas, ceklis harian, refleksi mingguan, tautan permintaan konsultasi, serta privasi data (catatan tersimpan aman dan dibagikan hanya atas pilihan peserta didik).'],
                    ['Rencana aksi tujuh hari', 'Prioritas tugas, jadwal, indikator keberhasilan, dan refleksi.'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 gap-3 print:grid-cols-2">
                @foreach ($materiPokok as [$judul, $isi])
                    <div class="p-4 rounded-md bg-neutral-50/70 border border-neutral-300 space-y-1 print:bg-white print:border-black/30 print:break-inside-avoid">
                        <p class="font-semibold text-black text-sm">{{ $judul }}</p>
                        <p class="text-xs sm:text-[13px] text-neutral-700 leading-relaxed">{{ $isi }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- C. Langkah Pelaksanaan --}}
        <section class="space-y-4">
            <div class="pb-2 border-b border-neutral-300 flex flex-wrap items-baseline justify-between gap-2">
                <h3 class="font-serif text-xl font-semibold text-black flex items-baseline gap-2.5">
                    <span>C.</span>
                    <span>Langkah Pelaksanaan</span>
                </h3>
                <span class="text-xs text-neutral-600">Total 80 Menit (2 × 40 Menit)</span>
            </div>

            <div class="overflow-x-auto border border-neutral-300 rounded-md print:rounded-none print:border-black/40">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="bg-neutral-100 border-b border-neutral-300 text-left text-xs uppercase tracking-[0.08em] text-black">
                        <tr>
                            <th class="py-3 px-4 font-semibold w-36">Tahap</th>
                            <th class="py-3 px-4 font-semibold w-28">Waktu</th>
                            <th class="py-3 px-4 font-semibold">Uraian Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-300">
                        <tr class="align-top print:break-inside-avoid">
                            <td class="py-4 px-4 font-semibold text-black bg-neutral-50">Pendahuluan</td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded bg-neutral-100 border border-neutral-300 text-xs font-medium text-black">10 Menit</span>
                            </td>
                            <td class="py-4 px-4 text-neutral-800">
                                <ul class="list-disc pl-4 space-y-1.5">
                                    <li>Guru BK menyapa, menyampaikan tujuan, kesepakatan kelas, dan kerahasiaan berbagi pengalaman.</li>
                                    <li>Peserta didik menilai kebiasaan mengatur waktu belajar secara singkat dengan skala 1 sampai 4.</li>
                                </ul>
                            </td>
                        </tr>

                        <tr class="align-top print:break-inside-avoid">
                            <td class="py-4 px-4 font-semibold text-black bg-neutral-50">Kegiatan Inti</td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded bg-black text-white text-xs font-semibold print:bg-white print:border print:border-black print:text-black">60 Menit</span>
                            </td>
                            <td class="py-4 px-4 space-y-3 text-neutral-800">
                                <div class="space-y-0.5">
                                    <p class="font-semibold text-black">1. Eksplorasi <span class="font-normal text-xs text-neutral-600">(10 menit)</span></p>
                                    <p>Peserta didik mencatat kekuatan, hambatan, dan situasi belajar yang menantang dalam mengatur waktu.</p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="font-semibold text-black">2. Pemaparan dan Demonstrasi <span class="font-normal text-xs text-neutral-600">(15 menit)</span></p>
                                    <p>Guru BK memperagakan penggunaan JUBEMA (panduan singkat, perencana mingguan, pemecah tugas, dan ceklis harian), serta penetapan tujuan belajar mingguan.</p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="font-semibold text-black">3. Diskusi Kasus <span class="font-normal text-xs text-neutral-600">(15 menit)</span></p>
                                    <p>Kelompok menganalisis peserta didik yang sering menunda tugas dan tugasnya menumpuk, mengidentifikasi faktor yang dapat dikendalikan, dan menyarankan strategi.</p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="font-semibold text-black">4. Praktik <span class="font-normal text-xs text-neutral-600">(20 menit)</span></p>
                                    <p>Setiap peserta didik mengisi perencana mingguan pada JUBEMA (atau lembar cetak PDF bagi yang tidak memiliki perangkat atau kuota), memecah satu tugas menjadi langkah kecil, dan menentukan indikator pemantauan.</p>
                                </div>
                            </td>
                        </tr>

                        <tr class="align-top print:break-inside-avoid">
                            <td class="py-4 px-4 font-semibold text-black bg-neutral-50">Penutup</td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded bg-neutral-100 border border-neutral-300 text-xs font-medium text-black">10 Menit</span>
                            </td>
                            <td class="py-4 px-4 text-neutral-800">
                                <ul class="list-disc pl-4 space-y-1.5">
                                    <li>Dua atau tiga peserta didik menyampaikan rencana belajar pilihannya secara sukarela.</li>
                                    <li>Guru BK memberi penguatan, melakukan tiket keluar, dan menyepakati pemantauan setelah satu minggu melalui refleksi mingguan pada JUBEMA.</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- D. Evaluasi dan Tindak Lanjut --}}
        <section class="space-y-4">
            <h3 class="font-serif text-xl font-semibold text-black pb-2 border-b border-neutral-300 flex items-baseline gap-2.5">
                <span>D.</span>
                <span>Evaluasi dan Tindak Lanjut</span>
            </h3>

            <div class="grid md:grid-cols-3 gap-4 print:grid-cols-3">
                <div class="p-4 rounded-md border border-neutral-300 bg-neutral-50/70 space-y-1.5 print:bg-white print:border-black/30 print:break-inside-avoid">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-black">Evaluasi Proses</p>
                    <p class="text-xs sm:text-[13px] text-neutral-700 leading-relaxed">
                        Guru BK mencatat keterlibatan, ketepatan waktu, partisipasi diskusi, serta kemampuan
                        menyelesaikan lembar kerja dan menggunakan fitur utama JUBEMA.
                    </p>
                </div>

                <div class="p-4 rounded-md border border-neutral-300 bg-neutral-50/70 space-y-1.5 print:bg-white print:border-black/30 print:break-inside-avoid">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-black">Evaluasi Hasil</p>
                    <p class="text-xs sm:text-[13px] text-neutral-700 leading-relaxed">
                        Cek pemahaman melalui tiket keluar dan kualitas rencana belajar. Kriteria operasional:
                        minimal tiga dari empat komponen rencana terisi jelas (tujuan mingguan, tugas &amp; tenggat,
                        langkah pemecahan tugas, indikator/refleksi). Bukan instrumen psikometrik.
                    </p>
                </div>

                <div class="p-4 rounded-md border border-neutral-300 bg-neutral-50/70 space-y-1.5 print:bg-white print:border-black/30 print:break-inside-avoid">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-black">Tindak Lanjut</p>
                    <p class="text-xs sm:text-[13px] text-neutral-700 leading-relaxed">
                        Guru BK meninjau catatan penerapan tujuh hari yang dibagikan secara sukarela, menawarkan
                        bimbingan kelompok atau konseling bagi siswa yang membutuhkan, serta berkolaborasi dengan
                        wali kelas dan guru mapel sesuai prinsip kerahasiaan.
                    </p>
                </div>
            </div>
        </section>

        {{-- E. Pengesahan --}}
        <section class="pt-4 space-y-5 print:break-inside-avoid">
            <h3 class="font-serif text-xl font-semibold text-black pb-2 border-b border-neutral-300 flex items-baseline gap-2.5">
                <span>E.</span>
                <span>Pengesahan</span>
            </h3>

            <p class="text-xs sm:text-sm text-black text-right">
                Bandar Lampung, .............................. {{ date('Y') }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 print:grid-cols-3 border border-neutral-300 rounded-md divide-y sm:divide-y-0 sm:divide-x print:divide-y-0 print:divide-x divide-neutral-300 print:rounded-none print:border-black/40 text-xs text-black">
                <div class="p-5 flex flex-col justify-between min-h-[156px]">
                    <p class="font-semibold">Mengetahui,<br>Kepala Sekolah</p>
                    <div class="space-y-1 pt-12">
                        <p class="border-b border-dotted border-neutral-500 pb-0.5">Nama: ....................................</p>
                        <p class="text-neutral-600">NIP: .......................................</p>
                    </div>
                </div>

                <div class="p-5 flex flex-col justify-between min-h-[156px]">
                    <p class="font-semibold">Mengetahui,<br>Koordinator BK</p>
                    <div class="space-y-1 pt-12">
                        <p class="border-b border-dotted border-neutral-500 pb-0.5">Nama: ....................................</p>
                        <p class="text-neutral-600">NIP: .......................................</p>
                    </div>
                </div>

                <div class="p-5 flex flex-col justify-between min-h-[156px]">
                    <p class="font-semibold">Pelaksana,<br>Guru BK</p>
                    <div class="space-y-1 pt-12">
                        <p class="font-semibold border-b border-dotted border-neutral-500 pb-0.5">{{ config('app.jubema.admin_name') }}</p>
                        <p class="text-neutral-600">NIP: .......................................</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
</article>
