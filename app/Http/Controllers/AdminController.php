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
        $cleanList = static function (mixed $values): array {
            if (! is_array($values)) {
                return [];
            }

            return array_values(array_unique(array_filter(array_map(
                static fn ($item) => trim((string) $item),
                $values
            ), static fn (string $item) => $item !== '')));
        };

        $request->merge([
            'q1_options' => $cleanList($request->input('q1_options', [])),
            'q3_options' => $cleanList($request->input('q3_options', [])),
            'q4_options' => $cleanList($request->input('q4_options', [])),
            'q5_options' => $cleanList($request->input('q5_options', [])),
            'q6_options' => $cleanList($request->input('q6_options', [])),
        ]);

        $validated = $request->validate([
            'intro' => ['required', 'string', 'max:1000'],
            'q1_label' => ['required', 'string', 'max:250'],
            'q1_options' => ['required', 'array', 'min:1', 'max:15'],
            'q1_options.*' => ['required', 'string', 'max:200'],
            'q1_allow_other' => ['nullable', 'boolean'],
            'q2_label' => ['required', 'string', 'max:250'],
            'q2_min_label' => ['required', 'string', 'max:100'],
            'q2_max_label' => ['required', 'string', 'max:100'],
            'q3_label' => ['required', 'string', 'max:250'],
            'q3_options' => ['required', 'array', 'min:1', 'max:15'],
            'q3_options.*' => ['required', 'string', 'max:200'],
            'q4_label' => ['required', 'string', 'max:250'],
            'q4_options' => ['required', 'array', 'min:1', 'max:10'],
            'q4_options.*' => ['required', 'string', 'max:120'],
            'q5_label' => ['required', 'string', 'max:250'],
            'q5_options' => ['required', 'array', 'min:1', 'max:10'],
            'q5_options.*' => ['required', 'string', 'max:120'],
            'q6_label' => ['required', 'string', 'max:250'],
            'q6_options' => ['required', 'array', 'min:1', 'max:10'],
            'q6_options.*' => ['required', 'string', 'max:120'],
            'note_label' => ['required', 'string', 'max:150'],
            'note_placeholder' => ['required', 'string', 'max:250'],
        ], [
            'intro.required' => 'Teks pengantar formulir wajib diisi.',
            'q1_label.required' => 'Pertanyaan 1 wajib diisi.',
            'q1_options.min' => 'Pertanyaan 1 minimal memiliki 1 pilihan.',
            'q2_label.required' => 'Pertanyaan 2 wajib diisi.',
            'q3_label.required' => 'Pertanyaan 3 wajib diisi.',
            'q3_options.min' => 'Pertanyaan 3 minimal memiliki 1 pilihan.',
            'q4_label.required' => 'Pertanyaan 4 wajib diisi.',
            'q4_options.min' => 'Pertanyaan 4 minimal memiliki 1 pilihan.',
            'q5_label.required' => 'Pertanyaan 5 wajib diisi.',
            'q5_options.min' => 'Pertanyaan 5 minimal memiliki 1 pilihan.',
            'q6_label.required' => 'Pertanyaan 6 wajib diisi.',
            'q6_options.min' => 'Pertanyaan 6 minimal memiliki 1 pilihan.',
        ]);

        ConsultationFormConfig::save([
            ...$validated,
            'q1_allow_other' => $request->boolean('q1_allow_other'),
        ]);

        return back()->with('success', 'Pertanyaan Formulir Konsultasi berhasil diperbarui!');
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
     * Tandai permintaan konsultasi "Sudah Dijadwalkan".
     */
    public function markConsultation(Request $request, Consultation $consultation): RedirectResponse
    {
        $consultation->status = $consultation->status === 'sudah_dijadwalkan'
            ? 'menunggu_jadwal_bk'
            : 'sudah_dijadwalkan';

        $consultation->scheduled_at = $consultation->status === 'sudah_dijadwalkan'
            ? now()
            : null;

        $consultation->save();

        return back()->with('success', 'Status konsultasi diperbarui.');
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
                $details = array_filter([
                    $k->difficulty ? "Kesulitan: {$k->difficulty}" : null,
                    $k->focus_level ? "Fokus: {$k->focus_level}/4" : null,
                    $k->bring_journal ? "Catatan jurnal: {$k->bring_journal}" : null,
                    $k->mode ? "Waktu: {$k->mode}" : null,
                    $k->consultation_method ? "Cara pendampingan: {$k->consultation_method}" : null,
                    $k->message ? "Catatan: {$k->message}" : null,
                ]);

                fputcsv($out, [
                    'Konsultasi',
                    $k->displayName(),
                    $k->displayKelas(),
                    $k->created_at?->translatedFormat('d M Y'),
                    $k->topic,
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
