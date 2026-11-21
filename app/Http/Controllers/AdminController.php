<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Reflection;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Support\ConsultationFormConfig;
use App\Support\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
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
        return $this->render('integrasi');
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

        return back()->with('success', "Akun Guru BK {$user->name} ({$user->email}) berhasil ditambahkan.");
    }

    /**
     * Cabut akses akun Guru BK (Admin) lain.
     */
    public function destroyAdmin(User $user): RedirectResponse
    {
        abort_unless($user->isAdmin(), 404);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $envAdminEmails = array_filter(array_map(
            static fn ($email) => Str::lower(trim((string) $email)),
            array_merge(
                [(string) config('app.jubema.admin_email')],
                (array) config('app.jubema.admin_emails', [])
            )
        ));

        if (in_array(Str::lower($user->email), $envAdminEmails, true)) {
            return back()->with('error', 'Akun Guru BK yang ditetapkan di konfigurasi server (.env) tidak dapat dihapus dari halaman ini.');
        }

        $email = $user->email;
        $user->delete();

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
     * Ekspor seluruh data aktivitas siswa ke CSV (bisa dibuka di Excel / Google Sheets).
     */
    public function exportSpreadsheet(): StreamedResponse
    {
        $filename = 'MASTER_SPREADSHEET_JUBEMA_ADMIN_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            // BOM agar karakter Indonesia terbaca rapi di Excel.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'KATEGORI', 'NAMA PANGGILAN SISWA', 'KELAS',
                'HARI/PERIODE', 'JUDUL/TOPIK', 'DETAIL/HAMBATAN', 'STATUS/RENCANA',
            ]);

            Task::with('student')->latest()->get()->each(function (Task $t) use ($out) {
                fputcsv($out, [
                    'Rencana Belajar',
                    $t->student?->name,
                    $t->student?->kelas,
                    $t->day,
                    "{$t->subject} - {$t->title}",
                    $t->priority,
                    $t->completed ? 'Selesai' : 'Berproses',
                ]);
            });

            Reflection::with('student')->latest()->get()->each(function (Reflection $r) use ($out) {
                fputcsv($out, [
                    'Refleksi',
                    $r->student?->name,
                    $r->student?->kelas,
                    $r->week,
                    $r->mood,
                    "Baik: {$r->good} | Hambatan: {$r->obstacle} | Rencana: {$r->plan}",
                    $r->shared_with_bk ? 'Dibagikan ke BK' : 'Privat',
                ]);
            });

            Consultation::with('student')->latest()->get()->each(function (Consultation $k) use ($out) {
                $details = [];
                foreach ($k->formattedAnswers() as $ans) {
                    $details[] = "{$ans['label']}: {$ans['value']}";
                }
                if ($k->scheduled_at) {
                    $details[] = 'Jadwal: '.$k->scheduled_at->translatedFormat('d M Y, H:i');
                }
                if (filled($k->scheduled_location)) {
                    $details[] = "Tempat: {$k->scheduled_location}";
                }

                fputcsv($out, [
                    'Konsultasi',
                    $k->displayName(),
                    $k->displayKelas(),
                    $k->created_at?->translatedFormat('d M Y'),
                    $k->displayTopic(),
                    implode(' | ', $details),
                    $k->statusLabel(),
                ]);
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
