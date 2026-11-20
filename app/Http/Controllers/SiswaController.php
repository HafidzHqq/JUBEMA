<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Subtask;
use App\Models\Task;
use App\Support\ConsultationFormConfig;
use App\Support\StudentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Halaman siswa: beranda, perencana, ceklis, refleksi, materi, RPL, konsultasi.
 */
class SiswaController extends Controller
{
    private const VIEWS = [
        'beranda' => 'siswa.beranda',
        'perencana' => 'siswa.perencana',
        'ceklis' => 'siswa.ceklis',
        'refleksi' => 'siswa.refleksi',
        'materi' => 'siswa.materi',
        'rpl' => 'siswa.rpl',
        'konsultasi' => 'siswa.konsultasi',
    ];

    public function index(): View
    {
        return $this->beranda();
    }

    public function beranda(): View
    {
        return $this->render('beranda');
    }

    public function perencana(): View
    {
        return $this->render('perencana');
    }

    public function ceklis(): View
    {
        return $this->render('ceklis');
    }

    public function refleksi(): View
    {
        return $this->render('refleksi');
    }

    public function materi(): View
    {
        return $this->render('materi');
    }

    public function rpl(): View
    {
        return $this->render('rpl');
    }

    public function konsultasi(): View
    {
        return $this->render('konsultasi');
    }

    /**
     * Render satu halaman siswa dengan data yang dibutuhkan bersama
     * (daftar tugas, statistik kemajuan, riwayat refleksi & konsultasi).
     */
    private function render(string $page): View
    {
        $student = StudentSession::current(request());

        abort_if($student === null, 403, 'Sesi siswa tidak ditemukan.');

        $tasks = Task::with('subtasks')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $reflections = $student->reflections()->latest()->get();
        $consultations = $student->consultations()->latest()->get();

        $total = $tasks->count();
        $done = $tasks->where('completed', true)->count();

        return view(self::VIEWS[$page], [
            'page' => $page,
            'student' => $student,
            'tasks' => $tasks,
            'reflections' => $reflections,
            'consultations' => $consultations,
            'formConfig' => $page === 'konsultasi' ? ConsultationFormConfig::get() : null,
            'totalTasks' => $total,
            'doneTasks' => $done,
            'progressPercent' => $total === 0 ? 0 : (int) round(($done / $total) * 100),
        ]);
    }

