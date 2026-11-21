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
            $rawStep = isset($validated['subtasks'][$i]) ? trim((string) $validated['subtasks'][$i]) : '';

            Subtask::create([
                'task_id' => $task->id,
                'text' => $rawStep !== '' ? $rawStep : $defaults[$i],
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
        $questions = $formConfig['questions'];

        $rules = [
            'record_email' => ['nullable', 'boolean'],
            'answers' => ['array'],
            'answers_other' => ['array'],
        ];
        $messages = [];

        foreach ($questions as $index => $q) {
            $id = $q['id'];
            $num = $index + 1;
            $key = "answers.{$id}";
            $requiredRule = ! empty($q['required']) ? 'required' : 'nullable';

            if ($q['type'] === 'radio') {
                $allowed = $q['options'] ?? [];
                if (! empty($q['allow_other'])) {
                    $allowed[] = '__other__';
                }
                $rules[$key] = [$requiredRule, Rule::in($allowed)];
                $messages["{$key}.required"] = "Pilih jawaban untuk pertanyaan {$num}.";

                if (! empty($q['allow_other'])) {
                    $otherKey = "answers_other.{$id}";
                    $rules[$otherKey] = ['nullable', "required_if:{$key},__other__", 'string', 'max:200'];
                    $messages["{$otherKey}.required_if"] = "Tuliskan jawaban lainnya pada pertanyaan {$num}.";
                }
            } elseif ($q['type'] === 'scale') {
                $rules[$key] = [$requiredRule, 'integer', 'between:1,4'];
                $messages["{$key}.required"] = "Pilih skala (1–4) untuk pertanyaan {$num}.";
            } elseif ($q['type'] === 'text') {
                $rules[$key] = [$requiredRule, 'string', 'max:250'];
                $messages["{$key}.required"] = "Pertanyaan {$num} wajib diisi.";
            } else {
                $rules[$key] = [$requiredRule, 'string', 'max:1000'];
                $messages["{$key}.required"] = "Pertanyaan {$num} wajib diisi.";
            }
        }

        $validated = $request->validate($rules, $messages);

        $rawAnswers = $validated['answers'] ?? [];
        $rawOthers = $validated['answers_other'] ?? [];
        $structuredAnswers = [];
        $byId = [];

        foreach ($questions as $q) {
            $id = $q['id'];
            $val = $rawAnswers[$id] ?? null;

            if ($q['type'] === 'radio' && $val === '__other__') {
                $val = trim((string) ($rawOthers[$id] ?? ''));
            } elseif ($val !== null) {
                $val = trim((string) $val);
            }

            if ($val === '' || $val === null) {
                continue;
            }

            $displayValue = $q['type'] === 'scale' ? "{$val} / 4" : $val;

            $structuredAnswers[] = [
                'id' => $id,
                'label' => $q['label'],
                'type' => $q['type'],
                'value' => $displayValue,
            ];
            $byId[$id] = $val;
        }

        $email = $request->boolean('record_email') && filled($student->google_email)
            ? $student->google_email
            : null;

        $firstTextAnswer = $structuredAnswers[0]['value'] ?? 'Permintaan Konsultasi';

        $student->consultations()->create([
            'respondent_name' => $student->name,
            'respondent_kelas' => str_replace('Kelas ', '', (string) $student->kelas),
            'email' => $email,
            'difficulty' => isset($byId['difficulty']) ? mb_substr($byId['difficulty'], 0, 255) : null,
            'focus_level' => isset($byId['focus_level']) && is_numeric($byId['focus_level']) ? (int) $byId['focus_level'] : null,
            'topic' => mb_substr((string) ($byId['topic'] ?? $firstTextAnswer), 0, 200),
            'bring_journal' => isset($byId['bring_journal']) ? mb_substr($byId['bring_journal'], 0, 120) : null,
            'mode' => isset($byId['mode']) ? mb_substr($byId['mode'], 0, 120) : null,
            'consultation_method' => isset($byId['consultation_method']) ? mb_substr($byId['consultation_method'], 0, 120) : null,
            'message' => $byId['message'] ?? null,
            'answers' => $structuredAnswers,
            'status' => 'terkirim',
        ]);

        return back()->with('success', 'Formulir konsultasi berhasil dikirim ke Guru BK!');
    }
}
