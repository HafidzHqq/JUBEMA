<?php

namespace App\Http\Middleware;

use App\Support\StudentSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSiswa
{
    public function handle(Request $request, Closure $next): Response
    {
        $student = StudentSession::current($request);

        // Sesi bisa saja masih ada padahal baris siswa sudah dihapus
        // (mis. database di-reset). Bersihkan sesinya agar tidak error.
        if ($student === null) {
            StudentSession::forget($request);

            return redirect()->route('login')
                ->with('error', 'Silakan masuk sebagai siswa terlebih dahulu.');
        }

        $request->attributes->set('student', $student);

        return $next($request);
    }
}