    // ------------------------------------------------------------------
    // CRUD Tugas & Langkah Kecil (Pemecah Tugas)
    // ------------------------------------------------------------------

    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:60'],
            'day' => ['required', 'in:'.implode(',', Task::DAYS)],
            'priority' => ['required', 'in:'.implode(',', Task::PRIORITIES)],
            'title' => ['required', 'string', 'max:120'],
            'time' => ['required', 'string', 'max:60'],
            'subtasks' => ['array', 'max:3'],
            'subtasks.*' => ['nullable', 'string', 'max:80'],
        ], [
            'subject.required' => 'Mata pelajaran wajib diisi.',
            'title.required' => 'Target tugas wajib diisi.',
            'time.required' => 'Estimasi waktu wajib diisi.',
        ]);

        $student = StudentSession::current($request);

        $task = Task::create([
            'student_id' => $student->id,
            'day' => $validated['day'],
            'subject' => $validated['subject'],
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'time' => $validated['time'],
            'completed' => false,
        ]);

        $defaults = [
            'Siapkan alat tulis & materi',
            'Kerjakan bagian inti tugas',
            'Periksa kembali hasil kerja',
        ];

        foreach ([0, 1, 2] as $i) {
            Subtask::create([
                'task_id' => $task->id,
                'text' => $validated['subtasks'][$i] ?? $defaults[$i],
                'done' => false,
                'position' => $i,
            ]);
        }

        return back()->with('success', 'Rencana belajar & pemecah tugas tersimpan ke spreadsheet MySQL!');
    }

    public function toggleTask(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->student_id === StudentSession::current($request)->id, 403);

        $task->completed = ! $task->completed;
        $task->save();

        return back()->with(
            'success',
            $task->completed
                ? 'Hebat! Satu rencana belajar selesai dikerjakan.'
                : 'Status tugas dikembalikan.'
        );
    }

    public function toggleSubtask(Request $request, Task $task, Subtask $subtask): RedirectResponse
    {
        abort_unless($task->student_id === StudentSession::current($request)->id, 403);
        abort_unless($subtask->task_id === $task->id, 403);

        $subtask->done = ! $subtask->done;
        $subtask->save();

        // Sinkron dua arah: batal centang juga membatalkan status tugas selesai.
        $task->syncCompletionFromSubtasks();

        return back();
    }

    public function deleteTask(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->student_id === StudentSession::current($request)->id, 403);

        $task->delete();

        return back()->with('info', 'Rencana tugas dihapus.');
    }

    // ------------------------------------------------------------------
    // Refleksi Mingguan
    // ------------------------------------------------------------------

    public function storeReflection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'week' => ['required', 'string', 'max:60'],
            'mood' => ['required', 'string', 'max:60'],
            'good' => ['required', 'string', 'max:500'],
            'obstacle' => ['required', 'string', 'max:500'],
            'plan' => ['required', 'string', 'max:500'],
            'share_bk' => ['boolean'],
        ], [
            'week.required' => 'Periode minggu refleksi wajib diisi.',
        ]);

        $student = StudentSession::current($request);

        $student->reflections()->create([
            'week' => $validated['week'],
            'mood' => $validated['mood'],
            'good' => $validated['good'],
            'obstacle' => $validated['obstacle'],
            'plan' => $validated['plan'],
            'shared_with_bk' => $request->boolean('share_bk'),
        ]);

        return back()->with('success', 'Refleksi mingguan tersimpan!');
    }

    // ------------------------------------------------------------------
    // Permintaan Konsultasi
    // ------------------------------------------------------------------

    public function storeConsultation(Request $request): RedirectResponse
    {
        $student = StudentSession::current($request);
        $formConfig = ConsultationFormConfig::get();

        $allowedDifficulties = $formConfig['q1_options'];
        if ($formConfig['q1_allow_other']) {
            $allowedDifficulties[] = '__other__';
        }

        $validated = $request->validate([
            'record_email' => ['nullable', 'boolean'],
            'difficulty' => ['required', Rule::in($allowedDifficulties)],
            'difficulty_other' => ['nullable', 'required_if:difficulty,__other__', 'string', 'max:200'],
            'focus_level' => ['required', 'integer', 'between:1,4'],
            'topic' => ['required', Rule::in($formConfig['q3_options'])],
            'bring_journal' => ['required', Rule::in($formConfig['q4_options'])],
            'mode' => ['required', Rule::in($formConfig['q5_options'])],
            'consultation_method' => ['required', Rule::in($formConfig['q6_options'])],
            'message' => ['nullable', 'string', 'max:600'],
        ], [
            'difficulty.required' => 'Pilih jawaban untuk pertanyaan 1.',
            'difficulty_other.required_if' => 'Tuliskan jawaban lainnya pada pertanyaan 1.',
            'focus_level.required' => 'Pilih skala tingkat fokus minggu ini (1–4).',
            'topic.required' => 'Pilih jawaban untuk pertanyaan 3.',
            'bring_journal.required' => 'Pilih jawaban untuk pertanyaan 4.',
            'mode.required' => 'Pilih jawaban untuk pertanyaan 5.',
            'consultation_method.required' => 'Pilih cara pendampingan yang paling nyaman untukmu.',
        ]);

        $difficulty = $validated['difficulty'] === '__other__'
            ? trim((string) $validated['difficulty_other'])
            : $validated['difficulty'];

        $email = $request->boolean('record_email') && filled($student->google_email)
            ? $student->google_email
            : null;

        $student->consultations()->create([
            'respondent_name' => $student->name,
            'respondent_kelas' => str_replace('Kelas ', '', (string) $student->kelas),
            'email' => $email,
            'difficulty' => $difficulty,
            'focus_level' => (int) $validated['focus_level'],
            'topic' => $validated['topic'],
            'bring_journal' => $validated['bring_journal'],
            'mode' => $validated['mode'],
            'consultation_method' => $validated['consultation_method'],
            'message' => filled($validated['message'] ?? null) ? trim((string) $validated['message']) : null,
            'status' => 'terkirim',
        ]);

        return back()->with('success', 'Formulir konsultasi berhasil dikirim ke Guru BK!');
    }
}
