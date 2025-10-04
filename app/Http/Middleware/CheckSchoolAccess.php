<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\School;

class CheckSchoolAccess
{

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $school = $request->route('school');

        if ($user->role === 'SuperAdmin' || $user->role === 'AdminMonitor' || $user->role === 'Pengawas') {
            return $next($request);
        }

        $school = $request->route('school') ?: ($request->school_id ? School::find($request->school_id) : $user->school);
        if (!$school || $user->school_id !== $school->id) {
            abort(403, 'Unauthorized access to this school.');
        }

        return $next($request);
    }
}
