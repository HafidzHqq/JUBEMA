<article class="rpl-sheet mt-8 bg-white border border-neutral-300 shadow-sm text-black print:mt-0 print:border-0 print:shadow-none"
         style="font-family: 'Times New Roman', Times, Georgia, serif;">

    <div class="px-4 sm:px-12 lg:px-16 py-6 sm:py-14 space-y-5 sm:space-y-6 text-[13.5px] sm:text-[15px] leading-[1.6] sm:leading-[1.65] print:px-0 print:py-0">

        {{-- Kop Surat Resmi --}}
        <header class="text-center pb-3.5 sm:pb-4 border-b-[3px] border-double border-black">
            <p class="text-[13px] sm:text-base font-bold uppercase tracking-wide leading-snug">
                Universitas Islam Negeri Raden Intan Lampung
            </p>
            <p class="text-[12.5px] sm:text-[15px] font-bold uppercase tracking-wide leading-snug">
                Fakultas Tarbiyah dan Keguruan
            </p>
            <p class="text-[12.5px] sm:text-[15px] font-bold uppercase tracking-wide leading-snug">
                Bimbingan dan Konseling Pendidikan Islam
            </p>
        </header>

        {{-- Judul Dokumen --}}
        <div class="text-center pt-1 pb-1.5 sm:pb-2">
            <h2 class="text-[13.5px] sm:text-base font-bold uppercase leading-snug">
                Rencana Pelaksanaan Layanan (RPL)
            </h2>
            <p class="text-[12.5px] sm:text-[15px] font-bold uppercase leading-snug mt-0.5">
                Bimbingan Klasikal Bidang Belajar (Akademik)
            </p>
        </div>

        {{-- Tabel Identitas Layanan --}}
        <div>
            <table class="w-full border-collapse border border-black text-[13px] sm:text-[15px] leading-[1.5]">
                <tbody>
                    @php
                        $identitas = [
                            ['Satuan Pendidikan', 'SMP Negeri / Swasta'],
                            ['Kelas / Semester', 'VII / VIII / IX'],
                            ['Komponen Layanan', 'Layanan dasar'],
                            ['Bidang Layanan', 'Belajar / akademik'],
                            ['Topik', 'Manajemen Waktu Belajar Mandiri melalui Jurnal Digital (JUBEMA)'],
                            ['Fungsi Layanan', 'Pemahaman, pengembangan, pencegahan'],
                            ['Sasaran', 'Seluruh peserta didik kelas VII–IX'],
                            ['Alokasi Waktu', '2 × 40 menit, satu pertemuan'],
                            ['Metode', 'Diskusi terbimbing, studi kasus, demonstrasi, refleksi, latihan perencanaan'],
                            ['Media', 'Presentasi, papan tulis, lembar kerja, kartu kasus, website JUBEMA, versi cetak PDF'],
                            ['Dasar Kebutuhan', 'Hasil asesmen kebutuhan peserta didik (angket kebutuhan) dan/atau observasi guru BK, diverifikasi sebelum pelaksanaan'],
                        ];
                    @endphp
                    @foreach ($identitas as [$label, $value])
                        <tr class="align-top">
                            <td class="border border-black px-3 py-1.5 w-[32%] font-normal">{{ $label }}</td>
                            <td class="border border-black px-3 py-1.5">{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- A. Rasional dan Tujuan --}}
        <section class="space-y-3 pt-2">
            <h3 class="text-[15px] font-bold">A. Rasional dan Tujuan</h3>

            <p class="text-justify indent-8">
                Peserta didik kelas VII–IX berada pada masa remaja awal dan menghadapi peralihan tuntutan belajar:
                jumlah mata pelajaran bertambah, guru berganti-ganti, dan tugas dari banyak mata pelajaran sering
                menumpuk. Pada usia ini kemandirian belajar mulai tumbuh tetapi belum stabil, sehingga peserta didik
                masih memerlukan contoh konkret, struktur sederhana, dan umpan balik yang cepat. Layanan ini membantu
                mereka menyusun rencana belajar mingguan, memecah tugas, memantau pelaksanaan, dan merefleksikan
                hasilnya dengan bantuan media Jurnal Digital Manajemen Waktu Belajar (JUBEMA). Materi menggunakan
                prinsip manajemen waktu, regulasi diri, penetapan tujuan, dan efikasi diri (Britton &amp; Tesser, 1991;
                Zimmerman, 2002; Locke &amp; Latham, 2002; Bandura, 1997), serta memperhatikan faktor penyebab
                penundaan tugas (Steel, 2007).
            </p>

            <p class="text-justify indent-8">
                <strong>Tujuan umum:</strong> peserta didik mampu menyusun dan menerapkan rencana belajar mingguan yang
                realistis dengan bantuan JUBEMA, memantau kemajuannya, serta menentukan langkah perbaikan ketika
                menghadapi hambatan dalam mengelola waktu belajar.
            </p>

            <div class="space-y-1.5">
                <p class="text-justify indent-8"><strong>Tujuan khusus:</strong></p>
                <ol class="list-decimal pl-10 space-y-1 text-justify">
                    <li>Mengidentifikasi sedikitnya dua kekuatan dan dua hambatan dalam mengelola waktu belajar.</li>
                    <li>Menjelaskan tiga strategi manajemen waktu (menentukan prioritas, memecah tugas, dan mengurangi gangguan).</li>
                    <li>Menyusun rencana belajar satu minggu dengan tujuan mingguan dan tugas beserta tenggatnya pada JUBEMA.</li>
                    <li>Mempraktikkan pemecahan satu tugas besar menjadi langkah-langkah kecil dan respons adaptif ketika menunda tugas.</li>
                    <li>Menentukan indikator pemantauan (ceklis harian dan refleksi mingguan) serta sumber bantuan, termasuk permintaan konsultasi kepada guru BK.</li>
                </ol>
            </div>
        </section>

        {{-- B. Materi Pokok --}}
        <section class="space-y-2.5 pt-1">
            <h3 class="text-[15px] font-bold">B. Materi Pokok</h3>

            <ul class="list-disc pl-8 space-y-1.5 text-justify">
                <li>
                    <strong>Pengenalan pola belajar:</strong> tujuan, kebiasaan, gangguan perhatian, dan sumber dukungan.
                </li>
                <li>
                    <strong>Strategi manajemen waktu:</strong> menetapkan tujuan dan prioritas, memecah tugas besar menjadi
                    langkah kecil, menggunakan jadwal dan ceklis, serta mengurangi gangguan saat belajar.
                </li>
                <li>
                    <strong>Regulasi diri:</strong> merencanakan (perencana mingguan), melaksanakan dan memantau (ceklis
                    harian), serta merefleksikan hasil (refleksi mingguan) melalui JUBEMA.
                </li>
                <li>
                    <strong>Menghadapi tantangan:</strong> mengenali penyebab menunda tugas yang dapat diperbaiki, mengganti
                    strategi, meminta bantuan, dan menghindari pelabelan diri.
                </li>
                <li>
                    <strong>Pengenalan dan penggunaan JUBEMA:</strong> panduan singkat, perencana mingguan, pemecah tugas,
                    ceklis harian, refleksi mingguan, tautan permintaan konsultasi, serta privasi data (catatan tersimpan
                    aman dan dibagikan hanya atas pilihan peserta didik).
                </li>
                <li>
                    <strong>Rencana aksi tujuh hari:</strong> prioritas tugas, jadwal, indikator keberhasilan, dan refleksi.
                </li>
            </ul>
        </section>

        {{-- C. Langkah Pelaksanaan --}}
        <section class="space-y-3 pt-1">
            <h3 class="text-[14px] sm:text-[15px] font-bold">C. Langkah Pelaksanaan</h3>

            <div>
                <table class="w-full border-collapse border border-black text-[13px] sm:text-[15px] leading-[1.5]">
                    <thead>
                        <tr class="text-left">
                            <th class="border border-black px-2.5 sm:px-3 py-2 font-bold w-[25%] sm:w-[22%]">Tahap</th>
                            <th class="border border-black px-2 sm:px-3 py-2 font-bold w-[18%] sm:w-[16%] text-center">Waktu</th>
                            <th class="border border-black px-2.5 sm:px-3 py-2 font-bold">Uraian Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="align-top print:break-inside-avoid">
                            <td class="border border-black px-3 py-2.5 font-bold">Pendahuluan</td>
                            <td class="border border-black px-3 py-2.5 text-center whitespace-nowrap">10 menit</td>
                            <td class="border border-black px-3 py-2.5">
                                <ul class="list-disc pl-5 space-y-1 text-justify">
                                    <li>Guru BK menyapa, menyampaikan tujuan, kesepakatan kelas, dan kerahasiaan berbagi pengalaman.</li>
                                    <li>Peserta didik menilai kebiasaan mengatur waktu belajar secara singkat dengan skala 1 sampai 4.</li>
                                </ul>
                            </td>
                        </tr>

                        <tr class="align-top print:break-inside-avoid">
                            <td class="border border-black px-3 py-2.5 font-bold">Kegiatan Inti</td>
                            <td class="border border-black px-3 py-2.5 text-center whitespace-nowrap">60 menit</td>
                            <td class="border border-black px-3 py-2.5">
                                <ul class="list-disc pl-5 space-y-1.5 text-justify">
                                    <li>
                                        <strong>Eksplorasi (10 menit):</strong> peserta didik mencatat kekuatan, hambatan, dan situasi
                                        belajar yang menantang dalam mengatur waktu.
                                    </li>
                                    <li>
                                        <strong>Pemaparan dan demonstrasi (15 menit):</strong> guru BK memperagakan penggunaan JUBEMA
                                        (panduan singkat, perencana mingguan, pemecah tugas, dan ceklis harian), serta penetapan tujuan
                                        belajar mingguan.
                                    </li>
                                    <li>
                                        <strong>Diskusi kasus (15 menit):</strong> kelompok menganalisis peserta didik yang sering menunda
                                        tugas dan tugasnya menumpuk, mengidentifikasi faktor yang dapat dikendalikan, dan menyarankan strategi.
                                    </li>
                                    <li>
                                        <strong>Praktik (20 menit):</strong> setiap peserta didik mengisi perencana mingguan pada JUBEMA
                                        (atau lembar cetak PDF bagi yang tidak memiliki perangkat atau kuota), memecah satu tugas menjadi
                                        langkah kecil, dan menentukan indikator pemantauan.
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        <tr class="align-top print:break-inside-avoid">
                            <td class="border border-black px-3 py-2.5 font-bold">Penutup</td>
                            <td class="border border-black px-3 py-2.5 text-center whitespace-nowrap">10 menit</td>
                            <td class="border border-black px-3 py-2.5">
                                <ul class="list-disc pl-5 space-y-1 text-justify">
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
        <section class="space-y-3 pt-1">
            <h3 class="text-[15px] font-bold">D. Evaluasi dan Tindak Lanjut</h3>

            <p class="text-justify indent-8">
                <strong>Evaluasi proses:</strong> guru BK mencatat keterlibatan, ketepatan waktu, partisipasi diskusi,
                serta kemampuan menyelesaikan lembar kerja dan menggunakan fitur utama JUBEMA.
            </p>

            <p class="text-justify indent-8">
                <strong>Evaluasi hasil:</strong> cek pemahaman melalui tiket keluar dan kualitas rencana belajar.
                Kriteria operasional yang disarankan: minimal tiga dari empat komponen rencana terisi dengan jelas
                (tujuan mingguan, tugas dan tenggat, langkah pemecahan tugas, indikator/refleksi). Kriteria ini
                merupakan alat evaluasi layanan, bukan instrumen psikometrik.
            </p>

            <p class="text-justify indent-8">
                <strong>Tindak lanjut:</strong> guru BK meninjau catatan penerapan tujuh hari yang dibagikan peserta
                didik secara sukarela, menawarkan bimbingan kelompok atau konseling bagi peserta didik yang memerlukan
                dukungan tambahan (termasuk yang mengajukan konsultasi melalui JUBEMA), serta berkolaborasi dengan wali
                kelas dan guru mata pelajaran sesuai kebutuhan dan prinsip kerahasiaan.
            </p>
        </section>

        {{-- E. Pengesahan --}}
        <section class="space-y-4 pt-2 print:break-inside-avoid">
            <h3 class="text-[15px] font-bold">E. Pengesahan</h3>

            <p class="text-right">
                Bandar Lampung, ............................ {{ date('Y') }}
            </p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[540px] border-collapse border border-black text-[14px]">
                    <thead>
                        <tr class="align-top text-left">
                            <th class="border border-black px-3 py-2 font-normal w-1/3">Mengetahui Kepala Sekolah</th>
                            <th class="border border-black px-3 py-2 font-normal w-1/3">Koordinator BK</th>
                            <th class="border border-black px-3 py-2 font-normal w-1/3">Guru BK Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="align-bottom">
                            <td class="border border-black px-3 pt-20 pb-3">
                                <p>Nama: ..................................</p>
                                <p class="mt-0.5">NIP: .....................................</p>
                            </td>
                            <td class="border border-black px-3 pt-20 pb-3">
                                <p>Nama: ..................................</p>
                                <p class="mt-0.5">NIP: .....................................</p>
                            </td>
                            <td class="border border-black px-3 pt-20 pb-3">
                                <p>Nama: {{ config('app.jubema.admin_name') }}</p>
                                <p class="mt-0.5">NIP: .....................................</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</article>
