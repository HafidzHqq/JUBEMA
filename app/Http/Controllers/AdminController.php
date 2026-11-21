<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Material;
use App\Models\Reflection;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Support\ConsultationFormConfig;
use App\Support\SimpleXlsxBuilder;
use App\Support\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /** Sub-menu dashboard admin. */
    public function dashboard(): View
    {
        return $this->render('dashboard');
    }

    public function konsultasiMasuk(): View
    {
        return $this->render('konsultasi-masuk');
    }

    /**
     * Halaman ubah teks pertanyaan & pilihan Formulir Konsultasi BK.
     */
    public function editConsultationForm(): View
    {
        return view('admin.konsultasi-formulir', [
            'formConfig' => ConsultationFormConfig::get(),
        ]);
    }

    public function updateConsultationForm(Request $request): RedirectResponse
    {
        $rawQuestions = $request->input('questions', []);
        $cleanedQuestions = [];

        if (is_array($rawQuestions)) {
            foreach (array_values($rawQuestions) as $q) {
                if (! is_array($q)) {
                    continue;
                }

                $type = trim((string) ($q['type'] ?? 'text'));
                $options = [];
                if (is_array($q['options'] ?? null)) {
                    $options = array_values(array_unique(array_filter(array_map(
                        static fn ($item) => trim((string) $item),
                        $q['options']
                    ), static fn (string $item) => $item !== '')));
                }

                $cleanedQuestions[] = [
                    'id' => trim((string) ($q['id'] ?? '')),
                    'label' => trim((string) ($q['label'] ?? '')),
                    'type' => $type,
                    'required' => ! empty($q['required']),
                    'options' => $options,
                    'allow_other' => $type === 'radio' && ! empty($q['allow_other']),
                    'min_label' => trim((string) ($q['min_label'] ?? '')),
                    'max_label' => trim((string) ($q['max_label'] ?? '')),
                    'placeholder' => trim((string) ($q['placeholder'] ?? '')),
                ];
            }
        }

        $request->merge(['questions' => $cleanedQuestions]);

        $allowedTypes = array_keys(ConsultationFormConfig::QUESTION_TYPES);

        $validated = $request->validate([
            'intro' => ['required', 'string', 'max:1000'],
            'questions' => ['required', 'array', 'min:1', 'max:25'],
            'questions.*.id' => ['nullable', 'string', 'max:60'],
            'questions.*.label' => ['required', 'string', 'max:250'],
            'questions.*.type' => ['required', Rule::in($allowedTypes)],
            'questions.*.required' => ['boolean'],
            'questions.*.options' => ['array', 'max:15'],
            'questions.*.options.*' => ['required', 'string', 'max:200'],
            'questions.*.allow_other' => ['boolean'],
            'questions.*.min_label' => ['nullable', 'string', 'max:100'],
            'questions.*.max_label' => ['nullable', 'string', 'max:100'],
            'questions.*.placeholder' => ['nullable', 'string', 'max:250'],
        ], [
            'intro.required' => 'Teks pengantar formulir wajib diisi.',
            'questions.required' => 'Formulir minimal harus memiliki 1 pertanyaan.',
            'questions.min' => 'Formulir minimal harus memiliki 1 pertanyaan.',
            'questions.*.label.required' => 'Semua teks pertanyaan wajib diisi.',
            'questions.*.type.in' => 'Jenis pertanyaan tidak valid.',
        ]);

        // Validasi tambahan khusus tipe Pilihan Ganda & Skala
        foreach ($validated['questions'] as $index => $q) {
            $num = $index + 1;
            if ($q['type'] === 'radio' && count($q['options'] ?? []) < 1) {
                return back()
                    ->withErrors(["questions.{$index}.options" => "Pertanyaan #{$num} (Pilihan Ganda) minimal harus memiliki 1 pilihan jawaban."])
                    ->withInput();
            }
            if ($q['type'] === 'scale' && (blank($q['min_label'] ?? null) || blank($q['max_label'] ?? null))) {
                return back()
                    ->withErrors(["questions.{$index}.min_label" => "Pertanyaan #{$num} (Skala 1–4) wajib memiliki keterangan skala terendah dan tertinggi."])
                    ->withInput();
            }
        }

        ConsultationFormConfig::save($validated);

        return back()->with('success', 'Formulir Konsultasi berhasil diperbarui!');
    }

    public function resetConsultationForm(): RedirectResponse
    {
        ConsultationFormConfig::reset();

        return back()->with('info', 'Formulir Konsultasi dikembalikan ke pertanyaan bawaan.');
    }

    public function rplAdmin(): View
    {
        return $this->render('rpl-admin');
    }

    public function integrasi(): View
    {
        $materials = Material::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.integrasi', [
            'materials' => $materials,
        ]);
    }

    /**
     * Unggah file PDF materi baru.
     */
    public function storeMaterial(Request $request): RedirectResponse
    {
        $sourceType = $request->input('source_type', 'file') === 'link' ? 'link' : 'file';
        $hasBase64 = filled($request->input('material_file_base64'));

        $rules = [
            'title' => ['required', 'string', 'min:2', 'max:150'],
            'meta' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
        ];

        if ($sourceType === 'file') {
            if (! $hasBase64) {
                $rules['material_file'] = ['required', 'file', 'mimes:pdf', 'max:10240'];
            }
        } else {
            $rules['external_url'] = ['required', 'url', 'max:500'];
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Judul materi wajib diisi.',
            'title.min' => 'Judul materi minimal 2 karakter.',
            'material_file.required' => 'Pilih file PDF materi yang ingin diunggah.',
            'material_file.uploaded' => 'Ukuran file PDF melebihi batas unggah server. Pilih file PDF di bawah 10 MB.',
            'material_file.mimes' => 'Format file materi harus berupa PDF (.pdf).',
            'material_file.max' => 'Ukuran file PDF maksimal 10 MB.',
            'external_url.required' => 'Alamat tautan materi wajib diisi.',
            'external_url.url' => 'Format tautan materi tidak valid.',
        ]);

        $filePath = null;
        $fileName = null;
        $externalUrl = null;

        if ($sourceType === 'file') {
            if ($request->hasFile('material_file') && $request->file('material_file')->isValid()) {
                $uploaded = $request->file('material_file');
                $fileName = $uploaded->getClientOriginalName();
                $filePath = $uploaded->store('materials', 'public');
            } elseif ($hasBase64) {
                [$filePath, $fileName] = $this->storeBase64Pdf(
                    (string) $request->input('material_file_base64'),
                    (string) $request->input('material_file_name', 'materi.pdf')
                );
                if ($filePath === null) {
                    return back()->withErrors(['material_file' => 'Berkas PDF tidak valid atau melebihi 10 MB.'])->withInput();
                }
            }
        } else {
            $externalUrl = trim((string) ($validated['external_url'] ?? ''));
        }

        $nextOrder = ((int) Material::max('sort_order')) + 1;

        Material::create([
            'title' => trim($validated['title']),
            'meta' => filled($validated['meta'] ?? null)
                ? trim((string) $validated['meta'])
                : 'Modul PDF · Layanan BK',
            'summary' => filled($validated['summary'] ?? null) ? trim((string) $validated['summary']) : null,
            'source_type' => $sourceType,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'external_url' => $externalUrl,
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', 'Materi belajar berhasil diunggah dan langsung tampil di halaman siswa!');
    }

    /**
     * Perbarui judul, keterangan, ringkasan, atau ganti file PDF materi.
     */
    public function updateMaterial(Request $request, Material $material): RedirectResponse
    {
        $hasBase64 = filled($request->input('material_file_base64'));

        $rules = [
            'title' => ['required', 'string', 'min:2', 'max:150'],
            'meta' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
        ];

        if ($request->hasFile('material_file') && ! $hasBase64) {
            $rules['material_file'] = ['nullable', 'file', 'mimes:pdf', 'max:10240'];
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Judul materi wajib diisi.',
            'material_file.mimes' => 'Format file materi harus berupa PDF (.pdf).',
            'material_file.max' => 'Ukuran file PDF maksimal 10 MB.',
        ]);

        $material->title = trim($validated['title']);
        $material->meta = filled($validated['meta'] ?? null)
            ? trim((string) $validated['meta'])
            : ($material->meta ?: 'Modul PDF · Layanan BK');
        $material->summary = filled($validated['summary'] ?? null)
            ? trim((string) $validated['summary'])
            : null;

        if ($request->hasFile('material_file') && $request->file('material_file')->isValid()) {
            if ($material->source_type === 'file' && filled($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $uploaded = $request->file('material_file');
            $material->source_type = 'file';
            $material->file_name = $uploaded->getClientOriginalName();
            $material->file_path = $uploaded->store('materials', 'public');
            $material->external_url = null;
        } elseif ($hasBase64) {
            [$newPath, $newName] = $this->storeBase64Pdf(
                (string) $request->input('material_file_base64'),
                (string) $request->input('material_file_name', 'materi.pdf')
            );
            if ($newPath === null) {
                return back()->withErrors(['material_file' => 'Berkas PDF pengganti tidak valid.']);
            }
            if ($material->source_type === 'file' && filled($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $material->source_type = 'file';
            $material->file_path = $newPath;
            $material->file_name = $newName;
            $material->external_url = null;
        }

        $material->save();

        return back()->with('success', "Materi \"{$material->title}\" berhasil diperbarui!");
    }

    /**
     * Simpan file PDF dari payload base64 (cadangan bila ukuran file > batas upload_max_filesize PHP).
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function storeBase64Pdf(string $dataUri, string $originalName): array
    {
        $raw = preg_replace('#^data:application/pdf;base64,#i', '', trim($dataUri));
        $binary = base64_decode((string) $raw, true);

        if ($binary === false || strlen($binary) < 8 || strlen($binary) > 10 * 1024 * 1024) {
            return [null, null];
        }

        if (! str_starts_with($binary, '%PDF-')) {
            return [null, null];
        }

        $path = 'materials/'.Str::random(40).'.pdf';
        Storage::disk('public')->put($path, $binary);

        $cleanName = basename(trim($originalName)) ?: 'materi.pdf';
        if (! str_ends_with(strtolower($cleanName), '.pdf')) {
            $cleanName .= '.pdf';
        }

        return [$path, Str::limit($cleanName, 200, '')];
    }

    /**
     * Hapus materi belajar beserta file PDF jika tersimpan di server.
     */
    public function destroyMaterial(Material $material): RedirectResponse
    {
        if ($material->source_type === 'file' && filled($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $title = $material->title;
        $material->delete();

        return back()->with('info', "Materi \"{$title}\" berhasil dihapus.");
    }

    /**
     * Tampilkan file PDF materi secara inline di browser (untuk iframe halaman web).
     */
    public function serveMaterialFile(Request $request, Material $material): BinaryFileResponse
    {
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $isStudent = StudentSession::current($request) !== null;

        abort_unless($isAdmin || $isStudent, 403, 'Silakan masuk terlebih dahulu untuk mengakses materi.');
        abort_unless(
            $material->source_type === 'file'
            && filled($material->file_path)
            && Storage::disk('public')->exists($material->file_path),
            404,
            'Berkas materi tidak ditemukan.'
        );

        $fullPath = Storage::disk('public')->path($material->file_path);
        $safeName = preg_replace('/[^a-zA-Z0-9._ -]/', '', (string) ($material->file_name ?: 'materi.pdf')) ?: 'materi.pdf';

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$safeName.'"',
        ]);
    }

    /**
     * Halaman kelola akun Guru BK (Admin) yang diizinkan masuk lewat Google.
     */
    public function admins(): View
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->get();

        $primaryAdminEmail = Str::lower(trim((string) config('app.jubema.admin_email')));
        $envAdminEmails = array_values(array_unique(array_filter(array_map(
            static fn ($email) => Str::lower(trim((string) $email)),
            array_merge([$primaryAdminEmail], (array) config('app.jubema.admin_emails', []))
        ))));

        return view('admin.akun-guru', [
            'admins' => $admins,
            'primaryAdminEmail' => $primaryAdminEmail,
            'envAdminEmails' => $envAdminEmails,
        ]);
    }

    /**
     * Tambahkan akun Guru BK (Admin) baru berdasarkan alamat email Google.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->where('role', 'admin'),
            ],
        ], [
            'name.required' => 'Nama Guru BK wajib diisi.',
            'name.min' => 'Nama Guru BK minimal 2 karakter.',
            'email.required' => 'Alamat email Google wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar sebagai akun Guru BK.',
        ]);

        $user = User::firstOrNew(['email' => $validated['email']]);
        $user->name = $validated['name'];
        $user->role = 'admin';

        if (! $user->exists || blank($user->password)) {
            $user->password = Str::random(40);
        }

        $user->save();

        // Jika email ini sebelumnya pernah dicabut dari allowlist .env, aktifkan kembali.
        $revoked = array_values(array_filter(
            GoogleAuthController::revokedEnvAdminEmails(),
            static fn (string $item) => $item !== Str::lower($user->email)
        ));
        Setting::put('revoked_env_admin_emails', json_encode($revoked));

        return back()->with('success', "Akun Guru BK {$user->name} ({$user->email}) berhasil ditambahkan.");
    }

    /**
     * Cabut akses akun Guru BK atau keluar dari admin (termasuk Admin Utama),
     * selama masih ada minimal satu akun Guru BK lain yang aktif.
     */
    public function destroyAdmin(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isAdmin(), 404);

        $hasOtherActiveAdmin = User::where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->exists();

        if (! $hasOtherActiveAdmin) {
            return back()->with('error', 'Tidak dapat keluar atau menghapus akses karena minimal harus tersisa satu akun Guru BK yang aktif.');
        }

        $email = Str::lower(trim((string) $user->email));
        $envAdminEmails = array_filter(array_map(
            static fn ($item) => Str::lower(trim((string) $item)),
            array_merge(
                [(string) config('app.jubema.admin_email')],
                (array) config('app.jubema.admin_emails', [])
            )
        ));

        if (in_array($email, $envAdminEmails, true)) {
            $revoked = array_values(array_unique([
                ...GoogleAuthController::revokedEnvAdminEmails(),
                $email,
            ]));
            Setting::put('revoked_env_admin_emails', json_encode($revoked));
        }

        $isSelf = $user->id === Auth::id();
        $user->delete();

        if ($isSelf) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('info', 'Anda telah keluar dari akses Admin Guru BK. Pengelolaan dilanjutkan oleh admin aktif lainnya.');
        }

        return back()->with('success', "Akses Guru BK untuk {$email} berhasil dihapus.");
    }

    private function render(string $page): View
    {
        $tasks = Task::with(['student', 'subtasks'])->latest()->get();
        $reflections = Reflection::with('student')->latest()->get();
        $consultations = Consultation::with('student')->latest()->get();

        return view('admin.'.$page, [
            'page' => $page,
            'tasks' => $tasks,
            'reflections' => $reflections,
            'consultations' => $consultations,
            'totalTasks' => $tasks->count(),
            'doneTasks' => $tasks->where('completed', true)->count(),
            'sharedReflections' => $reflections->where('shared_with_bk', true),
        ]);
    }

    /**
     * Atur, ubah, atau batalkan jadwal konsultasi (tanggal, waktu, tempat, catatan).
     */
    public function markConsultation(Request $request, Consultation $consultation): RedirectResponse
    {
        if ($request->input('action') === 'cancel') {
            $consultation->status = 'menunggu_jadwal_bk';
            $consultation->scheduled_at = null;
            $consultation->scheduled_location = null;
            $consultation->scheduled_note = null;
            $consultation->save();

            return back()->with('info', 'Jadwal konsultasi dibatalkan.');
        }

        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'scheduled_location' => ['required', 'string', 'max:150'],
            'scheduled_note' => ['nullable', 'string', 'max:300'],
        ], [
            'scheduled_date.required' => 'Tanggal konsultasi wajib diisi.',
            'scheduled_date.date' => 'Format tanggal konsultasi tidak valid.',
            'scheduled_time.required' => 'Jam konsultasi wajib diisi.',
            'scheduled_time.date_format' => 'Format jam konsultasi harus HH:MM.',
            'scheduled_location.required' => 'Tempat atau tautan konsultasi wajib diisi.',
        ]);

        $consultation->status = 'sudah_dijadwalkan';
        $consultation->scheduled_at = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['scheduled_date'].' '.$validated['scheduled_time']
        );
        $consultation->scheduled_location = trim($validated['scheduled_location']);
        $consultation->scheduled_note = filled($validated['scheduled_note'] ?? null)
            ? trim((string) $validated['scheduled_note'])
            : null;
        $consultation->save();

        return back()->with('success', 'Jadwal konsultasi berhasil disimpan!');
    }

    /**
     * Hapus respons formulir konsultasi siswa.
     */
    public function destroyConsultation(Consultation $consultation): RedirectResponse
    {
        $consultation->delete();

        return back()->with('info', 'Respons formulir konsultasi berhasil dihapus.');
    }

    /**
     * Simpan tautan Google Workspace (Form, Spreadsheet, Drive).
     */
    public function saveLinks(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_form_url' => ['nullable', 'url', 'max:500'],
            'google_sheet_url' => ['required', 'url', 'max:500'],
            'google_drive_url' => ['required', 'url', 'max:500'],
        ], [
            'google_form_url.url' => 'URL Google Form tidak valid.',
            'google_sheet_url.required' => 'URL Google Spreadsheet wajib diisi.',
            'google_sheet_url.url' => 'URL Google Spreadsheet tidak valid.',
            'google_drive_url.required' => 'URL Folder Google Drive wajib diisi.',
            'google_drive_url.url' => 'URL Folder Google Drive tidak valid.',
        ]);

        if (filled($validated['google_form_url'] ?? null)) {
            Setting::put('google_form_url', $validated['google_form_url']);
        }
        Setting::put('google_sheet_url', $validated['google_sheet_url']);
        Setting::put('google_drive_url', $validated['google_drive_url']);

        return back()->with('success', 'Tautan Google Workspace berhasil diperbarui!');
    }

    /**
     * Ekspor seluruh data aktivitas siswa ke Excel (.xlsx) multi-sheet yang rapi dan mudah dibaca.
     */
    public function exportSpreadsheet(): BinaryFileResponse
    {
        $generatedAt = now()->translatedFormat('d F Y, H:i').' WIB';
        $filename = 'REKAP_KELAS_JUBEMA_'.now()->format('Ymd_His').'.xlsx';

        $taskRows = [];
        Task::with(['student', 'subtasks'])->latest()->get()->values()->each(function (Task $t, int $idx) use (&$taskRows) {
            $doneSteps = $t->subtasks->where('done', true)->count();
            $totalSteps = $t->subtasks->count();
            $stepsSummary = $totalSteps > 0 ? "{$doneSteps}/{$totalSteps} langkah" : '-';

            $taskRows[] = [
                $idx + 1,
                $t->student?->name ?? '-',
                $t->student?->kelas ?? '-',
                $t->day,
                $t->subject,
                $t->title,
                $t->priority,
                $t->time,
                $stepsSummary,
                $t->completed ? 'Selesai' : 'Berproses',
            ];
        });

        $reflectionRows = [];
        Reflection::with('student')->latest()->get()->values()->each(function (Reflection $r, int $idx) use (&$reflectionRows) {
            $reflectionRows[] = [
                $idx + 1,
                $r->student?->name ?? '-',
                $r->student?->kelas ?? '-',
                $r->week,
                $r->mood,
                $r->good,
                $r->obstacle,
                $r->plan,
                $r->shared_with_bk ? 'Dibagikan ke BK' : 'Privat',
            ];
        });

        $consultationRows = [];
        Consultation::with('student')->latest()->get()->values()->each(function (Consultation $k, int $idx) use (&$consultationRows) {
            $details = [];
            foreach ($k->formattedAnswers() as $ans) {
                $details[] = "• {$ans['label']}: {$ans['value']}";
            }

            $consultationRows[] = [
                $idx + 1,
                $k->created_at?->translatedFormat('d M Y, H:i') ?? '-',
                $k->displayName(),
                $k->displayKelas(),
                $k->email ?: '-',
                $k->displayTopic(),
                implode("\n", $details) ?: '-',
                $k->statusLabel(),
                $k->scheduled_at ? $k->scheduled_at->translatedFormat('d M Y, H:i').' WIB' : '-',
                $k->scheduled_location ?: '-',
            ];
        });

        $builder = (new SimpleXlsxBuilder)
            ->addSheet(
                'Rencana Belajar',
                'REKAP RENCANA BELAJAR SISWA — JUBEMA',
                "Layanan Bimbingan dan Konseling SMP · Diekspor pada {$generatedAt}",
                ['No', 'Nama Siswa', 'Kelas', 'Hari', 'Mata Pelajaran', 'Target Tugas', 'Prioritas', 'Waktu', 'Progres Langkah', 'Status'],
                [6, 22, 13, 12, 20, 36, 22, 22, 16, 15],
                $taskRows
            )
            ->addSheet(
                'Refleksi Siswa',
                'REKAP REFLEKSI MINGGUAN SISWA — JUBEMA',
                "Layanan Bimbingan dan Konseling SMP · Diekspor pada {$generatedAt}",
                ['No', 'Nama Siswa', 'Kelas', 'Periode Minggu', 'Perasaan', 'Berhasil Dilakukan', 'Hambatan', 'Rencana Perbaikan', 'Izin Guru BK'],
                [6, 22, 13, 22, 18, 34, 34, 34, 18],
                $reflectionRows
            )
            ->addSheet(
                'Konsultasi BK',
                'REKAP FORMULIR KONSULTASI BK — JUBEMA',
                "Layanan Bimbingan dan Konseling SMP · Diekspor pada {$generatedAt}",
                ['No', 'Tanggal Masuk', 'Nama Siswa', 'Kelas', 'Email', 'Topik Utama', 'Ringkasan Jawaban', 'Status', 'Jadwal Konsultasi', 'Tempat / Tautan'],
                [6, 18, 22, 13, 24, 30, 44, 20, 22, 26],
                $consultationRows
            );

        $tmpPath = $builder->buildToTempFile();

        return response()
            ->download($tmpPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    /**
     * Ekspor tugas milik satu siswa (dipakai tombol "Sinkron / Ekspor Spreadsheet").
     */
    public function exportSiswaTasks(Request $request): StreamedResponse
    {
        $student = StudentSession::current($request);

        abort_if($student === null, 403, 'Sesi siswa tidak ditemukan.');

        $filename = 'JUBEMA_Spreadsheet_'.preg_replace('/[\\\\\/:*?"<>|]/', '-', $student->name).'.csv';

        return response()->streamDownload(function () use ($student) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Nama Panggilan Siswa', 'Kelas', 'Hari',
                'Mata Pelajaran', 'Target Tugas', 'Prioritas', 'Estimasi Waktu', 'Status',
            ]);

            $student->tasks()->latest()->get()->each(function (Task $t) use ($out) {
                fputcsv($out, [
                    $student->name,
                    $student->kelas,
                    $t->day,
                    $t->subject,
                    $t->title,
                    $t->priority,
                    $t->time,
                    $t->completed ? 'Selesai' : 'Berproses',
                ]);
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
