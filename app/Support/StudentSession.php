<?php

namespace App\Support;

use App\Models\Student;
use Illuminate\Http\Request;

/**
 * Helper sesi siswa. Siswa tidak memakai kata sandi: cukup memilih
 * Nama Panggilan + Kelas, lalu id-nya disimpan di session.
 */
class StudentSession
{
    public const KEY = 'student_id';

    public static function put(Request $request, Student $student): void
    {
        $request->session()->put(self::KEY, $student->id);
        $request->session()->put('student_name', $student->name);
        $request->session()->put('student_kelas', $student->kelas);
    }

    public static function current(Request $request): ?Student
    {
        $id = $request->session()->get(self::KEY);

        return $id ? Student::find($id) : null;
    }

    public static function forget(Request $request): void
    {
        $request->session()->forget([self::KEY, 'student_name', 'student_kelas']);
    }
}
